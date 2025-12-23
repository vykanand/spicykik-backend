<?php 
session_start();
include('./config.php');

$module = isset($_GET['module']) ? $_GET['module'] : '';

if (empty($module)) {
    echo "No module specified";
    exit;
}

// Detect which optional columns exist in navigation (field_types, field_required, field_options)
$hasFieldTypes = false;
$hasFieldRequired = false;
$hasFieldOptions = false;
$colRes = mysqli_query($db, "SHOW COLUMNS FROM navigation LIKE 'field_types'");
if ($colRes && mysqli_num_rows($colRes) > 0) $hasFieldTypes = true;
$colRes = mysqli_query($db, "SHOW COLUMNS FROM navigation LIKE 'field_required'");
if ($colRes && mysqli_num_rows($colRes) > 0) $hasFieldRequired = true;
$colRes = mysqli_query($db, "SHOW COLUMNS FROM navigation LIKE 'field_options'");
if ($colRes && mysqli_num_rows($colRes) > 0) $hasFieldOptions = true;

// Build select list based on available columns
$selectCols = array('nav', 'urn', 'plugin');
if ($hasFieldTypes) $selectCols[] = 'field_types';
if ($hasFieldRequired) $selectCols[] = 'field_required';
if ($hasFieldOptions) $selectCols[] = 'field_options';

$sql = 'SELECT ' . implode(', ', $selectCols) . " FROM navigation WHERE nav = ?";
$stmt = $db->prepare($sql);
if ($stmt === false) {
    // fallback: try selecting only nav, urn, plugin
    $stmt = $db->prepare("SELECT nav, urn, plugin FROM navigation WHERE nav = ?");
}
$stmt->bind_param("s", $module);
$stmt->execute();
$result = $stmt->get_result();
$moduleData = $result->fetch_assoc();
$stmt->close();

if (!$moduleData) {
    echo "Module not found";
    exit;
}

// Get existing plugin mappings
$pluginMappings = array();
if (!empty($moduleData['plugin'])) {
    $pluginMappings = json_decode($moduleData['plugin'], true);
    if (!is_array($pluginMappings)) {
        $pluginMappings = array();
    }
}

// Get existing field type mappings
$fieldTypeMappings = array();
if (!empty($moduleData['field_types'])) {
    $fieldTypeMappings = json_decode($moduleData['field_types'], true);
    if (!is_array($fieldTypeMappings)) {
        $fieldTypeMappings = array();
    }
}

// Get existing field required mappings
$fieldRequiredMappings = array();
if (!empty($moduleData['field_required'])) {
    $fieldRequiredMappings = json_decode($moduleData['field_required'], true);
    if (!is_array($fieldRequiredMappings)) {
        $fieldRequiredMappings = array();
    }
}

// Get existing field options mappings
$fieldOptionsMappings = array();
if (!empty($moduleData['field_options'])) {
    $fieldOptionsMappings = json_decode($moduleData['field_options'], true);
    if (!is_array($fieldOptionsMappings)) {
        $fieldOptionsMappings = array();
    }
}

// Get table name from urn
$urnParts = explode('/', $moduleData['urn']);
$tableName = $urnParts[0];

// Get table columns
$columns = array();
$sql = "SHOW COLUMNS FROM `$tableName`";
$result = mysqli_query($db, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $columnName = $row['Field'];
        // Exclude system columns
        if (!in_array($columnName, array('id', 'role', 'created_at', 'updated_at'))) {
            $columns[] = $columnName;
        }
    }
}

// Get available plugins
$plugins = array();
$pluginDir = './plugins/';
if ($handle = opendir($pluginDir)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && is_dir($pluginDir . $entry)) {
            $plugins[] = $entry;
        }
    }
    closedir($handle);
}
sort($plugins);

// Handle AJAX POST actions: save mappings, add_column, drop_column
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // Temporary debug logging: record POST payloads and responses
    // Writes to both a file and php://stdout/php://stderr so container logs capture entries
    function _pm_log($data) {
        $file = __DIR__ . '/pluginmap-debug.log';
        $entry = '[' . date('Y-m-d H:i:s') . '] ' . json_encode($data, JSON_UNESCAPED_SLASHES) . "\n";
        // write to local debug file (if writable)
        @file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
        // write to container stdout/stderr so Docker / cloud logging captures it
        @file_put_contents('php://stdout', $entry, FILE_APPEND);
        @file_put_contents('php://stderr', $entry, FILE_APPEND);
        // also send to PHP error log
        @error_log(trim($entry));
    }
    try { _pm_log(array('event' => 'post_received', 'action' => $action, 'post' => $_POST)); } catch(Exception $e) {}

    if ($action === 'save') {
        $mappings = isset($_POST['mappings']) ? $_POST['mappings'] : array();
        $fieldTypes = isset($_POST['fieldTypes']) ? $_POST['fieldTypes'] : array();
        $fieldRequired = isset($_POST['fieldRequired']) ? $_POST['fieldRequired'] : array();
        $fieldOptions = isset($_POST['fieldOptions']) ? $_POST['fieldOptions'] : array();

        $jsonMappings = json_encode($mappings);
        $jsonFieldTypes = json_encode($fieldTypes);
        $jsonFieldRequired = json_encode($fieldRequired);
        $jsonFieldOptions = json_encode($fieldOptions);

        // Build dynamic UPDATE depending on which columns exist
        $updateParts = array();
        $bindParams = array();
        $bindTypes = '';

        // plugin is always present
        $updateParts[] = 'plugin = ?';
        $bindTypes .= 's';
        $bindParams[] = $jsonMappings;

        if ($hasFieldTypes) {
            $updateParts[] = 'field_types = ?';
            $bindTypes .= 's';
            $bindParams[] = $jsonFieldTypes;
        }

        if ($hasFieldRequired) {
            $updateParts[] = 'field_required = ?';
            $bindTypes .= 's';
            $bindParams[] = $jsonFieldRequired;
        }

        if ($hasFieldOptions) {
            $updateParts[] = 'field_options = ?';
            $bindTypes .= 's';
            $bindParams[] = $jsonFieldOptions;
        }

        $updateSql = 'UPDATE navigation SET ' . implode(', ', $updateParts) . ' WHERE nav = ?';
        $bindTypes .= 's'; // for nav
        $bindParams[] = $module;

        $stmt = $db->prepare($updateSql);
        if ($stmt === false) {
            echo json_encode(array('success' => false, 'message' => 'Failed to prepare update: ' . $db->error));
            exit;
        }

        // bind params dynamically (mysqli requires references)
        $params = array_merge(array($bindTypes), $bindParams);
        // mysqli::bind_param needs references, so build an array of references
        $refs = array();
        foreach ($params as $key => $value) {
            $refs[$key] = & $params[$key];
        }
        call_user_func_array(array($stmt, 'bind_param'), $refs);

        if ($stmt->execute()) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . '/erpconsole/manage.php';
            $resp = array('success' => true, 'message' => 'Field configurations saved successfully', 'redirect' => $redirect);
        } else {
            $resp = array('success' => false, 'message' => 'Failed to save configurations: ' . $stmt->error);
        }
        try { _pm_log(array('event' => 'save_response', 'response' => $resp)); } catch (
        Exception $e) {}
        echo json_encode($resp);
        $stmt->close();
        exit;
    }

    // Add column action
    if ($action === 'add_column') {
        $col = isset($_POST['column']) ? trim($_POST['column']) : '';

        // Validate column name strictly
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $col)) {
            echo json_encode(array('success' => false, 'message' => 'Invalid column name'));
            exit;
        }

        // Protect critical columns
        $protected = array('id', 'role', 'created_at', 'updated_at');
        if (in_array(strtolower($col), $protected)) {
            echo json_encode(array('success' => false, 'message' => 'Refusing to modify protected column'));
            exit;
        }

        // Check column doesn't already exist
        $check = mysqli_query($db, "SHOW COLUMNS FROM `" . $db->real_escape_string($tableName) . "` LIKE '" . $db->real_escape_string($col) . "'");
        if ($check && mysqli_num_rows($check) > 0) {
            echo json_encode(array('success' => false, 'message' => 'Column already exists'));
            exit;
        }

        // Always create a nullable TEXT column (server-enforced default)
        $sqlType = 'TEXT';
        $nullSql = 'NULL';
        $alterSql = "ALTER TABLE `" . $db->real_escape_string($tableName) . "` ADD COLUMN `" . $db->real_escape_string($col) . "` $sqlType $nullSql";

        if (mysqli_query($db, $alterSql)) {
            echo json_encode(array('success' => true, 'message' => 'New field has been added successfully!'));
        } else {
            error_log('Failed to add column ' . $col . ' to ' . $tableName . ': ' . mysqli_error($db));
            echo json_encode(array('success' => false, 'message' => 'Failed to add column'));
        }
        exit;
    }

    // Drop column action
    if ($action === 'drop_column') {
        $col = isset($_POST['column']) ? trim($_POST['column']) : '';
        $confirm = isset($_POST['confirm']) && $_POST['confirm'] == '1';

        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $col)) {
            echo json_encode(array('success' => false, 'message' => 'Invalid column name'));
            exit;
        }

        $protected = array('id', 'role', 'created_at', 'updated_at');
        if (in_array(strtolower($col), $protected)) {
            echo json_encode(array('success' => false, 'message' => 'Refusing to drop protected column'));
            exit;
        }

        // Ensure column exists
        $check = mysqli_query($db, "SHOW COLUMNS FROM `" . $db->real_escape_string($tableName) . "` LIKE '" . $db->real_escape_string($col) . "'");
        if (!($check && mysqli_num_rows($check) > 0)) {
            echo json_encode(array('success' => false, 'message' => 'Column does not exist'));
            exit;
        }

        if (!$confirm) {
            echo json_encode(array('success' => false, 'message' => 'Confirmation required to drop column'));
            exit;
        }

        $alterSql = "ALTER TABLE `" . $db->real_escape_string($tableName) . "` DROP COLUMN `" . $db->real_escape_string($col) . "`";
        if (mysqli_query($db, $alterSql)) {
            // Also remove any mappings for this column by updating plugin/field mappings
            // remove from plugin json stored in navigation.plugin
            $navRes = mysqli_query($db, "SELECT plugin, field_types, field_required, field_options FROM navigation WHERE nav = '" . $db->real_escape_string($module) . "' LIMIT 1");
            if ($navRes && $navRow = mysqli_fetch_assoc($navRes)) {
                $changed = false;
                $pluginArr = json_decode($navRow['plugin'], true) ?: array();
                unset($pluginArr[$col]);
                $ft = json_decode($navRow['field_types'], true) ?: array(); unset($ft[$col]);
                $fr = json_decode($navRow['field_required'], true) ?: array(); unset($fr[$col]);
                $fo = json_decode($navRow['field_options'], true) ?: array(); unset($fo[$col]);
                $upd = $db->prepare('UPDATE navigation SET plugin = ?, field_types = ?, field_required = ?, field_options = ? WHERE nav = ?');
                if ($upd) {
                    $upd->bind_param('sssss', json_encode($pluginArr), json_encode($ft), json_encode($fr), json_encode($fo), $module);
                    $upd->execute();
                    $upd->close();
                }
            }
            echo json_encode(array('success' => true, 'message' => 'Column dropped and mappings cleaned'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to drop column: ' . mysqli_error($db)));
        }
        exit;
    }

    // unknown action
    echo json_encode(array('success' => false, 'message' => 'Unknown action'));
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plugin Mapping - <?= htmlspecialchars($module) ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="loader.css">
    <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/resource/bootstrap.min.js"></script>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 900px;
        }
        h2 {
            color: #0D104D;
            margin-bottom: 30px;
        }
        .mapping-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .field-label {
            flex: 0 0 160px;
            font-weight: 600;
            color: #333;
        }
        .plugin-select {
            flex: 1;
            margin: 0 8px;
        }
        .type-select {
            flex: 0 0 160px;
            margin: 0 8px;
        }
        .required-checkbox {
            flex: 0 0 90px;
            margin: 0 8px;
            text-align: center;
        }
        .remove-btn {
            flex: 0 0 70px;
            text-align: right;
        }
        .options-input {
            flex: 1 1 100%;
            margin-top: 10px;
            display: none;
        }
        .options-input.show {
            display: block;
        }
        .options-input input {
            width: 100%;
        }
        .btn-save {
            background: #0D104D;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            margin-top: 20px;
            border: none;
        }
        .btn-save:hover {
            background: #1a1d6b;
            color: white;
        }
        /* Keep button text white on focus/active/disabled to avoid black text after click */
        .btn-save:focus, .btn-save:active, .btn-save:disabled {
            color: #fff !important;
            background: #0D104D !important;
            box-shadow: none !important;
        }
        .alert {
            margin-top: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #0D104D;
        }
        .no-plugin {
            color: #999;
            font-style: italic;
        }
        /* Add-field section styles */
        .add-field-section {
            margin-top: 18px;
        }
        .add-field-panel {
            background: #fbfdff;
            border: 1px solid #e6eef8;
            padding: 16px;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(13,16,77,0.03);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .add-field-panel .form-control { max-width: 320px; }
        .add-field-panel .col-inline { display:inline-block; vertical-align:middle; }
        .add-field-panel .btn { white-space:nowrap; }
    </style>
</head>
<body>
        <!-- Loading Overlay (reused project loader) -->
        <div id="loadingOverlay" class="loadmodal" style="display: none;">
            <div class="loading">
                <div class="loading-bar"></div>
                <div class="loading-bar"></div>
                <div class="loading-bar"></div>
                <div class="loading-bar"></div>
            </div>
        </div>
    <div class="container">
        <a href="./erpconsole/manage.php" class="back-link">← Back to Manage Apps</a>
        
        <h2>Field Configuration: <?= htmlspecialchars($module) ?></h2>
        
        <p class="text-muted">Configure each field's plugin mapping and input type. When a field has a plugin assigned, clicking the plugin icon will directly open that plugin. The input type controls how the field is rendered in add/edit forms.</p>
        <div class="add-field-section">
            <div class="add-field-panel" style="width:100%">
                <div style="flex:1 1 60%; min-width:220px;">
                    <strong style="color:#334">Add New Field</strong>
                    <div style="font-size:12px;color:#666">Create a new column for this module.</div>
                </div>
                <div style="flex:0 1 35%; text-align:right;">
                    <button type="button" id="showAddFieldBtn" class="btn btn-success">Add New Field</button>
                </div>
                <div style="width:100%; margin-top:10px;">
                    <div id="addFieldContainer" style="display:none;">
                        <form id="addColumnForm" class="form-inline" style="margin-bottom:0;">
                            <div class="row" style="width:100%">
                                <div class="col-xs-12 col-sm-9" style="margin-bottom:6px;">
                                    <input type="text" class="form-control" name="column" placeholder="Enter New Field Name" required>
                                </div>
                                <div class="col-xs-12 col-sm-3" style="text-align:right;">
                                    <button type="button" id="addColumnBtn" class="btn btn-primary">Add Field</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <form id="mappingForm">
            <div id="mappings-container">
                <?php foreach ($columns as $column): ?>
                <div class="mapping-row">
                    <div class="field-label">
                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $column))) ?>
                    </div>
                    <div class="plugin-select">
                        <label style="font-size: 11px; color: #666; margin-bottom: 3px;">Plugin</label>
                        <select class="form-control" name="mappings[<?= htmlspecialchars($column) ?>]">
                            <option value="">-- No Plugin --</option>
                            <?php foreach ($plugins as $plugin): ?>
                            <option value="<?= htmlspecialchars($plugin) ?>" 
                                <?= (isset($pluginMappings[$column]) && $pluginMappings[$column] === $plugin) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($plugin) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="type-select">
                        <label style="font-size: 11px; color: #666; margin-bottom: 3px;">Input Type</label>
                        <select class="form-control" name="fieldTypes[<?= htmlspecialchars($column) ?>]">
                            <option value="text" <?= (!isset($fieldTypeMappings[$column]) || $fieldTypeMappings[$column] === 'text') ? 'selected' : '' ?>>Text</option>
                            <option value="password" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'password') ? 'selected' : '' ?>>Password</option>
                            <option value="search" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'search') ? 'selected' : '' ?>>Search</option>
                            <option value="email" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'email') ? 'selected' : '' ?>>Email</option>
                            <option value="tel" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'tel') ? 'selected' : '' ?>>Tel</option>
                            <option value="url" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'url') ? 'selected' : '' ?>>URL</option>
                            <option value="number" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'number') ? 'selected' : '' ?>>Number</option>
                            <option value="color" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'color') ? 'selected' : '' ?>>Color</option>
                            <option value="textarea" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'textarea') ? 'selected' : '' ?>>Textarea</option>
                            <option value="select" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'select') ? 'selected' : '' ?>>Select Dropdown</option>
                            <option value="radio" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'radio') ? 'selected' : '' ?>>Radio Buttons</option>
                            <option value="checkbox" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'checkbox') ? 'selected' : '' ?>>Checkboxes</option>
                            <option value="date" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'date') ? 'selected' : '' ?>>Date</option>
                            <option value="datetime-local" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'datetime-local') ? 'selected' : '' ?>>DateTime-Local</option>
                            <option value="month" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'month') ? 'selected' : '' ?>>Month</option>
                            <option value="week" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'week') ? 'selected' : '' ?>>Week</option>
                            <option value="time" <?= (isset($fieldTypeMappings[$column]) && $fieldTypeMappings[$column] === 'time') ? 'selected' : '' ?>>Time</option>
                        </select>
                    </div>
                    <div class="required-checkbox">
                        <label style="font-size: 11px; color: #666; margin-bottom: 3px;">Required</label>
                        <div>
                            <input type="checkbox" name="fieldRequired[<?= htmlspecialchars($column) ?>]" value="1" 
                                <?= (isset($fieldRequiredMappings[$column]) && $fieldRequiredMappings[$column]) ? 'checked' : '' ?>
                                style="width: 20px; height: 20px; cursor: pointer;">
                        </div>
                    </div>
                    <div class="remove-btn">
                        <button type="button" class="btn btn-sm btn-default clear-mapping" data-field="<?= htmlspecialchars($column) ?>">
                            Clear
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-column" data-field="<?= htmlspecialchars($column) ?>" style="margin-left:6px;">
                            Drop
                        </button>
                    </div>
                    <div class="options-input" data-field="<?= htmlspecialchars($column) ?>" <?= (isset($fieldTypeMappings[$column]) && in_array($fieldTypeMappings[$column], array('select', 'radio', 'checkbox'))) ? 'style="display:block;"' : '' ?>>
                        <label style="font-size: 11px; color: #666; margin-bottom: 3px;">Options (comma-separated, e.g., "Red,Green,Blue")</label>
                        <input type="text" class="form-control" name="fieldOptions[<?= htmlspecialchars($column) ?>]" 
                            value="<?= isset($fieldOptionsMappings[$column]) ? htmlspecialchars($fieldOptionsMappings[$column]) : '' ?>" 
                            placeholder="Option1,Option2,Option3">
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($columns)): ?>
                <p class="text-muted">No fields found in this module.</p>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($columns)): ?>
            <button type="submit" class="btn btn-save">Save Mappings</button>
            <?php endif; ?>
        </form>
        
        <!-- Add Field UI (moved inside panel) -->
        
        <div id="alertContainer"></div>
    </div>

    <script>
        $(document).ready(function() {
            // If navigation requested persistent loader (set by manage.php), show it until full load
            try {
                if (localStorage && localStorage.getItem('showLoaderUntilPageLoad')) {
                    $('#loadingOverlay').show();
                    // remove the flag so subsequent reloads don't show it
                    localStorage.removeItem('showLoaderUntilPageLoad');
                    // ensure overlay hides on full window load
                    $(window).on('load', function () { $('#loadingOverlay').fadeOut(150); });
                }
            } catch (e) {}
            // Clear individual mapping
            $('.clear-mapping').click(function() {
                var field = $(this).data('field');
                $('select[name="mappings[' + field + ']"]').val('');
                $('select[name="fieldTypes[' + field + ']"]').val('text');
                $('input[name="fieldRequired[' + field + ']"]').prop('checked', false);
                $('input[name="fieldOptions[' + field + ']"]').val('');
                $('.options-input[data-field="' + field + '"]').hide();
            });
            
            // Delete column
            $('.delete-column').click(function() {
                var field = $(this).data('field');
                if (!confirm('Are you sure you want to DROP column "' + field + '"? This is destructive.')) return;
                showLoader();
                $.post(window.location.href, { action: 'drop_column', column: field, confirm: '1' }, function(resp) {
                    hideLoader();
                    if (resp && resp.success) {
                        showAlert('success', resp.message);
                        setTimeout(function(){ location.reload(); }, 700);
                    } else {
                        showAlert('danger', resp ? resp.message : 'Failed to drop column');
                    }
                }, 'json').fail(function(){ hideLoader(); showAlert('danger','Request failed'); });
            });

            // Show Add Field form with slide animation and focus
            $('#showAddFieldBtn').click(function() {
                $('#addFieldContainer').slideToggle(180, function() {
                    if ($(this).is(':visible')) {
                        $(this).find('input[name=column]').focus();
                    }
                });
            });

            // Add column (Add Field) — client validates name and sends only the column
            $('#addColumnBtn').click(function() {
                var input = $('#addFieldContainer').find('input[name="column"]');
                var col = $.trim(input.val());
                var nameRegex = /^[A-Za-z_][A-Za-z0-9_]*$/;
                if (!nameRegex.test(col)) {
                    showAlert('danger', 'Invalid column name. Use letters, numbers and underscores, must not start with a number.');
                    input.focus();
                    return;
                }
                var payload = { action: 'add_column', column: col };
                // disable button to prevent double submits
                $('#addColumnBtn').prop('disabled', true);
                showLoader();
                $.post(window.location.href, payload, function(resp) {
                    hideLoader();
                    $('#addColumnBtn').prop('disabled', false);
                    if (resp && resp.success) {
                        showAlert('success', resp.message);
                        setTimeout(function(){ location.reload(); }, 700);
                    } else {
                        showAlert('danger', resp ? resp.message : 'Failed to add column');
                    }
                }, 'json').fail(function(){ hideLoader(); $('#addColumnBtn').prop('disabled', false); showAlert('danger','Request failed'); });
            });
            
            // Show/hide options input based on field type selection
            $('select[name^="fieldTypes["]').change(function() {
                var fieldName = $(this).attr('name').match(/fieldTypes\[(.*)\]/)[1];
                var selectedType = $(this).val();
                var optionsDiv = $('.options-input[data-field="' + fieldName + '"]');
                
                if (selectedType === 'select' || selectedType === 'radio' || selectedType === 'checkbox') {
                    optionsDiv.show();
                } else {
                    optionsDiv.hide();
                }
            });
            
            // Handle form submission
            $('#mappingForm').submit(function(e) {
                e.preventDefault();
                
                var formData = $(this).serializeArray();
                var mappings = {};
                var fieldTypes = {};
                var fieldRequired = {};
                var fieldOptions = {};
                
                // Build mappings, fieldTypes, fieldRequired, and fieldOptions objects
                formData.forEach(function(item) {
                    var mappingMatch = item.name.match(/mappings\[(.*)\]/);
                    var typeMatch = item.name.match(/fieldTypes\[(.*)\]/);
                    var requiredMatch = item.name.match(/fieldRequired\[(.*)\]/);
                    var optionsMatch = item.name.match(/fieldOptions\[(.*)\]/);
                    
                    if (mappingMatch && item.value) {
                        mappings[mappingMatch[1]] = item.value;
                    }
                    if (typeMatch && item.value) {
                        fieldTypes[typeMatch[1]] = item.value;
                    }
                    if (requiredMatch && item.value) {
                        fieldRequired[requiredMatch[1]] = true;
                    }
                    if (optionsMatch && item.value) {
                        fieldOptions[optionsMatch[1]] = item.value;
                    }
                });
                
                    // Send AJAX request using FormData so PHP receives arrays properly
                    $('.btn-save').prop('disabled', true);
                    showLoader();

                    var fd = new FormData();
                    fd.append('action', 'save');
                    Object.keys(mappings).forEach(function(k){ fd.append('mappings['+k+']', mappings[k]); });
                    Object.keys(fieldTypes).forEach(function(k){ fd.append('fieldTypes['+k+']', fieldTypes[k]); });
                    Object.keys(fieldOptions).forEach(function(k){ fd.append('fieldOptions['+k+']', fieldOptions[k]); });
                    Object.keys(fieldRequired).forEach(function(k){ fd.append('fieldRequired['+k+']', fieldRequired[k] ? '1' : '0'); });

                    $.ajax({
                        url: window.location.pathname + window.location.search,
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.success) {
                                showAlert('success', response.message);
                                // ensure overlay is hidden before navigating
                                hideLoader();
                                // set loader flag for the target page, then attempt top-level navigation using secure URL
                                try { localStorage.setItem('showLoaderUntilPageLoad', '1'); } catch (e) {}
                                (function(){
                                    var target = (response && response.redirect) ? response.redirect : ('https://' + window.location.host + '/erpconsole/manage.php');
                                    // Navigate within the current frame (do not force top-level navigation)
                                    try { window.location.href = target; } catch (e) { /* fallback ignore */ }
                                })();
                                // safety: if navigation didn't happen for some reason, clear loader and re-enable
                                setTimeout(function() { $('.btn-save').prop('disabled', false); hideLoader(); }, 5000);
                            } else {
                                console.error('Save response error:', response);
                                showAlert('danger', response && response.message ? response.message : 'Failed to save configurations');
                                $('.btn-save').prop('disabled', false);
                                hideLoader();
                            }
                        },
                        error: function(xhr, status, err) {
                            console.error('AJAX save failed', status, err, xhr.responseText);
                            showAlert('danger', 'An error occurred while saving mappings');
                            $('.btn-save').prop('disabled', false);
                            hideLoader();
                        }
                    });
            });
            
            function showAlert(type, message) {
                var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    message +
                    '</div>';
                $('#alertContainer').html(alertHtml);
                
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 3000);
            }
            
            // Loader helpers (reuse project's standard loader)
            var loaderCount = 0;
            function showLoader() {
                loaderCount++;
                $('#loadingOverlay').show();
            }

            function hideLoader() {
                loaderCount--;
                if (loaderCount <= 0) {
                    loaderCount = 0;
                    $('#loadingOverlay').hide();
                    // notify parent frame about height change if embedded
                    setTimeout(function() {
                        try { window.parent.postMessage(document.body.scrollHeight, '*'); } catch (e) {}
                    }, 100);
                }
            }
        });
    </script>
        <script>
        (function(){
            // Ensure loader doesn't hang: if the flag is set but load event doesn't fire, hide after timeout
            try {
                if (localStorage && localStorage.getItem('showLoaderUntilPageLoad')) {
                    var overlay = document.getElementById('loadingOverlay');
                    if (overlay) overlay.style.display = 'block';
                    localStorage.removeItem('showLoaderUntilPageLoad');
                    window.addEventListener('load', function(){ if (overlay) overlay.style.display = 'none'; });
                    // fallback auto-hide after 6s
                    setTimeout(function(){ if (overlay) overlay.style.display = 'none'; }, 6000);
                }
            } catch(e){}

            // If jQuery failed to load (common on some mobile setups), provide a lightweight vanilla fallback
            if (typeof window.jQuery === 'undefined') {
                function showOverlay(){ var o=document.getElementById('loadingOverlay'); if(o) o.style.display='block'; }
                function hideOverlay(){ var o=document.getElementById('loadingOverlay'); if(o) o.style.display='none'; }
                function showAlert(type, msg){ var c=document.getElementById('alertContainer'); if(!c) return; c.innerHTML='<div class="alert alert-'+type+'">'+msg+'</div>'; setTimeout(function(){ if(c) c.innerHTML=''; },3000); }

                // Clear mapping
                document.querySelectorAll('.clear-mapping').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        var field = btn.getAttribute('data-field');
                        var sel = document.querySelector('select[name="mappings['+field+']"]'); if(sel) sel.value='';
                        var sel2 = document.querySelector('select[name="fieldTypes['+field+']"]'); if(sel2) sel2.value='text';
                        var chk = document.querySelector('input[name="fieldRequired['+field+']"]'); if(chk) chk.checked=false;
                        var opt = document.querySelector('input[name="fieldOptions['+field+']"]'); if(opt) opt.value='';
                        var optionsDiv = document.querySelector('.options-input[data-field="'+field+'"]'); if(optionsDiv) optionsDiv.style.display='none';
                    });
                });

                // Drop column
                document.querySelectorAll('.delete-column').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        var field = btn.getAttribute('data-field');
                        if (!confirm('Are you sure you want to DROP column "' + field + '"? This is destructive.')) return;
                        showOverlay();
                        var fd = new FormData(); fd.append('action','drop_column'); fd.append('column', field); fd.append('confirm','1');
                        fetch(window.location.pathname + window.location.search, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(resp){
                            hideOverlay();
                            if (resp && resp.success) { showAlert('success', resp.message); setTimeout(function(){ location.reload(); },700); }
                            else { showAlert('danger', resp ? resp.message : 'Failed to drop column'); }
                        }).catch(function(){ hideOverlay(); showAlert('danger','Request failed'); });
                    });
                });

                // Add column
                var addBtn = document.getElementById('addColumnBtn');
                if (addBtn) {
                    addBtn.addEventListener('click', function(){
                        var input = document.querySelector('#addFieldContainer input[name="column"]');
                        var col = input ? input.value.trim() : '';
                        var nameRegex = /^[A-Za-z_][A-Za-z0-9_]*$/;
                        if (!nameRegex.test(col)) { showAlert('danger','Invalid column name. Use letters, numbers and underscores, must not start with a number.'); if (input) input.focus(); return; }
                        addBtn.disabled = true;
                        showOverlay();
                        var fd2 = new FormData(); fd2.append('action','add_column'); fd2.append('column', col);
                        fetch(window.location.pathname + window.location.search, { method: 'POST', body: fd2, credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(resp){
                            hideOverlay(); addBtn.disabled = false;
                            if (resp && resp.success) { showAlert('success', resp.message); setTimeout(function(){ location.reload(); },700); }
                            else { showAlert('danger', resp ? resp.message : 'Failed to add column'); }
                        }).catch(function(){ hideOverlay(); addBtn.disabled = false; showAlert('danger','Request failed'); });
                    });
                }

                // Show/hide options input based on type
                document.querySelectorAll('select[name^="fieldTypes["]').forEach(function(sel){
                    sel.addEventListener('change', function(){
                        var m = sel.name.match(/fieldTypes\[(.*)\]/);
                        if (!m) return; var fieldName = m[1]; var selectedType = sel.value;
                        var optionsDiv = document.querySelector('.options-input[data-field="'+fieldName+'"]');
                        if (optionsDiv) {
                            if (selectedType === 'select' || selectedType === 'radio' || selectedType === 'checkbox') optionsDiv.style.display = 'block'; else optionsDiv.style.display = 'none';
                        }
                    });
                });

                // Mapping form submit fallback (constructs payloads similar to jQuery version)
                var mappingForm = document.getElementById('mappingForm');
                if (mappingForm) {
                    mappingForm.addEventListener('submit', function(e){
                        e.preventDefault();
                        showOverlay();
                        var fd = new FormData(mappingForm);
                        var mappings = {}, fieldTypes = {}, fieldRequired = {}, fieldOptions = {};
                        for (var pair of fd.entries()) {
                            var name = pair[0], val = pair[1];
                            var m = name.match(/mappings\[(.*)\]/);
                            var t = name.match(/fieldTypes\[(.*)\]/);
                            var r = name.match(/fieldRequired\[(.*)\]/);
                            var o = name.match(/fieldOptions\[(.*)\]/);
                            if (m && val) mappings[m[1]] = val;
                            if (t && val) fieldTypes[t[1]] = val;
                            if (r) fieldRequired[r[1]] = true;
                            if (o && val) fieldOptions[o[1]] = val;
                        }
                        var outFd = new FormData();
                        outFd.append('action','save');
                        Object.keys(mappings).forEach(function(k){ outFd.append('mappings['+k+']', mappings[k]); });
                        Object.keys(fieldTypes).forEach(function(k){ outFd.append('fieldTypes['+k+']', fieldTypes[k]); });
                        Object.keys(fieldOptions).forEach(function(k){ outFd.append('fieldOptions['+k+']', fieldOptions[k]); });
                        Object.keys(fieldRequired).forEach(function(k){ outFd.append('fieldRequired['+k+']', fieldRequired[k] ? '1' : '0'); });

                        fetch(window.location.pathname + window.location.search, { method: 'POST', body: outFd, credentials: 'same-origin' })
                        .then(function(r){ return r.json(); }).then(function(resp){
                            // hide overlay before navigation
                            hideOverlay();
                            if (resp && resp.success) {
                                showAlert('success', resp.message);
                                try{ localStorage.setItem('showLoaderUntilPageLoad','1'); }catch(e){}
                                (function(){
                                    var target = (resp && resp.redirect) ? resp.redirect : ('https://' + window.location.host + '/erpconsole/manage.php');
                                    // Navigate within the current frame (do not force top-level navigation)
                                    try { window.location.href = target; } catch (e) { /* fallback ignore */ }
                                })();
                                // safety: if navigation didn't occur, clear overlay after timeout
                                setTimeout(function(){ hideOverlay(); }, 5000);
                            } else {
                                console.error('Save failed (fallback):', resp);
                                showAlert('danger', resp ? resp.message : 'An error occurred');
                            }
                        }).catch(function(err){ hideOverlay(); console.error('Save fetch error (fallback):', err); showAlert('danger','An error occurred while saving mappings'); });
                    });
                }
            }
        })();
        </script>
</body>
</html>

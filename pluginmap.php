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

// Handle AJAX save request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    header('Content-Type: application/json');
    
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
    $bindNames = array();
    $bindNames[] = $bindTypes;
    for ($i = 0; $i < count($bindParams); $i++) {
        $bindNames[] = & $bindParams[$i];
    }
    call_user_func_array(array($stmt, 'bind_param'), $bindNames);

    if ($stmt->execute()) {
        echo json_encode(array('success' => true, 'message' => 'Field configurations saved successfully'));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to save configurations: ' . $stmt->error));
    }
    $stmt->close();
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
                
                    // Send AJAX request
                    // Disable the save button, show loader and block UI
                    $('.btn-save').prop('disabled', true);
                    showLoader();

                    $.ajax({
                        url: window.location.href,
                        method: 'POST',
                        data: {
                            action: 'save',
                            mappings: mappings,
                            fieldTypes: fieldTypes,
                            fieldRequired: fieldRequired,
                            fieldOptions: fieldOptions
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message);
                                // short delay so user sees the message, then navigate back
                                setTimeout(function() {
                                    try { localStorage.setItem('showLoaderUntilPageLoad', '1'); } catch (e) {}
                                    window.location.href = './erpconsole/manage.php';
                                }, 700);
                            } else {
                                showAlert('danger', response.message);
                                $('.btn-save').prop('disabled', false);
                                hideLoader();
                            }
                        },
                        error: function() {
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
</body>
</html>

<?php
// Include database configuration
require_once 'config.php';

// Use the existing connection from config.php
$conn = $db;

// Function to get all databases
function getDatabases($conn) {
    $databases = array();
    $result = $conn->query("SHOW DATABASES");
    if ($result) {
        while ($row = $result->fetch_array()) {
            $databases[] = $row[0];
        }
    }
    return $databases;
}

// Function to get tables in a database
function getTables($conn, $db) {
    $tables = array();
    if ($conn->select_db($db)) {
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
        }
    }
    return $tables;
}

// Function to get table schema
function getTableSchema($conn, $db, $table) {
    $schema = array();
    if ($conn->select_db($db)) {
        $result = $conn->query("DESCRIBE `$table`");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $schema[] = $row;
            }
        }
    }
    return $schema;
}

// Function to get table data
function getTableData($conn, $db, $table, $limit = 10) {
    $data = array();
    if ($conn->select_db($db)) {
        $result = $conn->query("SELECT * FROM `$table` LIMIT $limit");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
    }
    return $data;
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $response = array();
    
    try {
        switch ($_GET['action']) {
            case 'get_tables':
                $db = $conn->real_escape_string($_GET['db']);
                $response['tables'] = getTables($conn, $db);
                break;
                
            case 'get_schema':
                $db = $conn->real_escape_string($_GET['db']);
                $table = $conn->real_escape_string($_GET['table']);
                $response['schema'] = getTableSchema($conn, $db, $table);
                $response['data'] = getTableData($conn, $db, $table, 5); // Get first 5 rows
                break;
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Schema Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            overflow-y: auto;
            background-color: #2c3e50;
            border-right: 1px solid #1a252f;
            color: #ecf0f1;
            padding: 15px;
        }
        .sidebar h4 {
            color: #3498db;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #3e4f61;
        }
        .db-item {
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 4px 0;
            font-weight: 500;
            transition: all 0.2s ease;
            color: #ecf0f1;
            border-left: 3px solid transparent;
        }
        .table-item {
            cursor: pointer;
            padding: 6px 12px 6px 25px;
            border-radius: 3px;
            margin: 2px 0;
            font-size: 0.9em;
            color: #bdc3c7;
            transition: all 0.2s ease;
        }
        .db-item:hover, .table-item:hover {
            background-color: #34495e;
            color: #ffffff;
        }
        .db-item.active {
            background-color: #2980b9;
            color: white;
            border-left: 3px solid #3498db;
        }
        .table-item.active {
            background-color: #3d566e;
            color: white;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <h4>Databases</h4>
                    <div id="databaseList">
                        <?php 
                        $databases = getDatabases($conn);
                        foreach ($databases as $db): 
                            if (!in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys'])): 
                        ?>
                            <div class="db-item" data-db="<?php echo htmlspecialchars($db); ?>">
                                <i class="bi bi-database"></i> <?php echo htmlspecialchars($db); ?>
                                <div class="tables-container" style="display: none; margin-left: 15px;"></div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2" id="currentTable">Select a table to view schema</h1>
                </div>

                <div id="schemaContent">
                    <div class="alert alert-info">
                        Select a database and table from the sidebar to view its schema and sample data.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle database click
            $('.db-item').click(function(e) {
                e.stopPropagation();
                const $this = $(this);
                const db = $this.data('db');
                const $tablesContainer = $this.find('.tables-container');
                
                // Toggle active state
                $('.db-item').removeClass('active');
                $this.addClass('active');
                
                // Toggle tables visibility
                if ($tablesContainer.is(':empty')) {
                    // Fetch tables via AJAX
                    $.get('schema-check.php', { action: 'get_tables', db: db }, function(response) {
                        if (response.error) {
                            alert('Error: ' + response.error);
                            return;
                        }
                        
                        let tablesHtml = '';
                        response.tables.forEach(function(table) {
                            tablesHtml += `
                                <div class="table-item" data-db="${db}" data-table="${table}">
                                    <i class="bi bi-table"></i> ${table}
                                </div>
                            `;
                        });
                        
                        $tablesContainer.html(tablesHtml).slideDown();
                        
                        // Add click handler for new table items
                        $tablesContainer.find('.table-item').click(function(e) {
                            e.stopPropagation();
                            const $tableItem = $(this);
                            const db = $tableItem.data('db');
                            const table = $tableItem.data('table');
                            
                            // Update active state
                            $('.table-item').removeClass('active');
                            $tableItem.addClass('active');
                            
                            // Show loading
                            $('#schemaContent').html(`
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p>Loading schema and data for ${table}...</p>
                                </div>
                            `);
                            
                            // Fetch schema and data
                            $.get('schema-check.php', { 
                                action: 'get_schema', 
                                db: db, 
                                table: table 
                            }, function(response) {
                                if (response.error) {
                                    $('#schemaContent').html(`
                                        <div class="alert alert-danger">Error: ${response.error}</div>
                                    `);
                                    return;
                                }
                                
                                // Display schema
                                let schemaHtml = `
                                    <h3>${db}.${table}</h3>
                                    <h4 class="mt-4">Schema</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Type</th>
                                                    <th>Null</th>
                                                    <th>Key</th>
                                                    <th>Default</th>
                                                    <th>Extra</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                
                                response.schema.forEach(function(column) {
                                    schemaHtml += `
                                        <tr>
                                            <td>${column.Field}</td>
                                            <td>${column.Type}</td>
                                            <td>${column.Null}</td>
                                            <td>${column.Key}</td>
                                            <td>${column.Default === null ? 'NULL' : column.Default}</td>
                                            <td>${column.Extra}</td>
                                        </tr>
                                    `;
                                });
                                
                                schemaHtml += `
                                            </tbody>
                                        </table>
                                    </div>
                                `;
                                
                                // Display sample data if any
                                if (response.data && response.data.length > 0) {
                                    schemaHtml += `
                                        <h4 class="mt-4">Sample Data (First 5 Rows)</h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                    `;
                                    
                                    // Table headers
                                    Object.keys(response.data[0]).forEach(function(key) {
                                        schemaHtml += `<th>${key}</th>`;
                                    });
                                    
                                    schemaHtml += `
                                                    </tr>
                                                </thead>
                                                <tbody>
                                    `;
                                    
                                    // Table data
                                    response.data.forEach(function(row) {
                                        schemaHtml += '<tr>';
                                        Object.values(row).forEach(function(value) {
                                            const displayValue = value === null ? 'NULL' : 
                                                               (typeof value === 'object' ? JSON.stringify(value) : value);
                                            schemaHtml += `<td>${displayValue}</td>`;
                                        });
                                        schemaHtml += '</tr>';
                                    });
                                    
                                    schemaHtml += `
                                                </tbody>
                                            </table>
                                        </div>
                                    `;
                                } else {
                                    schemaHtml += `
                                        <div class="alert alert-warning mt-4">No data found in this table.</div>
                                    `;
                                }
                                
                                $('#schemaContent').html(schemaHtml);
                                $('#currentTable').text(`${db}.${table}`);
                            });
                        });
                    });
                } else {
                    $tablesContainer.slideToggle();
                }
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
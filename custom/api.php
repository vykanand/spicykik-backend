<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");

//`created_at`DATETIME DEFAULT CURRENT_TIMESTAMP,
//config

include "../config.php";

$table_name = basename(getcwd());

// Get plugin mappings from navigation table
if (isset($_GET["getpluginmap"])) {
    $sql = "SELECT plugin FROM navigation WHERE nav = '$table_name'";
    $result = mysqli_query($db, $sql);
    
    $pluginMap = array();
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['plugin'])) {
            $pluginMap = json_decode($row['plugin'], true);
            if (!is_array($pluginMap)) {
                $pluginMap = array();
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($pluginMap);
    exit();
}

// Get field type mappings from navigation table
if (isset($_GET["getfieldtypes"])) {
    $sql = "SELECT field_types FROM navigation WHERE nav = '$table_name'";
    $result = mysqli_query($db, $sql);
    
    $fieldTypeMap = array();
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['field_types'])) {
            $fieldTypeMap = json_decode($row['field_types'], true);
            if (!is_array($fieldTypeMap)) {
                $fieldTypeMap = array();
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($fieldTypeMap);
    exit();
}

// Get field required mappings from navigation table
if (isset($_GET["getfieldrequired"])) {
    $sql = "SELECT field_required FROM navigation WHERE nav = '$table_name'";
    $result = mysqli_query($db, $sql);
    
    $fieldRequiredMap = array();
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['field_required'])) {
            $fieldRequiredMap = json_decode($row['field_required'], true);
            if (!is_array($fieldRequiredMap)) {
                $fieldRequiredMap = array();
            } else {
                // Coerce common truthy/falsey string values to real booleans
                foreach ($fieldRequiredMap as $k => $v) {
                    if (is_string($v)) {
                        $lower = strtolower(trim($v));
                        if ($lower === 'true' || $lower === '1' || $lower === 'yes' || $lower === 'on') {
                            $fieldRequiredMap[$k] = true;
                        } else {
                            $fieldRequiredMap[$k] = false;
                        }
                    } else {
                        $fieldRequiredMap[$k] = (bool) $v;
                    }
                }
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($fieldRequiredMap);
    exit();
}

// Get field options mappings from navigation table
if (isset($_GET["getfieldoptions"])) {
    $sql = "SELECT field_options FROM navigation WHERE nav = '$table_name'";
    $result = mysqli_query($db, $sql);
    
    $fieldOptionsMap = array();
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['field_options'])) {
            $fieldOptionsMap = json_decode($row['field_options'], true);
            if (!is_array($fieldOptionsMap)) {
                $fieldOptionsMap = array();
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($fieldOptionsMap);
    exit();
}

if (isset($_GET["getfirstcontent"])) {
    $user_role = isset($_GET['role']) ? $_GET['role'] : '';

    // Get table structure
    $sql = "SHOW COLUMNS FROM $table_name";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));

    $columns = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
    }

    // Remove system columns
    $exclude_columns = array('id', 'role', 'created_at');
    $columns = array_diff($columns, $exclude_columns);

    header('Content-Type: application/json');
    echo json_encode(array_values($columns));
    exit();
}


if (isset($_GET["getcontent"]))
{

    $sql = "SELECT * FROM $table_name order by created_at DESC";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    $fulldata = array();
    while ($r = mysqli_fetch_assoc($result))
    {
        $fulldata[] = $r;

    }
    echo json_encode($fulldata);
}

if (isset($_REQUEST["setcontent"]))
{
    $data = $_POST;
    unset($data["setcontent"]);
    $key = array_keys($data); //get key( column name)
    $value = array_values($data); //get values (values to be inserted)
    $sql = "INSERT INTO $table_name ( " . implode(',', $key) . ") VALUES('" . implode("','", $value) . "')";

    $result = mysqli_query($db, $sql) or die(mysqli_error($db));

    if ($result)
    {
        $message = array(
            "response" => "success"
        );
    }
    else
    {
        $message = array(
            "response" => "failed"
        );
    }
    echo json_encode($message);

}

if (isset($_POST["edcontent"]))
{
    unset($_POST["edcontent"]);
    $query = "UPDATE $table_name SET";
    $comma = " ";
    foreach($_POST as $key => $val) {
    if( ! empty($val)) {
        $query .= $comma . $key . " = '" . mysqli_real_escape_string($db,trim($val)) . "'";
        $comma = ", ";
    }
}

$product_id = $_POST['id'];

$query = $query . "WHERE id = '".$product_id."' ";

$result = mysqli_query($db, $query) or die(mysqli_error($db));

    if ($result)
    {
        $message = array(
            "response" => "success"
        );
    }
    else
    {
        $message = array(
            "response" => "failed"
        );
    }
    echo json_encode($message);

}



if (isset($_GET["delcontentsa"]))
{
    $id = $_GET['id'];
    $sql = "DELETE FROM $table_name WHERE id='$id'";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    if ($result)
    {
        $message = array(
            "response" => "success"
        );
    }
    else
    {
        $message = array(
            "response" => "failed"
        );
    }
    echo json_encode($message);

}

$db -> close();
?>

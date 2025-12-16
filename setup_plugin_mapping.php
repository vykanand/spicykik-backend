<?php
// Quick setup script to add plugin column to navigation table
// Run this once to set up the plugin mapping feature

include('./config.php');

echo "<h2>Plugin Mapping Feature Setup</h2>";

// Check if plugin column exists
$checkSql = "SHOW COLUMNS FROM navigation LIKE 'plugin'";
$result = mysqli_query($db, $checkSql);

if (mysqli_num_rows($result) > 0) {
    echo "<p style='color: green;'>✓ Plugin column already exists in navigation table.</p>";
} else {
    echo "<p style='color: orange;'>→ Adding plugin column to navigation table...</p>";
    
    $alterSql = "ALTER TABLE `navigation` ADD COLUMN `plugin` TEXT NULL AFTER `urn`";
    
    if (mysqli_query($db, $alterSql)) {
        echo "<p style='color: green;'>✓ Plugin column added successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding plugin column: " . mysqli_error($db) . "</p>";
        exit;
    }
}

echo "<hr>";
echo "<h3>Setup Complete!</h3>";
echo "<p>You can now configure plugin mappings for your modules.</p>";
echo "<ul>";
echo "<li><a href='./erpconsole/manage.php'>Go to Manage Apps</a> - Click the extension icon next to any module</li>";
echo "<li><a href='./pluginmap.php?module=weather1'>Configure weather1 mappings</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h3>Current Modules:</h3>";
$navSql = "SELECT nav, urn FROM navigation ORDER BY nav";
$navResult = mysqli_query($db, $navSql);

if ($navResult && mysqli_num_rows($navResult) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($navResult)) {
        echo "<li>";
        echo "<strong>" . htmlspecialchars($row['nav']) . "</strong> ";
        echo "(<a href='./pluginmap.php?module=" . urlencode($row['nav']) . "'>Configure Plugins</a>)";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No modules found in navigation table.</p>";
}

$db->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}
h2, h3 {
    color: #0D104D;
}
a {
    color: #0D104D;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>

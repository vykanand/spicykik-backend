<?php
/**
 * Group and Role Management Functions
 */

// Use the global database connection from config.php
if (!isset($GLOBALS['db']) || $GLOBALS['db']->connect_errno !== 0) {
    die('Database connection not available. Please check your configuration.');
}

// Set the global connection variable
$conn = $GLOBALS['db'];

/**
 * Get all available groups
 * @return array List of groups
 */
function getAllGroups() {
    global $conn;
    $groups = [];
    
    // Get unique groups from users table
    $result = $conn->query("SELECT `groups` FROM users WHERE `groups` IS NOT NULL");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $groupArray = json_decode($row['groups'], true) ?: [];
            if (is_array($groupArray)) {
                $groups = array_merge($groups, $groupArray);
            }
        }
    }
    
    return array_unique($groups);
}

/**
 * Get all roles defined in the system
 * @return array List of roles
 */
function getAllRoles() {
    global $conn;
    $roles = [];
    
    // Get unique roles from roles_map
    $result = $conn->query("SELECT roles_map FROM users WHERE roles_map IS NOT NULL");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $roleMap = json_decode($row['roles_map'], true) ?: [];
            if (is_array($roleMap)) {
                foreach ($roleMap as $group => $groupRoles) {
                    if (is_array($groupRoles)) {
                        $roles = array_merge($roles, $groupRoles);
                    }
                }
            }
        }
    }
    
    return array_unique($roles);
}

/**
 * Get all modules in the system
 * @return array List of modules
 */
function getAllModules() {
    // This is a sample list of modules. In a real application, you might want to fetch this from a database
    return [
        'Dashboard',
        'Inventory',
        'Material',
        'Warehouse',
        'Delivery',
        'Users',
        'Reports',
        'Settings',
        'Billing',
        'Purchasing',
        'Sales',
        'Customers',
        'Suppliers',
        'Accounting',
        'HR',
        'Payroll'
    ];
}

/**
 * Get user's effective access based on their groups and roles
 * @param int $userId User ID
 * @return array Array of modules the user has access to
 */
function getUserEffectiveAccess($userId) {
    global $conn;
    
    // Get user data
    $stmt = $conn->prepare("SELECT `groups`, roles_map, access_map FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [];
    }
    
    $user = $result->fetch_assoc();
    
    // Initialize arrays - no need to decode as they're already JSON columns
    $groups = $user['groups'] ?? [];
    $rolesMap = $user['roles_map'] ?? [];
    $accessMap = $user['access_map'] ?? [];
    
    // Convert JSON strings to arrays if needed
    if (is_string($groups)) {
        $groups = json_decode($groups, true) ?: [];
    }
    if (is_string($rolesMap)) {
        $rolesMap = json_decode($rolesMap, true) ?: [];
    }
    if (is_string($accessMap)) {
        $accessMap = json_decode($accessMap, true) ?: [];
    }
    $allRoles = [];
    
    // Get roles from groups
    foreach ($groups as $group) {
        if (isset($rolesMap[$group]) && is_array($rolesMap[$group])) {
            $allRoles = array_merge($allRoles, $rolesMap[$group]);
        }
    }
    
    // Add custom roles
    if (isset($rolesMap['custom']) && is_array($rolesMap['custom'])) {
        $allRoles = array_merge($allRoles, $rolesMap['custom']);
    }
    
    $allRoles = array_unique($allRoles);
    $accessModules = [];
    
    // Get access modules from roles
    foreach ($allRoles as $role) {
        if (isset($accessMap[$role]) && is_array($accessMap[$role])) {
            $accessModules = array_merge($accessModules, $accessMap[$role]);
        }
    }
    
    return array_unique($accessModules);
}

/**
 * Update user's groups
 * @param int $userId User ID
 * @param array $groups Array of groups to assign to the user
 * @return bool True on success, false on failure
 */
function updateUserGroups($userId, $groups) {
    global $conn;
    
    // Ensure groups is a JSON string
    $groups = array_values(array_unique($groups));
    $groupsJson = json_encode($groups);
    
    // Use JSON_SET to properly update the JSON column
    $stmt = $conn->prepare("UPDATE users SET `groups` = ? WHERE id = ?");
    $stmt->bind_param("si", $groupsJson, $userId);
    
    return $stmt->execute();
}

/**
 * Update role to module mapping
 * @param string $role Role name
 * @param array $modules Array of modules to assign to the role
 * @return bool True on success, false on failure
 */
function updateRoleModules($role, $modules) {
    global $conn;
    
    // Get all users with this role in their access_map
    $users = [];
    $roleEscaped = $conn->real_escape_string($role);
    
    // Use JSON_CONTAINS with proper JSON path
    $result = $conn->query("SELECT id, access_map FROM users WHERE JSON_CONTAINS(JSON_KEYS(access_map), '\"$roleEscaped\"', '$')");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Decode access_map if it's a string
            if (is_string($row['access_map'])) {
                $row['access_map'] = json_decode($row['access_map'], true) ?: [];
            }
            $users[] = $row;
        }
    }
    
    // Update each user's access_map
    $success = true;
    foreach ($users as $user) {
        $accessMap = $user['access_map'];
        if (is_string($accessMap)) {
            $accessMap = json_decode($accessMap, true) ?: [];
        }
        
        $accessMap[$role] = $modules;
        $accessMapJson = json_encode($accessMap);
        
        $stmt = $conn->prepare("UPDATE users SET access_map = ? WHERE id = ?");
        $stmt->bind_param("si", $accessMapJson, $user['id']);
        $success = $success && $stmt->execute();
    }
    
    return $success;
}

/**
 * Check if user has access to a specific module
 * @param int $userId User ID
 * @param string $module Module name to check access for
 * @return bool True if user has access, false otherwise
 */
function hasAccess($userId, $module) {
    $accessModules = getUserEffectiveAccess($userId);
    return in_array($module, $accessModules);
}

/**
 * Get all users in a specific group
 * @param string $groupName Group name
 * @return array Array of user data
 */
function getUsersInGroup($groupName) {
    global $conn;
    $users = [];
    
    // Escape the group name and format it as a JSON string
    $groupEscaped = $conn->real_escape_string($groupName);
    $result = $conn->query("SELECT id, name, email FROM users WHERE JSON_CONTAINS(`groups`, '\"$groupEscaped\"')");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

/**
 * Get all roles for a specific group
 * @param string $groupName Group name
 * @return array Array of roles
 */
function getRolesForGroup($groupName) {
    global $conn;
    $roles = [];
    
    $groupEscaped = $conn->real_escape_string($groupName);
    $result = $conn->query("SELECT roles_map FROM users WHERE JSON_CONTAINS(`groups`, '\"$groupEscaped\"') LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rolesMap = json_decode($row['roles_map'] ?? '{}', true) ?: [];
        if (isset($rolesMap[$groupName]) && is_array($rolesMap[$groupName])) {
            $roles = $rolesMap[$groupName];
        }
    }
    
    return $roles;
}

/**
 * Check if a user is in a specific group
 * @param int $userId User ID
 * @param string $groupName Group name to check
 * @return bool True if user is in the group, false otherwise
 */
function isUserInGroup($userId, $groupName) {
    global $conn;
    
    $groupEscaped = $conn->real_escape_string($groupName);
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE id = ? AND JSON_CONTAINS(`groups`, '\"' ? '\"')");
    $stmt->bind_param("is", $userId, $groupEscaped);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

/**
 * Get all modules accessible by a specific role
 * @param string $role Role name
 * @return array Array of module names
 */
function getModulesForRole($role) {
    global $conn;
    $modules = [];
    
    $roleEscaped = $conn->real_escape_string($role);
    $result = $conn->query("SELECT access_map FROM users WHERE JSON_CONTAINS(JSON_KEYS(access_map), '\"$roleEscaped\"') LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $accessMap = json_decode($row['access_map'] ?? '{}', true) ?: [];
        if (isset($accessMap[$role]) && is_array($accessMap[$role])) {
            $modules = $accessMap[$role];
        }
    }
    
    return $modules;
}

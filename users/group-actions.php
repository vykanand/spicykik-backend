<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Include configuration and database connection
require_once(dirname(__DIR__) . '/config.php');

// Get action from request
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Initialize response array
$response = ['success' => false, 'message' => ''];

function loadGroups() {
    $groupsFile = __DIR__ . '/groups.json';
    if (file_exists($groupsFile)) {
        $groups_content = file_get_contents($groupsFile);
        $groups = $groups_content !== false ? json_decode($groups_content, true) : [];
        if (!is_array($groups)) $groups = [];
        $groups = array_filter($groups, fn($item) => is_array($item));
        $groups = array_values($groups);
        return $groups;
    }
    return [];
}

function saveGroups($groups) {
    $groups = array_filter($groups, fn($item) => is_array($item));
    $groups = array_values($groups);
    $groupsFile = __DIR__ . '/groups.json';
    if (file_put_contents($groupsFile, json_encode($groups, JSON_PRETTY_PRINT)) === false) {
        throw new Exception('Failed to save groups to file');
    }
}

function loadRoles() {
    $rolesFile = __DIR__ . '/roles.json';
    if (file_exists($rolesFile)) {
        $roles_content = file_get_contents($rolesFile);
        $roles = $roles_content !== false ? json_decode($roles_content, true) : [];
        if (!is_array($roles)) $roles = [];
        $roles = array_filter($roles, fn($item) => is_array($item));
        $roles = array_values($roles);
        return $roles;
    }
    return [];
}

function saveRoles($roles) {
    $roles = array_filter($roles, fn($item) => is_array($item));
    $roles = array_values($roles);
    $rolesFile = __DIR__ . '/roles.json';
    if (file_put_contents($rolesFile, json_encode($roles, JSON_PRETTY_PRINT)) === false) {
        throw new Exception('Failed to save roles to file');
    }
}

$groups_data = loadGroups();
$roles_data = loadRoles();

$group_name_to_id = [];
foreach ($groups_data as $group) {
    $group_name_to_id[$group['name']] = $group['id'];
}

$role_name_to_id = [];
foreach ($roles_data as $role) {
    $role_name_to_id[$role['name']] = $role['id'];
}

$group_id_to_name = array_flip($group_name_to_id);
$role_id_to_name = array_flip($role_name_to_id);

try {
    switch ($action) {
        case 'getGroups':
            $groups = loadGroups();
            $response = [
                'success' => true,
                'groups' => $groups
            ];
            echo json_encode($response);
            exit();

        case 'getRoles':
            $roles = loadRoles();
            $response = [
                'success' => true,
                'roles' => $roles
            ];
            echo json_encode($response);
            exit();

        case 'getAllModules':
            // Get all available modules from database
            $modules = [];
            $result = $db->query("SELECT nav, urn FROM erpz.navigation");
            if (!$result) {
                $response = [
                    'success' => false,
                    'message' => 'Database query failed: ' . $db->error
                ];
                echo json_encode($response);
                exit();
            }
            while ($row = $result->fetch_assoc()) {
                // Use nav as key, assuming unique
                $modules[] = ['key' => $row['nav'], 'label' => $row['nav']];
            }
            $result->free();

            $response = [
                'success' => true,
                'modules' => $modules
            ];
            echo json_encode($response);
            exit();

        case 'getRoleModules':
            $roleId = (int)($_GET['roleId'] ?? 0);
            if (!$roleId) {
                echo "<div class='alert alert-warning'>Please select a role</div>";
                exit();
            }

            // Get all available modules from database
            $modules = [];
            $result = $db->query("SELECT nav, urn FROM erpz.navigation");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Map urn to simple key (basename without extension)
                    $key = pathinfo($row['urn'], PATHINFO_FILENAME);
                    $modules[$key] = $row['nav'];
                }
                $result->free();
            }

            // Get role's current module access
            $roles = loadRoles();
            $role = array_filter($roles, fn($r) => $r['id'] == $roleId);
            $role = reset($role);
            $allowedModules = $role['modules'] ?? [];

            // Output module checkboxes
            echo "<div class='list-group'>";
            foreach ($modules as $key => $label) {
                $isChecked = in_array($key, $allowedModules) ? 'checked' : '';
                echo "<div class='module-item d-flex justify-content-between align-items-center'>";
                echo "<span>{$label}</span>";
                echo "<div class='form-check form-switch'>";
                echo "<input class='form-check-input' type='checkbox' ";
                echo "onchange='saveModuleAccess({$roleId}, \"{$key}\", this.checked)' ";
                echo "{$isChecked}>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
            exit();

        case 'saveGroup':
            $name = trim($_POST['name'] ?? '');
            $roles = json_decode($_POST['roles'] ?? '[]', true) ?? [];
            $roles = array_map('trim', $roles);

            if (empty($name)) {
                throw new Exception('Group name is required');
            }

            $groups = loadGroups();

            // Check if group exists
            foreach ($groups as $group) {
                if ($group['name'] === $name) {
                    throw new Exception('A group with this name already exists');
                }
            }

            // Add new group
            $newId = count($groups) > 0 ? max(array_column($groups, 'id')) + 1 : 1;
            $groups[] = [
                'id' => $newId,
                'name' => $name,
                'roles' => $roles
            ];

            saveGroups($groups);

            $response = [
                'success' => true,
                'message' => 'Group created successfully',
                'id' => $newId
            ];
            break;

        case 'editGroup':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $roles = json_decode($_POST['roles'] ?? '[]', true) ?? [];
            $roles = array_map('trim', $roles);

            if (!$id) {
                throw new Exception('Invalid group ID');
            }
            if (empty($name)) {
                throw new Exception('Group name is required');
            }

            $groups = loadGroups();

            // Check if group exists and find it
            $found = false;
            foreach ($groups as &$group) {
                if ($group['id'] == $id) {
                    $found = true;
                    // Check if name is unique (excluding current)
                    foreach ($groups as $g) {
                        if ($g['id'] != $id && $g['name'] === $name) {
                            throw new Exception('A group with this name already exists');
                        }
                    }
                    $group['name'] = $name;
                    $group['roles'] = $roles;
                    break;
                }
            }

            if (!$found) {
                throw new Exception('Group not found');
            }

            saveGroups($groups);

            $response = [
                'success' => true,
                'message' => 'Group updated successfully'
            ];
            break;
            
        case 'saveRole':
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                throw new Exception('Role name is required');
            }
            
            $roles = loadRoles();
            
            // Check if role exists
            foreach ($roles as $role) {
                if ($role['name'] === $name) {
                    throw new Exception('A role with this name already exists');
                }
            }
            
            // Add new role
            $newId = count($roles) > 0 ? max(array_column($roles, 'id')) + 1 : 1;
            $roles[] = [
                'id' => $newId,
                'name' => $name,
                'description' => $description
            ];
            
            saveRoles($roles);
            
            $response = [
                'success' => true,
                'message' => 'Role created successfully',
                'id' => $newId
            ];
            break;
            
        case 'updateRoleModules':
            $roleId = (int)($_POST['roleId'] ?? 0);
            $modules = json_decode($_POST['modules'] ?? '[]', true) ?? [];
            $modules = array_map('trim', $modules);
            $modules = array_values($modules); // ensure indexed array

            if (!$roleId) {
                throw new Exception('Invalid role ID');
            }

            $roles = loadRoles();
            $found = false;
            foreach ($roles as &$role) {
                if ($role['id'] == $roleId) {
                    $role['modules'] = $modules;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new Exception('Role not found');
            }

            saveRoles($roles);

            $response = [
                'success' => true,
                'message' => 'Role modules updated'
            ];
            break;
            
        case 'deleteGroup':
            $groupId = (int)($_POST['id'] ?? 0);
            if (!$groupId) {
                throw new Exception('Invalid group ID');
            }

            $groups = loadGroups();
            $roles = loadRoles();

            // Find the group name before deleting
            $groupToDelete = array_filter($groups, fn($g) => $g['id'] == $groupId);
            $groupToDelete = reset($groupToDelete);
            $groupName = $groupToDelete['name'] ?? '';

            $groups = array_filter($groups, fn($g) => $g['id'] != $groupId);
            $groups = array_values($groups); // reindex

            saveGroups($groups);

            // Remove group from users table and recalculate access
            // For scalability with millions of users, use batch processing
            $batchSize = 1000;
            $offset = 0;

            do {
                // Get batch of users who have this group
                $usersQuery = $db->prepare("SELECT id, user_groups, user_roles FROM users WHERE JSON_CONTAINS(user_groups, JSON_QUOTE(?)) LIMIT ? OFFSET ?");
                $usersQuery->bind_param('sii', $groupName, $batchSize, $offset);
                $usersQuery->execute();
                $result = $usersQuery->get_result();

                $usersToUpdate = [];
                while ($user = $result->fetch_assoc()) {
                    $user_groups = json_decode($user['user_groups'] ?? '[]', true);
                    $user_roles = json_decode($user['user_roles'] ?? '[]', true);

                    // Remove group from user_groups
                    $user_groups = array_filter($user_groups, fn($g) => $g != $groupName);
                    $user_groups = array_values($user_groups);

                    // Recalculate modules
                    $allRoles = [];
                    foreach ($user_groups as $remainingGroupName) {
                        foreach ($groups as $group) {
                            if ($group['name'] == $remainingGroupName) {
                                $allRoles = array_merge($allRoles, $group['roles']);
                                break;
                            }
                        }
                    }
                    $allRoles = array_merge($allRoles, $user_roles);
                    $allRoles = array_unique($allRoles);

                    $modules = [];
                    foreach ($allRoles as $roleName) {
                        foreach ($roles as $role) {
                            if ($role['name'] == $roleName) {
                                $modules = array_merge($modules, $role['modules'] ?? []);
                                break;
                            }
                        }
                    }
                    $modules = array_unique($modules);

                    $usersToUpdate[] = [
                        'id' => $user['id'],
                        'user_groups' => json_encode($user_groups),
                        'user_roles' => json_encode($user_roles),
                        'access' => json_encode(array_values($modules))
                    ];
                }

                // Batch update users
                if (!empty($usersToUpdate)) {
                    $updateStmt = $db->prepare("UPDATE users SET user_groups = ?, user_roles = ?, access = ? WHERE id = ?");
                    foreach ($usersToUpdate as $userUpdate) {
                        $updateStmt->bind_param('sssi',
                            $userUpdate['user_groups'],
                            $userUpdate['user_roles'],
                            $userUpdate['access'],
                            $userUpdate['id']
                        );
                        $updateStmt->execute();
                    }
                    $updateStmt->close();
                }

                $offset += $batchSize;
            } while ($result->num_rows == $batchSize);

            $usersQuery->close();

            $response = [
                'success' => true,
                'message' => 'Group deleted successfully'
            ];
            break;
            
        case 'deleteRole':
            $roleId = (int)($_POST['id'] ?? 0);
            if (!$roleId) {
                throw new Exception('Invalid role ID');
            }

            $roles = loadRoles();
            $groups = loadGroups();

            // Find the role name before deleting
            $roleToDelete = array_filter($roles, fn($r) => $r['id'] == $roleId);
            $roleToDelete = reset($roleToDelete);
            $roleName = $roleToDelete['name'] ?? '';

            $roles = array_filter($roles, fn($r) => $r['id'] != $roleId);
            $roles = array_values($roles); // reindex

            saveRoles($roles);

            // Remove role from groups.json (groups have role names)
            $groupsChanged = false;
            foreach ($groups as &$group) {
                if (isset($group['roles']) && in_array($roleName, $group['roles'])) {
                    $group['roles'] = array_filter($group['roles'], fn($r) => $r != $roleName);
                    $group['roles'] = array_values($group['roles']); // reindex
                    $groupsChanged = true;
                }
            }
            if ($groupsChanged) {
                saveGroups($groups);
            }

            // Remove role from users table and recalculate access
            // For scalability with millions of users, use batch processing
            $batchSize = 1000;
            $offset = 0;

            do {
                // Get batch of users who have this role
                $usersQuery = $db->prepare("SELECT id, user_groups, user_roles FROM users WHERE JSON_CONTAINS(user_roles, JSON_QUOTE(?)) LIMIT ? OFFSET ?");
                $usersQuery->bind_param('sii', $roleName, $batchSize, $offset);
                $usersQuery->execute();
                $result = $usersQuery->get_result();

                $usersToUpdate = [];
                while ($user = $result->fetch_assoc()) {
                    $user_groups = json_decode($user['user_groups'] ?? '[]', true);
                    $user_roles = json_decode($user['user_roles'] ?? '[]', true);

                    // Remove role from user_roles
                    $user_roles = array_filter($user_roles, fn($r) => $r != $roleName);
                    $user_roles = array_values($user_roles);

                    // Recalculate modules
                    $allRoles = [];
                    foreach ($user_groups as $groupName) {
                        foreach ($groups as $group) {
                            if ($group['name'] == $groupName) {
                                $allRoles = array_merge($allRoles, $group['roles']);
                                break;
                            }
                        }
                    }
                    $allRoles = array_merge($allRoles, $user_roles);
                    $allRoles = array_unique($allRoles);

                    $modules = [];
                    foreach ($allRoles as $remainingRoleName) {
                        foreach ($roles as $role) {
                            if ($role['name'] == $remainingRoleName) {
                                $modules = array_merge($modules, $role['modules'] ?? []);
                                break;
                            }
                        }
                    }
                    $modules = array_unique($modules);

                    $usersToUpdate[] = [
                        'id' => $user['id'],
                        'user_groups' => json_encode($user_groups),
                        'user_roles' => json_encode($user_roles),
                        'access' => json_encode(array_values($modules))
                    ];
                }

                // Batch update users
                if (!empty($usersToUpdate)) {
                    $updateStmt = $db->prepare("UPDATE users SET user_groups = ?, user_roles = ?, access = ? WHERE id = ?");
                    foreach ($usersToUpdate as $userUpdate) {
                        $updateStmt->bind_param('sssi',
                            $userUpdate['user_groups'],
                            $userUpdate['user_roles'],
                            $userUpdate['access'],
                            $userUpdate['id']
                        );
                        $updateStmt->execute();
                    }
                    $updateStmt->close();
                }

                $offset += $batchSize;
            } while ($result->num_rows == $batchSize);

            $usersQuery->close();

            $response = [
                'success' => true,
                'message' => 'Role deleted successfully'
            ];
            break;

        case 'getUserAssignment':
            $userId = (int)($_GET['userId'] ?? 0);
            if (!$userId) {
                throw new Exception('Invalid user ID');
            }

            $result = $db->query("SELECT user_groups, user_roles, access FROM users WHERE id = $userId");
            if (!$result) {
                throw new Exception('Database query failed');
            }
            $row = $result->fetch_assoc();
            $result->free();

            $user_groups = json_decode($row['user_groups'] ?? '[]', true);
            $user_roles = json_decode($row['user_roles'] ?? '[]', true);

            $groups = [];
            foreach ($user_groups as $name) {
                if (isset($group_name_to_id[$name])) {
                    $groups[] = $group_name_to_id[$name];
                }
            }

            $roles = [];
            foreach ($user_roles as $name) {
                if (isset($role_name_to_id[$name])) {
                    $roles[] = $role_name_to_id[$name];
                }
            }

            $access = json_decode($row['access'] ?? '[]', true);
            if (is_array($access)) {
                // Check if it's an array of modules or an object with modules key
                if (isset($access['modules']) && is_array($access['modules'])) {
                    $modules = $access['modules'];
                } else {
                    // Assume it's a direct array of modules
                    $modules = $access;
                }
            } else {
                $modules = [];
            }

            $response = [
                'success' => true,
                'groups' => $groups,
                'roles' => $roles,
                'modules' => $modules
            ];
            echo json_encode($response);
            exit();

        case 'getAssignedModules':
            $groups = json_decode($_POST['groups'] ?? '[]', true) ?? [];
            $roles = json_decode($_POST['roles'] ?? '[]', true) ?? [];

            $allRoles = [];
            $allGroups = loadGroups();
            $allRolesData = loadRoles();

            // Get roles from selected groups
            foreach ($groups as $groupId) {
                foreach ($allGroups as $group) {
                    if ($group['id'] == $groupId) {
                        $allRoles = array_merge($allRoles, $group['roles']);
                        break;
                    }
                }
            }

            // Add direct roles (convert IDs to names)
            $roleNames = [];
            foreach ($roles as $roleId) {
                if (isset($role_id_to_name[$roleId])) {
                    $roleNames[] = $role_id_to_name[$roleId];
                }
            }
            $allRoles = array_merge($allRoles, $roleNames);
            $allRoles = array_unique($allRoles);

            // Get modules from roles
            $modules = [];
            foreach ($allRoles as $roleName) {
                foreach ($allRolesData as $role) {
                    if ($role['name'] == $roleName) {
                        $modules = array_merge($modules, $role['modules'] ?? []);
                        break;
                    }
                }
            }
            $modules = array_unique($modules);
            $modules = array_values($modules); // ensure indexed array

            $response = [
                'success' => true,
                'modules' => $modules
            ];
            echo json_encode($response);
            exit();

        case 'saveUserAssignment':
            $userId = (int)($_POST['userId'] ?? 0);
            $groups = json_decode($_POST['groups'] ?? '[]', true) ?? [];
            $roles = json_decode($_POST['roles'] ?? '[]', true) ?? [];

            if (!$userId) {
                throw new Exception('Invalid user ID');
            }

            // Calculate assigned modules
            $allRoles = [];
            $allGroups = loadGroups();
            $allRolesData = loadRoles();

            // Get roles from selected groups
            foreach ($groups as $groupId) {
                foreach ($allGroups as $group) {
                    if ($group['id'] == $groupId) {
                        $allRoles = array_merge($allRoles, $group['roles']);
                        break;
                    }
                }
            }

            // Add direct roles (convert IDs to names)
            $roleNames = [];
            foreach ($roles as $roleId) {
                if (isset($role_id_to_name[$roleId])) {
                    $roleNames[] = $role_id_to_name[$roleId];
                }
            }
            $allRoles = array_merge($allRoles, $roleNames);
            $allRoles = array_unique($allRoles);

            // Get modules from roles
            $modules = [];
            foreach ($allRoles as $roleName) {
                foreach ($allRolesData as $role) {
                    if ($role['name'] == $roleName) {
                        $modules = array_merge($modules, $role['modules'] ?? []);
                        break;
                    }
                }
            }
            $modules = array_unique($modules);

            $user_groups = [];
            foreach ($groups as $id) {
                if (isset($group_id_to_name[$id])) {
                    $user_groups[] = $group_id_to_name[$id];
                }
            }

            $user_roles = [];
            foreach ($roles as $id) {
                if (isset($role_id_to_name[$id])) {
                    $user_roles[] = $role_id_to_name[$id];
                }
            }

            $user_groups_json = json_encode($user_groups);
            $user_roles_json = json_encode($user_roles);
            $access_json = json_encode(array_values($modules));

            // Debug logs for update
            error_log("Updating user access for user ID: $userId");
            error_log("User groups JSON: $user_groups_json");
            error_log("User roles JSON: $user_roles_json");
            error_log("Access JSON: $access_json");

            $stmt = $db->prepare("UPDATE users SET user_groups = ?, user_roles = ?, access = ? WHERE id = ?");
            $stmt->bind_param('sssi', $user_groups_json, $user_roles_json, $access_json, $userId);
            if (!$stmt->execute()) {
                error_log("Failed to update user access for user ID: $userId - " . $stmt->error);
                throw new Exception('Failed to update user access');
            }
            $stmt->close();

            $response = [
                'success' => true,
                'message' => 'User assignment saved successfully'
            ];
            break;

        case 'deleteModule':
            $moduleKey = trim($_POST['moduleKey'] ?? '');
            if (empty($moduleKey)) {
                throw new Exception('Module key is required');
            }
            // Remove module from roles.json
            $roles = loadRoles();
            $rolesChanged = false;
            foreach ($roles as &$role) {
                if (isset($role['modules']) && in_array($moduleKey, $role['modules'])) {
                    $role['modules'] = array_filter($role['modules'], fn($m) => $m !== $moduleKey);
                    $role['modules'] = array_values($role['modules']); // reindex
                    $rolesChanged = true;
                }
            }
            if ($rolesChanged) {
                saveRoles($roles);
            }
            // Remove module from groups.json roles
            $groups = loadGroups();
            $groupsChanged = false;
            foreach ($groups as &$group) {
                if (isset($group['roles'])) {
                    $newRoles = [];
                    foreach ($group['roles'] as $roleName) {
                        $role = array_filter($roles, fn($r) => $r['name'] == $roleName);
                        $role = reset($role);
                        if ($role && isset($role['modules']) && in_array($moduleKey, $role['modules'])) {
                            // role still has module, keep it
                            $newRoles[] = $roleName;
                        } else if ($role && isset($role['modules'])) {
                            // role no longer has module, remove it
                            // do not add to newRoles
                        } else {
                            // role not found, keep it
                            $newRoles[] = $roleName;
                        }
                    }
                    if (count($newRoles) !== count($group['roles'])) {
                        $group['roles'] = $newRoles;
                        $group['roles'] = array_values($group['roles']); // reindex
                        $groupsChanged = true;
                    }
                }
            }
            if ($groupsChanged) {
                saveGroups($groups);
            }
            // Recalculate access for all users
            $batchSize = 1000;
            $offset = 0;
            do {
                $usersQuery = $db->prepare("SELECT id, user_groups, user_roles FROM users LIMIT ? OFFSET ?");
                $usersQuery->bind_param('ii', $batchSize, $offset);
                $usersQuery->execute();
                $result = $usersQuery->get_result();

                $usersToUpdate = [];
                while ($user = $result->fetch_assoc()) {
                    $user_groups = json_decode($user['user_groups'] ?? '[]', true);
                    $user_roles = json_decode($user['user_roles'] ?? '[]', true);

                    // Recalculate modules
                    $allRoles = [];
                    foreach ($user_groups as $groupName) {
                        foreach ($groups as $group) {
                            if ($group['name'] == $groupName) {
                                $allRoles = array_merge($allRoles, $group['roles']);
                                break;
                            }
                        }
                    }
                    $allRoles = array_merge($allRoles, $user_roles);
                    $allRoles = array_unique($allRoles);

                    $modules = [];
                    foreach ($allRoles as $roleName) {
                        foreach ($roles as $role) {
                            if ($role['name'] == $roleName) {
                                $modules = array_merge($modules, $role['modules'] ?? []);
                                break;
                            }
                        }
                    }
                    $modules = array_unique($modules);
                    $modules = array_values($modules); // ensure indexed array

                    $usersToUpdate[] = [
                        'id' => $user['id'],
                        'access' => json_encode(array_values($modules))
                    ];
                }

                // Batch update users
                if (!empty($usersToUpdate)) {
                    $updateStmt = $db->prepare("UPDATE users SET access = ? WHERE id = ?");
                    foreach ($usersToUpdate as $userUpdate) {
                        $updateStmt->bind_param('si',
                            $userUpdate['access'],
                            $userUpdate['id']
                        );
                        $updateStmt->execute();
                    }
                    $updateStmt->close();
                }

                $offset += $batchSize;
            } while ($result->num_rows == $batchSize);

            $usersQuery->close();

            $response = ['success' => true, 'message' => 'Module and references deleted successfully'];
            echo json_encode($response);
            exit();

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Return JSON response
echo json_encode($response);

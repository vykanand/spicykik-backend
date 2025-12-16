<?php
include "config.php";

// Test the cleanup function
cleanupModuleReferences("Lmd", $db);

function cleanupModuleReferences($moduleKey, $db) {
    try {
        // Debug: Log the module key being processed
        error_log("Starting cleanup for module: " . $moduleKey);

        // Load roles and groups data
        $rolesFile = __DIR__ . '/users/roles.json';
        $groupsFile = __DIR__ . '/users/groups.json';

        $roles = file_exists($rolesFile) ? json_decode(file_get_contents($rolesFile), true) : [];
        $groups = file_exists($groupsFile) ? json_decode(file_get_contents($groupsFile), true) : [];

        error_log("Loaded " . count($roles) . " roles and " . count($groups) . " groups");

        $rolesChanged = false;
        $groupsChanged = false;

        // Remove module from roles.json
        foreach ($roles as &$role) {
            if (isset($role['modules']) && in_array($moduleKey, $role['modules'])) {
                $role['modules'] = array_filter($role['modules'], fn($m) => $m !== $moduleKey);
                $rolesChanged = true;
                error_log("Removed $moduleKey from role: " . $role['name']);
            }
        }

        // Remove module from groups.json roles
        // Note: groups.json stores role names, not IDs
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
                    $groupsChanged = true;
                    error_log("Updated group: " . $group['name']);
                }
            }
        }

        // Save updated roles and groups
        if ($rolesChanged) {
            file_put_contents($rolesFile, json_encode($roles, JSON_PRETTY_PRINT));
            error_log("Saved updated roles.json");
        }
        if ($groupsChanged) {
            file_put_contents($groupsFile, json_encode($groups, JSON_PRETTY_PRINT));
            error_log("Saved updated groups.json");
        }

        // Update users table - remove module from access and recalculate based on remaining roles
        // For scalability with millions of users, use batch processing and only update users who have the module
        $batchSize = 1000;
        $offset = 0;

        do {
            // Get batch of users who have the module in their access
            $usersQuery = $db->prepare("SELECT id, access, user_groups, user_roles FROM users WHERE JSON_CONTAINS(access, JSON_QUOTE(?)) LIMIT ? OFFSET ?");
            $usersQuery->bind_param('sii', $moduleKey, $batchSize, $offset);
            $usersQuery->execute();
            $result = $usersQuery->get_result();

            error_log("Found " . $result->num_rows . " users with module $moduleKey");

            $usersToUpdate = [];
            while ($user = $result->fetch_assoc()) {
                $access = json_decode($user['access'] ?? '[]', true);
                $userGroups = json_decode($user['user_groups'] ?? '[]', true);
                $userRoles = json_decode($user['user_roles'] ?? '[]', true);

                // Remove module from access
                if (($key = array_search($moduleKey, $access)) !== false) {
                    unset($access[$key]);
                    error_log("Removed $moduleKey from user " . $user['id']);
                }

                // Recalculate access based on remaining roles and groups
                $allRoles = [];
                $allGroups = $groups;

                // Get roles from selected groups
                foreach ($userGroups as $groupName) {
                    foreach ($allGroups as $group) {
                        if ($group['name'] == $groupName) {
                            $allRoles = array_merge($allRoles, $group['roles']);
                            break;
                        }
                    }
                }

                // Add direct roles
                foreach ($userRoles as $roleName) {
                    foreach ($roles as $role) {
                        if ($role['name'] == $roleName) {
                            $allRoles[] = $role['id'];
                            break;
                        }
                    }
                }
                $allRoles = array_unique($allRoles);

                // Get modules from roles
                $newModules = [];
                foreach ($allRoles as $roleId) {
                    foreach ($roles as $role) {
                        if ($role['id'] == $roleId) {
                            $newModules = array_merge($newModules, $role['modules'] ?? []);
                            break;
                        }
                    }
                }
                $newModules = array_unique($newModules);

                // Update with recalculated access
                $usersToUpdate[] = [
                    'id' => $user['id'],
                    'access' => json_encode(array_values($newModules)),
                    'user_groups' => json_encode($userGroups),
                    'user_roles' => json_encode($userRoles)
                ];
            }

            // Batch update users
            if (!empty($usersToUpdate)) {
                $updateStmt = $db->prepare("UPDATE users SET access = ?, user_groups = ?, user_roles = ? WHERE id = ?");
                foreach ($usersToUpdate as $userUpdate) {
                    $updateStmt->bind_param('sssi',
                        $userUpdate['access'],
                        $userUpdate['user_groups'],
                        $userUpdate['user_roles'],
                        $userUpdate['id']
                    );
                    $updateStmt->execute();
                }
                $updateStmt->close();
                error_log("Updated " . count($usersToUpdate) . " users");
            }

            $offset += $batchSize;
        } while ($result->num_rows == $batchSize);

        $usersQuery->close();

    } catch (Exception $e) {
        // Log error but don't fail the deletion
        error_log("Error cleaning up module references: " . $e->getMessage());
    }
}

echo "Cleanup test completed. Check error logs for details.";
?>

<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/RoleManager.php';

// Initialize RoleManager
$roleManager = new RoleManager();

// Handle AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'] ?? '';
        $response = [];
        
        switch ($action) {
            case 'get_roles':
                $response = $roleManager->getRoles();
                break;
                
            case 'get_groups':
                $response = $roleManager->getGroups();
                break;
                
            case 'get_permissions':
                $response = $roleManager->getPermissions();
                break;
                
            case 'create_group':
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                if (empty($name)) {
                    throw new Exception('Group name is required');
                }
                $groupId = $roleManager->createGroup($name, $description);
                $response = ['success' => true, 'id' => $groupId];
                break;
                
            default:
                throw new Exception('Invalid action');
        }
        
        echo json_encode(['success' => true, 'data' => $response]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role & Group Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { padding-top: 20px; }
        .sidebar { position: sticky; top: 20px; }
        .nav-link { color: #333; }
        .nav-link.active { font-weight: bold; color: #0d6efd; }
        .card { margin-bottom: 20px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .list-group-item { cursor: pointer; }
        .list-group-item:hover { background-color: #f8f9fa; }
        .list-group-item.active { background-color: #0d6efd; border-color: #0d6efd; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Security Management</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#roles" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                            <i class="bi bi-people-fill me-2"></i> Roles
                        </a>
                        <a href="#groups" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="bi bi-collection-fill me-2"></i> Groups
                        </a>
                        <a href="#permissions" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="bi bi-shield-lock-fill me-2"></i> Permissions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="tab-content">
                    <!-- Roles Tab -->
                    <div class="tab-pane fade show active" id="roles">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h2>Roles</h2>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                <i class="bi bi-plus-circle"></i> Add Role
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="rolesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Filled by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Groups Tab -->
                    <div class="tab-pane fade" id="groups">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h2>Groups</h2>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                                <i class="bi bi-plus-circle"></i> Add Group
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="groupsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Filled by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Permissions Tab -->
                    <div class="tab-pane fade" id="permissions">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h2>Permissions</h2>
                        </div>
                        <div class="row" id="permissionsContainer">
                            <!-- Filled by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Group Modal -->
    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addGroupForm">
                        <div class="mb-3">
                            <label for="groupName" class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="groupName" required>
                        </div>
                        <div class="mb-3">
                            <label for="groupDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="groupDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveGroupBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Wait for the document to be fully loaded
        $(function() {
            // Initialize tooltips if any exist
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            if (tooltipTriggerList.length > 0) {
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
            
            // Load initial data
            if (typeof loadRoles === 'function') loadRoles();
            if (typeof loadGroups === 'function') loadGroups();
            if (typeof loadPermissions === 'function') loadPermissions();

            // Save group
            $('#saveGroupBtn').click(function() {
                const name = $('#groupName').val();
                const description = $('#groupDescription').val();
                
                if (!name) {
                    alert('Group name is required');
                    return;
                }
                
                $.post('//' + window.location.host + '/security/admin_new.php', {
                    action: 'create_group',
                    name: name,
                    description: description
                }, function(response) {
                    if (response.success) {
                        $('#addGroupModal').modal('hide');
                        loadGroups();
                        $('#addGroupForm')[0].reset();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error'));
                    }
                }).fail(function(xhr) {
                    const error = xhr.responseJSON || {};
                    alert('Error: ' + (error.message || 'Failed to save group'));
                });
            });
            
            // Load roles
            function loadRoles() {
                $.post('//' + window.location.host + '/security/admin_new.php', { action: 'get_roles' }, function(response) {
                    if (response.success) {
                        const tbody = $('#rolesTable tbody');
                        tbody.empty();
                        
                        response.data.forEach(role => {
                            tbody.append(`
                                <tr>
                                    <td>${role.id}</td>
                                    <td>${role.name}</td>
                                    <td>${role.description || ''}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                });
            }
            
            // Load groups
            function loadGroups() {
                $.post('//' + window.location.host + '/security/admin_new.php', { action: 'get_groups' }, function(response) {
                    if (response.success) {
                        const tbody = $('#groupsTable tbody');
                        tbody.empty();
                        
                        response.data.forEach(group => {
                            tbody.append(`
                                <tr>
                                    <td>${group.id}</td>
                                    <td>${group.name}</td>
                                    <td>${group.description || ''}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                });
            }
            
            // Load permissions
            function loadPermissions() {
                $.post('//' + window.location.host + '/security/admin_new.php', { action: 'get_permissions' }, function(response) {
                    if (response.success) {
                        const container = $('#permissionsContainer');
                        container.empty();
                        
                        // Group permissions by category
                        const categories = {};
                        response.data.forEach(perm => {
                            const parts = perm.name.split('_');
                            const category = parts.length > 1 ? parts[0] : 'General';
                            
                            if (!categories[category]) {
                                categories[category] = [];
                            }
                            categories[category].push(perm);
                        });
                        
                        // Create cards for each category
                        for (const [category, perms] of Object.entries(categories)) {
                            const card = `
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">${category.charAt(0).toUpperCase() + category.slice(1)}</h5>
                                        </div>
                                        <div class="list-group list-group-flush">
                                            ${perms.map(perm => `
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>${perm.name}</span>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="perm-${perm.id}" ${perm.enabled ? 'checked' : ''}>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.append(card);
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

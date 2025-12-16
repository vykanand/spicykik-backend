<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(dirname(__FILE__)));

// Include configuration and database connection
require_once(BASE_PATH . '/config.php');

// Use the existing database connection from config.php
if (isset($db) && $db->connect_errno === 0) {
    $conn = $db;
} else {
    die("Database connection failed: " . ($db->error ?? 'Unknown error'));
}

// Include group role functions
require_once(__DIR__ . '/includes/group-role-functions.php');

$pageTitle = "Manage User Access";

// Get all users
$users = [];
$result = $conn->query("SELECT id, name, email FROM users");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Load groups and roles for mapping
$groups_content = file_get_contents(__DIR__ . '/groups.json');
$groups = $groups_content !== false ? json_decode($groups_content, true) : [];
if (!is_array($groups)) $groups = [];

$roles_content = file_get_contents(__DIR__ . '/roles.json');
$roles = $roles_content !== false ? json_decode($roles_content, true) : [];
if (!is_array($roles)) $roles = [];

// Create mappings
$group_name_to_id = [];
foreach ($groups as $group) {
    $group_name_to_id[$group['name']] = $group['id'];
}

$role_name_to_id = [];
foreach ($roles as $role) {
    $role_name_to_id[$role['name']] = $role['id'];
}

$group_id_to_name = array_flip($group_name_to_id);
$role_id_to_name = array_flip($role_name_to_id);

// Get all groups and roles
$groups_content2 = file_get_contents(__DIR__ . '/groups.json');
$groups = $groups_content2 !== false ? json_decode($groups_content2, true) : [];
if (!is_array($groups)) $groups = [];

$roles_content2 = file_get_contents(__DIR__ . '/roles.json');
$roles = $roles_content2 !== false ? json_decode($roles_content2, true) : [];
if (!is_array($roles)) $roles = [];

// Create role name to ID mapping
$roleNameToId = [];
foreach ($roles as $role) {
    $roleNameToId[$role['name']] = $role['id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $pageTitle; ?></title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
  />
  <link rel="stylesheet" href="../loader.css">

  <style>
    body {
      padding: 20px;
      background-color: #f8f9fa;
    }
    .card {
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      border: none;
    }
    .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #eee;
      font-weight: 500;
    }
    .checkbox-list {
      max-height: 200px;
      overflow-y: auto;
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
      padding: 0.5rem;
    }
    .checkbox-item {
      margin-bottom: 0.5rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>

    <tg onclick="history.back();" style="position: fixed; top: 20px; left: 20px; cursor: pointer; z-index: 9999;">
      <img src="./back-black.png" style="height: 40px; width: 40px;">
    </tg>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loadmodal" style="display: none;">
      <div class="loading">
        <div class="loading-bar"></div>
        <div class="loading-bar"></div>
        <div class="loading-bar"></div>
        <div class="loading-bar"></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>User Access Management</h5>
      </div>
      <div class="card-body">
        <form id="assignmentForm">
          <div id="message" class="alert" style="display:none;" role="alert"></div>
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="userSelect" class="form-label">Select User</label>
              <select class="form-select" id="userSelect" name="userId" required>
                <option value="">Choose a user...</option>
                <?php foreach ($users as $user): ?>
                  <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name'] . ' (' . $user['email'] . ')'); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Select Groups</label>
              <div class="checkbox-list" id="groupsList">
                <?php foreach ($groups as $group): ?>
                  <div class="checkbox-item">
                    <input type="checkbox" class="form-check-input" id="group_<?php echo $group['id']; ?>" value="<?php echo $group['id']; ?>">
                    <label class="form-check-label" for="group_<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Select Roles</label>
              <div class="checkbox-list" id="rolesList">
                <?php foreach ($roles as $role): ?>
                  <div class="checkbox-item">
                    <input type="checkbox" class="form-check-input" id="role_<?php echo $role['id']; ?>" value="<?php echo $role['id']; ?>">
                    <label class="form-check-label" for="role_<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-primary" id="saveBtn">Save Access</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>User Modules</h5>
      </div>
      <div class="card-body">
        <div id="modulesContainer">
          <p class="text-muted">Select a user and assign groups/roles to see accessible modules.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
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
        setTimeout(() => {
          window.parent.postMessage(document.body.scrollHeight, '*');
        }, 100);
      }
    }

    const groupsData = <?php echo json_encode($groups); ?>;
    const rolesData = <?php echo json_encode($roles); ?>;

    // Create a map of role name to id
    const roleNameToId = {};
    rolesData.forEach(role => {
      roleNameToId[role.name] = role.id;
    });

    // Save selected userId in localStorage to maintain state after reload
    const savedUserId = localStorage.getItem('selectedUserId');

    $(document).ready(function() {
      // Function to load user assignments
      function loadUserAssignments(userId) {
        showLoader();
        $.get('group-actions?action=getUserAssignment&userId=' + userId)
          .done(function(response) {
            hideLoader();
            console.log('Response:', response);
            if (response.success) {
              console.log('Setting groups:', response.groups);
              console.log('Setting roles:', response.roles);
              // Set selected groups
              if (response.groups && response.groups.length) {
                setCheckedBoxes('#groupsList input[type=checkbox]', response.groups);
              } else {
                setCheckedBoxes('#groupsList input[type=checkbox]', []);
              }
              // Set selected roles
              if (response.roles && response.roles.length) {
                setCheckedBoxes('#rolesList input[type=checkbox]', response.roles);
              } else {
                setCheckedBoxes('#rolesList input[type=checkbox]', []);
              }
              // Update modules based on current selection
              updateModules();
            } else {
              alert('Error: ' + response.message);
            }
          })
          .fail(function() {
            hideLoader();
            alert('Failed to load user access');
          });
      }

      // If saved userId exists, set it and load assignments
      if (savedUserId) {
        $('#userSelect').val(savedUserId);
        loadUserAssignments(savedUserId);
      }

      // Load current assignment when user is selected
      $('#userSelect').change(function() {
        const userId = $(this).val();
        if (userId) {
          // Save selected userId to localStorage
          localStorage.setItem('selectedUserId', userId);
          loadUserAssignments(userId);
        } else {
          // Uncheck all checkboxes
          $('#groupsList input[type=checkbox]').prop('checked', false);
          $('#rolesList input[type=checkbox]').prop('checked', false);
          $('#modulesContainer').html('<p class="text-muted">Select a user and assign groups/roles to see accessible modules.</p>');
          // Remove saved userId from localStorage
          localStorage.removeItem('selectedUserId');
        }
      });


      // Auto-select roles when groups are checked, but preserve user customizations
      $('#groupsList input[type=checkbox]').change(function() {
        const groupId = parseInt($(this).val());
        const isChecked = $(this).is(':checked');
        if (isChecked) {
          const group = groupsData.find(g => g.id === groupId);
          if (group && group.roles) {
            group.roles.forEach(roleName => {
              const roleId = roleNameToId[roleName];
              if (roleId) {
                // Only check role if not already checked by user customization
                if (!$('#role_' + roleId).data('user-customized')) {
                  $('#role_' + roleId).prop('checked', true);
                }
              }
            });
          }
        } else {
          // If group unchecked, uncheck roles that belong exclusively to this group
          const group = groupsData.find(g => g.id === groupId);
          if (group && group.roles) {
            group.roles.forEach(roleName => {
              const roleId = roleNameToId[roleName];
              if (roleId) {
                // Check if this role is not part of any other checked group
                let isInOtherGroup = false;
                $('#groupsList input[type=checkbox]:checked').each(function() {
                  const otherGroupId = parseInt($(this).val());
                  if (otherGroupId !== groupId) {
                    const otherGroup = groupsData.find(g => g.id === otherGroupId);
                    if (otherGroup && otherGroup.roles.includes(roleName)) {
                      isInOtherGroup = true;
                      return false; // break loop
                    }
                  }
                });
                if (!isInOtherGroup) {
                  $('#role_' + roleId).prop('checked', false);
                  $('#role_' + roleId).data('user-customized', false);
                }
              }
            });
          }
        }
        updateModules();
      });

      // Track user customizations on roles checkboxes and auto-uncheck groups if necessary
      $('#rolesList input[type=checkbox]').change(function() {
        $(this).data('user-customized', true);
        if (!$(this).is(':checked')) {
          const roleId = parseInt($(this).val());
          const role = rolesData.find(r => r.id === roleId);
          if (role) {
            groupsData.forEach(group => {
              if (group.roles && group.roles.includes(role.name)) {
                if ($('#group_' + group.id).is(':checked')) {
                  let hasOtherCheckedRole = false;
                  group.roles.forEach(rName => {
                    if (rName !== role.name) {
                      const rId = roleNameToId[rName];
                      if (rId && $('#role_' + rId).is(':checked')) {
                        hasOtherCheckedRole = true;
                      }
                    }
                  });
                  if (!hasOtherCheckedRole) {
                    $('#group_' + group.id).prop('checked', false);
                  }
                }
              }
            });
          }
        }
        updateModules();
      });

      // Save assignment
      $('#saveBtn').click(function() {
        const userId = $('#userSelect').val();
        const groups = getCheckedValues('#groupsList input[type=checkbox]');
        const roles = getCheckedValues('#rolesList input[type=checkbox]');

        if (!userId) {
          alert('Please select a user');
          return;
        }

        const accessData = {
          groups: groups,
          roles: roles
        };

        showLoader();
        $.post('group-actions', {
          action: 'saveUserAssignment',
          userId: userId,
          groups: JSON.stringify(groups),
          roles: JSON.stringify(roles)
        })
            .done(function(response) {
              hideLoader();
              if (response.success) {
                $('#message').removeClass('alert-danger').addClass('alert-success').text('Access saved successfully').show();
                // Reload the page after saving access
                loadUserAssignments(userId); // Reload assignments to update view
              } else {
                $('#message').removeClass('alert-success').addClass('alert-danger').text('Error: ' + response.message).show();
              }
            })
            .fail(function() {
              hideLoader();
              $('#message').removeClass('alert-success').addClass('alert-danger').text('Failed to save access').show();
            });
      });

      function updateModules() {
        const groups = getCheckedValues('#groupsList input[type=checkbox]');
        const roles = getCheckedValues('#rolesList input[type=checkbox]');
        console.log('Updating modules for groups:', groups, 'roles:', roles);

        $.post('group-actions', {
          action: 'getAssignedModules',
          groups: JSON.stringify(groups),
          roles: JSON.stringify(roles)
        })
        .done(function(response) {
          console.log('Modules response:', response);
          if (response.success) {
            displayModules(response.modules);
          } else {
            $('#modulesContainer').html('<p class="text-danger">Error: ' + response.message + '</p>');
          }
        })
        .fail(function() {
          $('#modulesContainer').html('<p class="text-danger">Failed to load modules</p>');
        });
      }

      function displayModules(modules) {
        if (!Array.isArray(modules) || modules.length === 0) {
          $('#modulesContainer').html('<p class="text-muted">No modules accessible.</p>');
          return;
        }

        let html = '<div class="d-flex flex-wrap gap-2">';
        modules.forEach(module => {
          html += `<span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">${module}</span>`;
        });
        html += '</div>';
        $('#modulesContainer').html(html);
      }

      function setCheckedBoxes(selector, values) {
        console.log('Setting checked for', selector, 'with values', values);
        $(selector).each(function() {
          const val = $(this).val();
          const parsed = parseInt(val);
          const checked = values.includes(parsed);
          console.log('Input val:', val, 'parsed:', parsed, 'includes:', checked);
          $(this).prop('checked', checked);
        });
      }

      function getCheckedValues(selector) {
        const values = [];
        $(selector + ':checked').each(function() {
          values.push(parseInt($(this).val()));
        });
        return values;
      }
    });
  </script>
</body>
</html>

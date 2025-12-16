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

$pageTitle = "Group & Role Manager";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_group') {
        $groupName = $_POST['group_name'] ?? '';
        $roles = $_POST['roles'] ?? [];
        
        if (!empty($groupName)) {
            // In a real implementation, you would save this to the database
            $_SESSION['flash_message'] = "Group '$groupName' saved successfully";
            header("Location: group-manager.php");
            exit();
        }
    }
}

// Get all users for the user assignment section
$users = [];
$result = $conn->query("SELECT id, name, email, user_groups FROM users");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $user_groups = json_decode($row['user_groups'] ?? '[]', true);
        $row['groups'] = $user_groups; // array of group names
        $users[] = $row;
    }
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
  <link rel="stylesheet" href="loader.css">

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
    .bootstrap-tagsinput {
      width: 100%;
      padding: 0.5rem;
      border-radius: 0.25rem;
      border: 1px solid #ced4da;
    }
    .bootstrap-tagsinput .tag {
      padding: 0.2rem 0.5rem;
      background: #007bff;
      color: white;
      border-radius: 3px;
      margin-right: 5px;
      margin-bottom: 5px;
      display: inline-block;
    }
    .bootstrap-tagsinput input {
      border: none;
      box-shadow: none;
      outline: none;
    }
    .module-item {
      padding: 0.5rem;
      border: 1px solid #eee;
      margin-bottom: 5px;
      border-radius: 4px;
    }
    .module-item:hover {
      background-color: #f8f9fa;
    }
    @media (max-width: 767.98px) {
      .card-body {
        padding: 1rem 0.5rem;
      }
      .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
      }
      h1.h3 {
        font-size: 1.5rem;
      }
    }
  </style>

  <!-- jQuery and Tag Input -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0"><?php echo $pageTitle; ?></h1>
      <a href="user-access.php" class="btn btn-primary">Manage User Access</a>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loadmodal" style="display: none;">
      <div class="loading">
        <div class="loading-bar"></div>
        <div class="loading-bar"></div>
        <div class="loading-bar"></div>
        <div class="loading-bar"></div>
      </div>
    </div>

    <div class="row g-3">
      <!-- Groups Section -->
      <div class="col-md-6">
        <div class="card">
          <div
            class="card-header d-flex justify-content-between align-items-center"
          >
            <span>Groups</span>
            <button
              class="btn btn-sm btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#addGroupModal"
            >
              <i class="fas fa-plus"></i> Add Group
            </button>
          </div>
          <div class="card-body" id="groupsList">
            <!-- Groups will be loaded here -->
          </div>
        </div>
      </div>

      <!-- Roles Section -->
      <div class="col-md-6">
        <div class="card">
          <div
            class="card-header d-flex justify-content-between align-items-center"
          >
            <span>Roles</span>
            <button
              class="btn btn-sm btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#addRoleModal"
            >
              <i class="fas fa-plus"></i> Add Role
            </button>
          </div>
          <div class="card-body" id="rolesList">
            <!-- Roles will be loaded here -->
          </div>
        </div>
      </div>
    </div>



    <!-- Add Group Modal -->
    <div class="modal fade" id="addGroupModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Group</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Group Name</label>
              <input
                type="text"
                class="form-control"
                id="newGroupName"
                placeholder="Enter group name"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Roles</label>
              <div id="groupRolesCheckboxes">
                <!-- Roles will be loaded here as checkboxes -->
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
            <button
              type="button"
              class="btn btn-primary"
              onclick="saveGroup()"
            >
              Save Group
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Group Modal -->
    <div class="modal fade" id="editGroupModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Group</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editGroupId">
            <div class="mb-3">
              <label class="form-label">Group Name</label>
              <input
                type="text"
                class="form-control"
                id="editGroupName"
                placeholder="Enter group name"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Roles</label>
              <div id="editGroupRolesCheckboxes">
                <!-- Roles will be loaded here as checkboxes -->
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
            <button
              type="button"
              class="btn btn-primary"
              onclick="saveEditGroup()"
            >
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Role</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Role Name</label>
              <input
                type="text"
                class="form-control"
                id="newRoleName"
                placeholder="Enter role name"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea
                class="form-control"
                id="roleDescription"
                rows="3"
                placeholder="Enter role description"
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
            <button
              type="button"
              class="btn btn-primary"
              onclick="saveRole()"
            >
              Save Role
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Manage Role Modules Modal -->
    <div class="modal fade" id="manageRoleModulesModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Manage Modules for Role</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body">
            <div id="assignedModules" class="mb-3">
              <!-- Assigned modules will be shown here -->
            </div>
            <div id="roleModulesList">
              <!-- Modules will be loaded here -->
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
            <button
              type="button"
              class="btn btn-primary"
              onclick="saveRoleModules()"
            >
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>

   <script>
      // Loading functions
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
          // Set window parent height after load
          setTimeout(() => {
            window.parent.postMessage(document.body.scrollHeight, '*');
          }, 100);
        }
      }

      // Populate the roles select input in add group modal as checkboxes
      function populateRoleSelect() {
        $.getJSON("group-actions?action=getRoles", function (response) {
          if (response.success) {
            let html = "";
            response.roles.forEach(function (role) {
              html += `<div class="form-check">
                <input class="form-check-input" type="checkbox" value="${role.name}" id="roleCheckbox_${role.id}">
                <label class="form-check-label" for="roleCheckbox_${role.id}">${role.name}</label>
              </div>`;
            });
            $("#groupRolesCheckboxes").html(html);
          }
        }).fail(function() {
          alert("Failed to load roles");
        });
      }

      // Populate roles for edit group modal
      function populateEditRoleSelect(selectedRoles) {
        $.getJSON("group-actions?action=getRoles", function (response) {
          if (response.success) {
            let html = "";
            response.roles.forEach(function (role) {
              const isChecked = selectedRoles.includes(role.name) ? 'checked' : '';
              html += `<div class="form-check">
                <input class="form-check-input" type="checkbox" value="${role.name}" id="editRoleCheckbox_${role.id}" ${isChecked}>
                <label class="form-check-label" for="editRoleCheckbox_${role.id}">${role.name}</label>
              </div>`;
            });
            $("#editGroupRolesCheckboxes").html(html);
          }
        }).fail(function() {
          alert("Failed to load roles");
        });
      }

      // Edit group
      function editGroup(groupId, groupName, rolesStr) {
        const roles = rolesStr ? rolesStr.split(',').filter(r => r) : [];
        $("#editGroupId").val(groupId);
        $("#editGroupName").val(groupName);
        populateEditRoleSelect(roles);
        $("#editGroupModal").modal("show");
      }

      // Save edited group
      function saveEditGroup() {
        const groupId = $("#editGroupId").val();
        const groupName = $("#editGroupName").val();
        const roles = [];
        $("#editGroupRolesCheckboxes input[type=checkbox]:checked").each(function () {
          roles.push($(this).val());
        });

        if (!groupName) {
          alert("Please enter a group name");
          return;
        }

        $.post(
          "group-actions",
          {
            action: "editGroup",
            id: groupId,
            name: groupName,
            roles: JSON.stringify(roles),
          },
          function (response) {
            if (response.success) {
              $("#editGroupModal").modal("hide");
              loadGroups();
            } else {
              alert("Error: " + (response.message || "Failed to update group"));
            }
          },
          "json"
        ).fail(function() {
          alert("Request failed");
        });
      }

      // Initialize tag inputs and tooltips
      $(document).ready(function () {
        var tooltipTriggerList = [].slice.call(
          document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Show loader on page load
        showLoader();

        var initialLoads = 2;

        // Load initial data
        loadGroups().then(function() {
          initialLoads--;
          if (initialLoads === 0) {
            hideLoader();
          }
        });
        loadRoles().then(function() {
          initialLoads--;
          if (initialLoads === 0) {
            hideLoader();
          }
        });

      // Populate role select when add group modal is shown
      $('#addGroupModal').on('show.bs.modal', function () {
        populateRoleSelect();
      });
      });

      // Load groups
      function loadGroups() {
        return $.getJSON("group-actions?action=getGroups").then(function (response) {
          if (response.success) {
            let html = "";
            response.groups.forEach(function (group) {
              html += "<div class='group-item border rounded p-3 mb-3 shadow-sm' data-group-id='" + group.id + "'>";
              html += "<div class='d-flex justify-content-between align-items-center'>";
              html += "<div>";
              html += "<strong class='fs-5'>" + group.name + "</strong><br>";
              if (group.roles && group.roles.length > 0) {
                html += "<div class='mt-1'>";
                group.roles.forEach(function (role) {
                  html += "<span class='badge bg-secondary text-white px-3 py-1 rounded-pill me-1'>" + role + "</span>";
                });
                html += "</div>";
              }
              html += "</div>";
              html += "<div>";
              html += "<button class='btn btn-sm btn-outline-primary me-1' onclick='editGroup(" + group.id + ", \"" + group.name + "\", \"" + group.roles.join(',') + "\")'>";
              html += "<i class='fas fa-edit'></i>";
              html += "</button>";
              html += "<button class='btn btn-sm btn-outline-danger' onclick='deleteGroup(" + group.id + ", this)'>";
              html += "<i class='fas fa-trash'></i>";
              html += "</button>";
              html += "</div>";
              html += "</div>";
              html += "</div>";
            });
            if (html === "") {
              html = "<div class='text-muted'>No groups found</div>";
            }
            $("#groupsList").html(html);
          }
        });
      }

      // Load roles
      function loadRoles() {
        return $.getJSON("group-actions?action=getRoles").then(function (response) {
          if (response.success) {
            let html = "";
            response.roles.forEach(function (role, index) {
              html += "<div class='role-item border rounded p-3 mb-3 shadow-sm'>";
              html += "<div class='d-flex justify-content-between align-items-center'>";
              html += "<div>";
              html += "<strong class='fs-5'>" + role.name + "</strong>";
              if (role.description) {
                html += "<br><small class='text-muted'>" + role.description + "</small>";
              }
              if (role.modules && role.modules.length > 0) {
                html += "<div class='mt-2'>";
                role.modules.forEach(function(mod) {
                  html += "<span class='badge bg-primary text-white px-3 py-1 rounded-pill me-1'>" + mod + "</span>";
                });
                html += "</div>";
              }
              html += "</div>";
              html += "<div>";
              html += "<button class='btn btn-sm btn-outline-primary me-1' onclick='manageRoleModules(" + role.id + ", \"" + role.name + "\")'>";
              html += "<i class='fas fa-cogs'></i> Modules";
              html += "</button>";
              html += "<button class='btn btn-sm btn-outline-danger' onclick='deleteRole(" + role.id + ", this)'>";
              html += "<i class='fas fa-trash'></i>";
              html += "</button>";
              html += "</div>";
              html += "</div>";
              html += "</div>";
            });
            if (html === "") {
              html = "<div class='text-muted'>No roles found</div>";
            }
            $("#rolesList").html(html);
          }
        });
      }

      // Save new group
      function saveGroup() {
        const groupName = $("#newGroupName").val();
        const roles = [];
        $("#groupRolesCheckboxes input[type=checkbox]:checked").each(function () {
          roles.push($(this).val());
        });

        if (!groupName) {
          alert("Please enter a group name");
          return;
        }

        $.post(
          "group-actions",
          {
            action: "saveGroup",
            name: groupName,
            roles: JSON.stringify(roles),
          },
          function (response) {
            if (response.success) {
              $("#addGroupModal").modal("hide");
              loadGroups();
              $("#newGroupName").val("");
              $("#groupRolesCheckboxes input[type=checkbox]").prop("checked", false);
            } else {
              alert("Error: " + (response.message || "Failed to save group"));
            }
          },
          "json"
        ).fail(function() {
          alert("Request failed");
        });
      }

      // Save new role
      function saveRole() {
        const roleName = $("#newRoleName").val();
        const description = $("#roleDescription").val();

        if (!roleName) {
          alert("Please enter a role name");
          return;
        }

        $.post(
          "group-actions",
          {
            action: "saveRole",
            name: roleName,
            description: description,
          },
          function (response) {
            if (response.success) {
              $("#addRoleModal").modal("hide");
              loadRoles();
              $("#newRoleName").val("");
              $("#roleDescription").val("");
            } else {
              alert("Error: " + (response.message || "Failed to save role"));
            }
          },
          "json"
        ).fail(function() {
          alert("Request failed");
        });
      }

      let currentRoleId = null;

      // Manage modules for role
      function manageRoleModules(roleId, roleName) {
        currentRoleId = roleId;
        $("#manageRoleModulesModal .modal-title").text("Manage Modules for " + roleName);
        loadRoleModules(roleId);
        $("#manageRoleModulesModal").modal("show");
      }

      // Load modules for role in modal
      function loadRoleModules(roleId) {
        $.getJSON("group-actions?action=getRoles").done(function (rolesResponse) {
          if (rolesResponse.success) {
            const role = rolesResponse.roles.find(r => r.id == roleId);
            const allowedModules = role ? role.modules || [] : [];
            if (allowedModules.length > 0) {
              let assignedHtml = '<strong>Assigned Modules:</strong><div class="assigned-modules-list d-flex flex-wrap gap-2 mt-2">';
              allowedModules.forEach(function(mod) {
                assignedHtml += '<span class="badge bg-primary text-white px-3 py-1 rounded-pill">' + mod + '</span>';
              });
              assignedHtml += '</div>';
              $("#assignedModules").html(assignedHtml);
            } else {
              $("#assignedModules").html('<strong>Assigned Modules:</strong> None');
            }
            $.getJSON("group-actions?action=getAllModules").done(function (response) {
              if (response.success) {
                let html = "";
                response.modules.forEach(function (module) {
                  const isChecked = allowedModules.includes(module.key) ? 'checked' : '';
                  html += "<div class='mb-2'>";
                  html += "<div class='form-check'>";
                  html += "<input class='form-check-input' type='checkbox' value='" + module.key + "' id='module_" + module.key + "' " + isChecked + ">";
                  html += "<label class='form-check-label' for='module_" + module.key + "'>" + module.label + "</label>";
                  html += "</div>";
                  html += "</div>";
                });
                $("#roleModulesList").html(html);
              }
            }).fail(function() {
              alert("Failed to load modules");
            });
          }
        }).fail(function() {
          alert("Failed to load roles");
        });
      }

      // Save role modules
      function saveRoleModules() {
        const selectedModules = [];
        $("#roleModulesList input[type=checkbox]:checked").each(function () {
          selectedModules.push($(this).val());
        });

        $.post(
          "group-actions",
          {
            action: "updateRoleModules",
            roleId: currentRoleId,
            modules: JSON.stringify(selectedModules),
          },
          function (response) {
            if (response.success) {
              $("#manageRoleModulesModal").modal("hide");
              loadRoles(); // Refresh roles list if needed
            } else {
              alert("Error: " + (response.message || "Failed to save modules"));
            }
          },
          "json"
        ).fail(function() {
          alert("Request failed");
        });
      }



      // Delete group
      function deleteGroup(groupId, element) {
        $.post(
          "group-actions",
          {
            action: "deleteGroup",
            id: groupId,
          },
          function (response) {
            if (response.success) {
              $(element).closest(".group-item").remove();
            } else {
              alert("Error: " + (response.message || "Failed to delete group"));
            }
          },
          "json"
        ).fail(function() {
          alert("Request failed");
        });
      }

      // Delete role
      function deleteRole(roleId, element) {
        $.post(
          "group-actions",
          {
            action: "deleteRole",
            id: roleId,
          },
          function (response) {
            if (response.success) {
              $(element).closest(".role-item").remove();
            } else {
              alert("Error: " + (response.message || "Failed to delete role"));
            }
          },
          "json"
        ).fail(function() {
          alert("Request failed");
        });
      }
    </script>
  </div>
</body>
</html>

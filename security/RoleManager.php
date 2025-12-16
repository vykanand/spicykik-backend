<?php
class RoleManager {
    private $db;
    private $tables;
    private $config;
    
    public function __construct() {
        // Load config
        $this->config = require __DIR__ . '/config.php';
        $this->tables = $this->config['tables'];
        
        // Create database connection
        $dbConfig = $this->config['database'];
        $this->db = new mysqli(
            $dbConfig['host'],
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            $dbConfig['port']
        );
        
        if ($this->db->connect_error) {
            throw new Exception('Security database connection failed: ' . $this->db->connect_error);
        }
        
        // Ensure database and tables exist
        $this->setupDatabase();
        $this->setupTables();
    }
    
    
    private function setupDatabase() {
        // Create database if not exists
        $dbName = $this->config['database']['database'];
        $createDb = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        // First connect without database to create it if needed
        $tempDb = new mysqli(
            $this->config['database']['host'],
            $this->config['database']['username'],
            $this->config['database']['password'],
            '',
            $this->config['database']['port']
        );
        
        if ($tempDb->connect_error) {
            throw new Exception('MySQL connection failed: ' . $tempDb->connect_error);
        }
        
        if (!$tempDb->query($createDb)) {
            throw new Exception('Failed to create database: ' . $tempDb->error);
        }
        
        $tempDb->close();
        
        // Select the database
        if (!$this->db->select_db($dbName)) {
            throw new Exception('Failed to select database: ' . $this->db->error);
        }
    }
    
    private function setupTables() {
        $queries = [
            // Roles table
            "CREATE TABLE IF NOT EXISTS `{$this->tables['roles']}` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
            
            // Groups table
            "CREATE TABLE IF NOT EXISTS `{$this->tables['groups']}` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
            
            // Group Roles mapping
            "CREATE TABLE IF NOT EXISTS `{$this->tables['group_roles']}` (
                group_id INT NOT NULL,
                role_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (group_id, role_id),
                FOREIGN KEY (group_id) REFERENCES `{$this->tables['groups']}`(id) ON DELETE CASCADE,
                FOREIGN KEY (role_id) REFERENCES `{$this->tables['roles']}`(id) ON DELETE CASCADE
            ) ENGINE=InnoDB",
            
            // User Groups mapping
            "CREATE TABLE IF NOT EXISTS `{$this->tables['user_groups']}` (
                user_id INT NOT NULL,
                group_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (user_id, group_id),
                FOREIGN KEY (group_id) REFERENCES `{$this->tables['groups']}`(id) ON DELETE CASCADE
            ) ENGINE=InnoDB",
            
            // Permissions table
            "CREATE TABLE IF NOT EXISTS `{$this->tables['permissions']}` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
            
            // Role Permissions mapping
            "CREATE TABLE IF NOT EXISTS `{$this->tables['role_permissions']}` (
                role_id INT NOT NULL,
                permission_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (role_id, permission_id),
                FOREIGN KEY (role_id) REFERENCES `{$this->tables['roles']}`(id) ON DELETE CASCADE,
                FOREIGN KEY (permission_id) REFERENCES `{$this->tables['permissions']}`(id) ON DELETE CASCADE
            ) ENGINE=InnoDB"
        ];
        
        foreach ($queries as $query) {
            $this->db->query($query);
        }
        
        $this->seedDefaultData();
    }
    
    private function seedDefaultData() {
        // Seed default roles
        if (isset($this->config['default_roles']) && is_array($this->config['default_roles'])) {
            foreach ($this->config['default_roles'] as $roleKey => $roleData) {
                if (is_array($roleData) && isset($roleData['name'])) {
                    $this->createRole($roleKey, $roleData);
                }
            }
        }
        
        // Seed permissions
        if (isset($this->config['permissions']) && is_array($this->config['permissions'])) {
            foreach ($this->config['permissions'] as $category => $perms) {
                if (is_array($perms)) {
                    foreach ($perms as $perm) {
                        $this->createPermission($perm, ucfirst(str_replace('_', ' ', $perm)));
                    }
                }
            }
        }
    }
    
    // Role Management
    public function createRole($name, $data) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO `{$this->tables['roles']}` (name, description) VALUES (?, ?)");
        $stmt->bind_param('ss', $name, $data['name']);
        $stmt->execute();
        return $this->db->insert_id;
    }
    
    public function getRoles() {
        $result = $this->db->query("SELECT * FROM `{$this->tables['roles']}` ORDER BY name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Group Management
    public function createGroup($name, $description = '') {
        $stmt = $this->db->prepare("INSERT INTO `{$this->tables['groups']}` (name, description) VALUES (?, ?)");
        $stmt->bind_param('ss', $name, $description);
        $stmt->execute();
        return $this->db->insert_id;
    }
    
    public function getGroups() {
        $result = $this->db->query("SELECT * FROM `{$this->tables['groups']}` ORDER BY name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Permission Management
    public function createPermission($name, $description = '') {
        $stmt = $this->db->prepare("INSERT IGNORE INTO `{$this->tables['permissions']}` (name, description) VALUES (?, ?)");
        $stmt->bind_param('ss', $name, $description);
        $stmt->execute();
        return $this->db->insert_id;
    }
    
    public function getPermissions() {
        $result = $this->db->query("SELECT * FROM `{$this->tables['permissions']}` ORDER BY name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // User Management
    public function addUserToGroup($userId, $groupId) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO `{$this->tables['user_groups']}` (user_id, group_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $userId, $groupId);
        return $stmt->execute();
    }
    
    public function getUserGroups($userId) {
        $stmt = $this->db->prepare("
            SELECT g.* FROM `{$this->tables['groups']}` g
            JOIN `{$this->tables['user_groups']}` ug ON g.id = ug.group_id
            WHERE ug.user_id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Check Permissions
    public function hasPermission($userId, $permission) {
        $stmt = $this->db->prepare("
            SELECT 1 FROM `{$this->tables['permissions']}` p
            JOIN `{$this->tables['role_permissions']}` rp ON p.id = rp.permission_id
            JOIN `{$this->tables['roles']}` r ON rp.role_id = r.id
            JOIN `{$this->tables['group_roles']}` gr ON r.id = gr.role_id
            JOIN `{$this->tables['user_groups']}` ug ON gr.group_id = ug.group_id
            WHERE ug.user_id = ? AND (p.name = ? OR p.name = '*')
            LIMIT 1
        ");
        $stmt->bind_param('is', $userId, $permission);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}

<?php
class RoleGroupManager {
    private $db;
    private $tablePrefix = 'security_';
    private $error = null;
    
    public function __construct() {
        try {
            // Use main application config and database connection
            if (!@include_once(__DIR__ . '/../config.php')) {
                throw new Exception('Failed to load config file');
            }
            
            global $db;
            
            if (!isset($db) || !($db instanceof mysqli)) {
                throw new Exception('Database connection not properly initialized');
            }
            
            $this->db = $db;
            
            // Test the connection
            if (!$this->db->ping()) {
                throw new Exception('Database connection failed: ' . $this->db->connect_error);
            }
            
            $this->setupTables();
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log('RoleGroupManager Error: ' . $this->error);
            throw $e; // Re-throw to be caught by the caller
        }
    }
    
    private function setupTables() {
        if (!$this->db) {
            throw new Exception('Database connection not established');
        }
        
        $tables = [
            "CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}groups` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}roles` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}user_groups` (
                user_id INT NOT NULL,
                group_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (user_id, group_id),
                FOREIGN KEY (group_id) REFERENCES `{$this->tablePrefix}groups`(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];
        
        foreach ($tables as $sql) {
            if (!$this->db->query($sql)) {
                throw new Exception('Failed to create table: ' . $this->db->error);
            }
        }
    }
    
    // Group methods
    public function createGroup($name, $description = '') {
        $query = "INSERT INTO `{$this->tablePrefix}groups` (name, description) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $name, $description);
        return $stmt->execute() ? $this->db->insert_id : false;
    }
    
    public function getGroups() {
        $query = "SELECT * FROM `{$this->tablePrefix}groups` ORDER BY name";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function deleteGroup($groupId) {
        $query = "DELETE FROM `{$this->tablePrefix}groups` WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $groupId);
        return $stmt->execute();
    }
    
    // User-Group methods
    public function addUserToGroup($userId, $groupId) {
        $query = "INSERT IGNORE INTO `{$this->tablePrefix}user_groups` (user_id, group_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userId, $groupId);
        return $stmt->execute();
    }
    
    public function removeUserFromGroup($userId, $groupId) {
        $query = "DELETE FROM `{$this->tablePrefix}user_groups` WHERE user_id = ? AND group_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userId, $groupId);
        return $stmt->execute();
    }
    
    public function getUserGroups($userId) {
        $query = "SELECT g.* FROM `{$this->tablePrefix}user_groups` ug
                 JOIN `{$this->tablePrefix}groups` g ON ug.group_id = g.id
                 WHERE ug.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Search users (assuming users table exists)
    public function searchUsers($query) {
        $search = "%$query%";
        $sql = "SELECT id, username, email FROM users 
                WHERE username LIKE ? OR email LIKE ? 
                LIMIT 20";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss', $search, $search);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

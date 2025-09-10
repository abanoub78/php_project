<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Database {
    private $host = "127.0.0.1";
    private $dbname = "Library_db";
    private $username = "admin1";
    private $password = "mustafa123";
    private $conn;
    private static $instance = null;

    private function __construct() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                                   $this->username,
                                   $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("DB Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getConnection() {
        return $this->db;
    }

    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public function create($data, $file) {
        $fname   = $this->validate($data['fname']);
        $lname   = $this->validate($data['lname']);
        $email   = $this->validate($data['email']);
        $password= password_hash($data['password'], PASSWORD_DEFAULT);

        $imgname = null;
        if (!empty($file['profimg']['name'])) {
            $from    = $file['profimg']['tmp_name'];
            $imgname = $file['profimg']['name'];
            move_uploaded_file($from, "./$imgname");
        }

        // Determine role from form input
        $role = isset($data['is_admin']) && $data['is_admin'] === 'on' ? 'admin' : 'member';

        // Validate role
        if (!in_array($role, ['member', 'admin'])) {
            throw new Exception("Invalid role value: " . htmlspecialchars($role));
        }

        // Debug role value (remove after testing)
        // error_log("Inserting role: '$role'");

        $stmt = $this->db->prepare("
            INSERT INTO users (fname, lname, email, password, imgname, role)
            VALUES (:fname, :lname, :email, :password, :imgname, :role)
        ");
        $stmt->execute([
            ':fname' => $fname,
            ':lname' => $lname,
            ':email' => $email,
            ':password' => $password,
            ':imgname' => $imgname,
            ':role' => $role
        ]);
        return $this->db->lastInsertId();
    }

    public function getAll($tablename) {
        $stmt = $this->db->query("SELECT * FROM $tablename");
        return $stmt->fetchAll();
    }

    public function getById($tablename, $id) {
        $stmt = $this->db->prepare("SELECT * FROM $tablename WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($tablename, $id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
        }
        $fields_str = implode(", ", $fields);

        $sql = "UPDATE $tablename SET $fields_str WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        $values = array_values($data);
        $values[] = $id;

        return $stmt->execute($values);
    }

    public function delete($tablename, $id) {
        $stmt = $this->db->prepare("DELETE FROM $tablename WHERE id=?");
        return $stmt->execute([$id]);
    }

    private function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}
?>
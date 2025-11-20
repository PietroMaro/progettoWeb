<?php
require_once  'db/database.php';
class UserManager{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // idUtente | null
    public function login($email, $password): ?int 
    {
        $sql = "SELECT idUtente, PASSWORD 
                FROM utente 
                WHERE email = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Errore Database (Prepare): " . $this->db->error);
        }
        $stmt->bind_param('s', $email);
        if (!$stmt->execute()) {
            throw new Exception("Errore Database (Execute): " . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($password === $row['PASSWORD']) {
                return (int)$row['idUtente'];
            }
        }
        return null;
    }
}
?>
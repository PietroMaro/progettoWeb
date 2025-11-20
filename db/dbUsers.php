<?php
require_once  'db/database.php';
class UserManager{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // id | null
    public function login($email, $password, $isAdmin): ?int 
    {
        if($isAdmin === false){
            $sql = "SELECT idUtente, PASSWORD 
                    FROM utente 
                    WHERE email = ?";
        } else {
            $sql = "SELECT idAdmin, PASSWORD 
                    FROM admin
                    WHERE email = ?";
        }

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
                if($isAdmin === false){
                    return (int)$row['idUtente'];
                } else {
                    return (int)$row['idAdmin'];
                }
            }
        }
        return null;
    }

    public function registerUser(
        $propic,
        $nome,
        $cognome,
        $username,
        $descrizione,
        $email,
        $password
    ) : void {
        $checkSql = "SELECT idUtente FROM utente WHERE email = ?";
        $stmt = $this->db->prepare($checkSql);
        if (!$stmt) {
            throw new Exception("Errore Database (Prepare Check): " . $this->db->error);
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            throw new Exception("Questa email è già registrata.");
        }
        $stmt->close();
        $idImmagine = null;
        if (isset($propic) && $propic['error'] === 0) {
            $imageData = file_get_contents($propic['tmp_name']);
            $imgSql = "INSERT INTO immagini (immagine) VALUES (?)";
            $stmtImg = $this->db->prepare($imgSql);
            if (!$stmtImg) {
                throw new Exception("Errore Database (Prepare Image): " . $this->db->error);
            }
            $stmtImg->bind_param('s', $imageData); 
            if (!$stmtImg->execute()) {
                throw new Exception("Errore caricamento immagine: " . $stmtImg->error);
            }
           $idImmagine = $stmtImg->insert_id;
           $stmtImg->close();
        }
        $insertSql = "INSERT INTO utente (nome, cognome, username, descrizione, email, PASSWORD, idImmagine) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($insertSql);
        if (!$stmt) {
            throw new Exception("Errore Database (Prepare Insert): " . $this->db->error);
        }
        $stmt->bind_param(
            'ssssssi', 
            $nome, 
            $cognome, 
            $username, 
            $descrizione, 
            $email, 
            $password, 
            $idImmagine
        );
        if (!$stmt->execute()) {
            throw new Exception("Errore durante la registrazione dell'utente: " . $stmt->error);
        }
        $stmt->close();
    }
}
?>
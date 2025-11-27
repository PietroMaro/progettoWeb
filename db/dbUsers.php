<?php
require_once __DIR__ . '/database.php';
class UserManager
{
    private $db;
    public function __construct()
    {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (Exception $e) {

            error_log("Errore di connessione al database: " . $e->getMessage());
            $this->db = null;
        }
    }


    public function getUserInfo($userId)
    {

        try {
            $queryUser = "SELECT u.nome, u.cognome, u.email, u.username, u.descrizione, i.immagine 
                  FROM utente u 
                  LEFT JOIN immagini i ON u.idImmagine = i.idImmagine 
                  WHERE u.idUtente = ?";
            $stmt = $this->db->prepare($queryUser);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->error);
            }
            
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return null;
            }

            $userData = $result->fetch_assoc();

            if (!empty($userData['immagine'])) {
                $userData['imgSrc'] = "data:image/jpeg;base64," . base64_encode($userData['immagine']);
            }
            unset($userData['immagine']);

            $queryProd = "SELECT p.idProdotto, p.nome, 
                                 (SELECT immagine FROM immagini WHERE idProdotto = p.idProdotto LIMIT 1) as immagine
                          FROM prodotto p 
                          WHERE p.idUtente = ? AND p.stato = 'venduto'
                          ORDER BY p.fineAsta DESC";

            $stmtProd = $this->db->prepare($queryProd);
            if (!$stmtProd) {
                throw new Exception("Prepare Prod failed: " . $this->db->error);
            }

            $stmtProd->bind_param("i", $userId);
            $stmtProd->execute();
            $resProd = $stmtProd->get_result();

            $soldProducts = [];
            while ($row = $resProd->fetch_assoc()) {
                if (!empty($row['immagine'])) {
                     $row['imgSrc'] = "data:image/jpeg;base64," . base64_encode($row['immagine']);
                } else {
                     $row['imgSrc'] = null; // Handle cases with no image
                }

                unset($row['immagine']);
                $soldProducts[] = $row;
            }

            $userData['soldProducts'] = $soldProducts;

            return $userData;
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return null;
        } 
    }

    // id | null
    public function login($email, $password, $isAdmin): ?int
    {
        if ($isAdmin === false) {
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
                if ($isAdmin === false) {
                    return (int) $row['idUtente'];
                } else {
                    return (int) $row['idAdmin'];
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
    ): void {
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

    public function isBanned($email, $password): bool {
        $sql = "SELECT idBan, PASSWORD 
                FROM ban 
                WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Errore Database (Prepare Ban): " . $this->db->error);
        }
        $stmt->bind_param('s', $email);
        if (!$stmt->execute()) {
            throw new Exception("Errore Database (Execute Ban): " . $stmt->error);
        }
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if ($password === $row['PASSWORD']) {
                return true;
            }
        }
        return false; 
    }


}
?>
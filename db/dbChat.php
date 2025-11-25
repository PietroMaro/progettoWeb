<?php
require_once __DIR__ . '/database.php';
class ChatManager
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

    public function getUserChats($userId)
    {
        try {
            $queryChat = "SELECT * FROM CHAT WHERE idUtente1 = ? OR idUtente2 = ?";
            $stmt = $this->db->prepare($queryChat);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->error);
            }
            $stmt->bind_param("ii", $userId, $userId);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $result = $stmt->get_result();
            $chats = []; 
            while ($row = $result->fetch_assoc()) {
                $notYouId = null;
                if ($row['idUtente1'] === $userId) {
                    $notYouId = $row['idUtente2'];
                } else {
                    $notYouId = $row['idUtente1'];
                }
                $row['immageNotYou'] = $this->getImmageOfUserFromId($notYouId);
                if (isset($row['idProdotto'])) {
                    $row['immageProdotto'] = $this->getImmageOfProdottoFromId($row['idProdotto']);
                } else {
                    $row['immageProdotto'] = null;
                }
                $row['nomeNotYou'] = $this->getNomeUtenteFromId($notYouId);
                $row['nomeProdotto'] = $this->getNomeProdottoFromId($row['idProdotto']);
                $chats[] = $row;
            }
            return $chats;
        } catch (Exception $e) {
            throw new Exception("Prepare Prod failed: " . $this->db->error);
        }
    }

    // Blob of the immage
    private function getImmageOfUserFromId($userId): ?string {
        $sql = "SELECT i.immagine 
                FROM utente u 
                JOIN immagini i ON u.idImmagine = i.idImmagine 
                WHERE u.idUtente = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return "data:image/jpeg;base64," . base64_encode($row['immagine']);
        }
        return null; 
    }

    // Blob of the immage
    private function getImmageOfProdottoFromId($prodottoId): ?string {
        $sql = "SELECT i.immagine 
                FROM immagini i 
                WHERE i.idProdotto = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("i", $prodottoId);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return "data:image/jpeg;base64," . base64_encode($row['immagine']);
        }
        return null; 
    }

    private function getNomeProdottoFromId(int $prodottoId): ?string {
        $sql = "SELECT nome
                FROM prodotto 
                WHERE idProdotto = ?"; 
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("i", $prodottoId);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['nome']; 
        }
        return null; 
    }

    private function getNomeUtenteFromId(int $userId): ?string {
        $sql = "SELECT nome
                FROM utente 
                WHERE idUtente = ?"; 
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['nome']; 
        }
        return null; 
    }

    public function getChatHistory($chatId): array {
        $sql = "SELECT 
                    'message' AS type,
                    m.idMessaggio AS id,
                    m.testo AS content,
                    m.progressivo,
                    m.idMandante,
                    i.immagine
                FROM messaggio m
                LEFT JOIN immagini i ON m.idMessaggio = i.idMessaggio
                WHERE m.idChat = ?
                UNION
                SELECT 
                    'offer' AS type,
                    o.idOffertaChat AS id,
                    CAST(o.valore AS CHAR) AS content,
                    o.progressivo,
                    o.idMandante,
                    NULL AS immagine
                FROM offertaChat o
                WHERE o.idChat = ?
                ORDER BY progressivo ASC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("ii", $chatId, $chatId);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $formattedImage = null;
            if (!empty($row['immagine'])) {
                $formattedImage = "data:image/jpeg;base64," . base64_encode($row['immagine']);
            }
            $history[] = [
                'type'        => $row['type'],
                'id'          => $row['id'],
                'content'     => $row['content'],
                'progressivo' => $row['progressivo'],
                'idMandante' => $row['idMandante'],
                'image'       => $formattedImage
            ];
        }
        return $history;
    }

    public function addMessage(int $idSender, int $idChat, ?string $messageContent, $immage = null) : void {
        $sql_progressivo = "
            SELECT MAX(progressivo) AS max_prog 
            FROM messaggio 
            WHERE idChat = ?
        ";
        $stmt_prog = $this->db->prepare($sql_progressivo);
        if (!$stmt_prog) {
            throw new Exception("Prepare failed (progressivo): " . $this->db->error);
        }
        $stmt_prog->bind_param("i", $idChat);
        $stmt_prog->execute();
        $result_prog = $stmt_prog->get_result()->fetch_assoc();
        $progressivo = ($result_prog['max_prog'] ?? 0) + 1;
        $stmt_prog->close();
        if (empty($messageContent) && $immage === null) {
            return; 
        }
        $sql_message = "
            INSERT INTO messaggio (testo, progressivo, idChat, idMandante) 
            VALUES (?, ?, ?, ?)
        ";
        $stmt_msg = $this->db->prepare($sql_message);
        if (!$stmt_msg) {
            throw new Exception("Prepare failed (messaggio): " . $this->db->error);
        }
        $stmt_msg->bind_param("siii", $messageContent, $progressivo, $idChat, $idSender);
        if (!$stmt_msg->execute()) {
            throw new Exception("Execute failed (messaggio): " . $stmt_msg->error);
        }
        $idMessaggio = $this->db->insert_id; // Get the ID of the newly inserted message
        $stmt_msg->close();
        if ($immage !== null) {
            $file_data = $immage;
            if ($file_data === FALSE) {
                $this->db->query("DELETE FROM messaggio WHERE idMessaggio = $idMessaggio");
                throw new Exception("Could not read uploaded file data: " . $immage);
            }
            $sql_image = "
                INSERT INTO immagini (immagine, idMessaggio) 
                VALUES (?, ?)
            ";
            $stmt_img = $this->db->prepare($sql_image);
            if (!$stmt_img) {
                $this->db->query("DELETE FROM messaggio WHERE idMessaggio = $idMessaggio");
                throw new Exception("Prepare failed (immagini): " . $this->db->error);
            }
            $null = NULL; 
            $stmt_img->bind_param("bi", $null, $idMessaggio); 
            $stmt_img->send_long_data(0, $file_data); // Send the actual binary data
            if (!$stmt_img->execute()) {
                $this->db->query("DELETE FROM messaggio WHERE idMessaggio = $idMessaggio");
                throw new Exception("Execute failed (immagini): " . $stmt_img->error);
            }
            $stmt_img->close();
        }
    }

    public function getNewMessages(int $idChat, int $idUser, int $lastProgressivo): array {
        $sql = "
            SELECT 
                m.idMandante, 
                m.testo AS content, 
                m.progressivo,
                i.immagine AS immage_blob,
                'message' AS type -- Helps the frontend distinguish 
            FROM messaggio m
            LEFT JOIN immagini i ON m.idMessaggio = i.idMessaggio
            WHERE m.idChat = ? 
              AND m.progressivo > ?
            ORDER BY m.progressivo ASC
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("ii", $idChat, $lastProgressivo);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $result;
    }
}
?>
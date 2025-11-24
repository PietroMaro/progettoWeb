<?php
require_once 'db/database.php';
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
                'image'       => $formattedImage
            ];
        }
        return $history;
    }



}
?>
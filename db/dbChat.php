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
            throw new Exception("Error creating the db connection ");
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

    public function getOpenReports(){
        try {
            $query = "
                SELECT 
                    s.*, 
                    c.idUtente1, 
                    c.idUtente2, 
                    c.idProdotto
                FROM segnalazione s
                JOIN chat c ON s.idChat = c.idChat
                WHERE s.stato = 'aperta'
            ";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->error);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $reports = [];

            while ($row = $result->fetch_assoc()) {
                $idReporter = $row['idMandante'];
                $idReported = ($row['idUtente1'] == $idReporter) ? $row['idUtente2'] : $row['idUtente1'];
                $row['idReporter']    = $idReporter;
                $row['nomeReporter']  = $this->getNomeUtenteFromId($idReporter);
                $row['imageReporter'] = $this->getImmageOfUserFromId($idReporter);
                $row['idReported']    = $idReported;
                $row['nomeReported']  = $this->getNomeUtenteFromId($idReported);
                $row['imageReported'] = $this->getImmageOfUserFromId($idReported);
                if (isset($row['idProdotto'])) {
                    $row['nomeProdotto']   = $this->getNomeProdottoFromId($row['idProdotto']);
                    $row['imageProdotto']  = $this->getImmageOfProdottoFromId($row['idProdotto']);
                } else {
                    $row['nomeProdotto']   = "Prodotto sconosciuto";
                    $row['imageProdotto']  = null;
                }

                $reports[] = $row;
            }

            return $reports;

        } catch (Exception $e) {
            throw new Exception("Error fetching open reports: " . $e->getMessage());
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

    private function getNextProgressivo(int $idChat): int {
        $sql = "
            SELECT GREATEST(
                COALESCE((SELECT MAX(progressivo) FROM messaggio WHERE idChat = ?), 0),
                COALESCE((SELECT MAX(progressivo) FROM offertaChat WHERE idChat = ?), 0)
            ) AS max_prog 
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed (getNextProgressivo): " . $this->db->error);
        }

        $stmt->bind_param("ii", $idChat, $idChat);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return ($result['max_prog'] ?? 0) + 1;
    }

    public function addMessage(int $idSender, int $idChat, ?string $messageContent, $immage = null) : void {
        
        $progressivo = $this->getNextProgressivo($idChat);

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

        $idMessaggio = $this->db->insert_id;
        $stmt_msg->close();

        if ($immage !== null) {
            $file_data = $immage;
            
            if ($file_data === FALSE) {
                $this->db->query("DELETE FROM messaggio WHERE idMessaggio = $idMessaggio");
                throw new Exception("Could not read uploaded file data.");
            }

            $sql_image = "INSERT INTO immagini (immagine, idMessaggio) VALUES (?, ?)";
            $stmt_img = $this->db->prepare($sql_image);
            
            if (!$stmt_img) {
                $this->db->query("DELETE FROM messaggio WHERE idMessaggio = $idMessaggio");
                throw new Exception("Prepare failed (immagini): " . $this->db->error);
            }

            $null = NULL; 
            $stmt_img->bind_param("bi", $null, $idMessaggio); 
            $stmt_img->send_long_data(0, $file_data);

            if (!$stmt_img->execute()) {
                $this->db->query("DELETE FROM messaggio WHERE idMessaggio = $idMessaggio");
                throw new Exception("Execute failed (immagini): " . $stmt_img->error);
            }
            $stmt_img->close();
        }
    }

    public function addNewOffertaChat(int $idChat, int $idMandante, int $valore) : void {
        $progressivo = $this->getNextProgressivo($idChat);

        $sql_offer = "
            INSERT INTO offertaChat (valore, progressivo, idChat, idMandante) 
            VALUES (?, ?, ?, ?)
        ";

        $stmt_off = $this->db->prepare($sql_offer);
        if (!$stmt_off) {
            throw new Exception("Prepare failed (offertaChat): " . $this->db->error);
        }

        $stmt_off->bind_param("iiii", $valore, $progressivo, $idChat, $idMandante);

        if (!$stmt_off->execute()) {
            throw new Exception("Execute failed (offertaChat): " . $stmt_off->error);
        }

        $stmt_off->close();
    }


    public function addNewSegnalazione(String $tipoSegnalazione, String $testo, int $idMandante, int $idChat) : void {
        $sql_offer = "
            INSERT INTO segnalazione (tipoSegnalazione, testo, idMandante, stato, idChat) 
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt_off = $this->db->prepare($sql_offer);
        if (!$stmt_off) {
            throw new Exception("Prepare failed (offertaChat): " . $this->db->error);
        }

        $stato = "aperta";
        $stmt_off->bind_param("ssisi", $tipoSegnalazione, $testo, $idMandante,$stato,$idChat);

        if (!$stmt_off->execute()) {
            throw new Exception("Execute failed (offertaChat): " . $stmt_off->error);
        }

        $stmt_off->close();

    }

    public function getNewMessages(int $idChat, int $idUser, int $lastProgressivo): array {
        $sql = "
            (
                -- 1. SELECT MESSAGES
                SELECT 
                    m.idMandante, 
                    m.testo AS content, 
                    m.progressivo, 
                    i.immagine AS immage_blob, 
                    'message' AS type
                FROM messaggio m
                LEFT JOIN immagini i ON m.idMessaggio = i.idMessaggio
                WHERE m.idChat = ? AND m.progressivo > ?
            )
            UNION ALL
            (
                -- 2. SELECT OFFERS
                SELECT 
                    o.idMandante, 
                    CAST(o.valore AS CHAR) AS content, -- Treat the price as the content
                    o.progressivo, 
                    NULL AS immage_blob, -- Offers don't have images, so we send NULL
                    'offer' AS type
                FROM offertaChat o
                WHERE o.idChat = ? AND o.progressivo > ?
            )
            -- 3. ORDER EVERYTHING BY TIME
            ORDER BY progressivo ASC
        ";
    
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
    
        $stmt->bind_param("iiii", $idChat, $lastProgressivo, $idChat, $lastProgressivo);
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    
        return $result;
    }


}
?>
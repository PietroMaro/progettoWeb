<?php
require_once __DIR__ . '/database.php';

class ProductManager
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

    public function saveProduct($formData, $loadedFile): void
    {
        if ($this->db === null) {
            throw new Exception("Servizio temporaneamente non disponibile.");
        }
        $this->db->begin_transaction();

        try {
            $name = $formData['productName'];
            $description = $formData['productDescription'];
            $price = $formData['productPrice'];
            $endDate = (isset($formData['isAuction'])) ? $formData['auctionEndDate'] : null;
            $userId = $_SESSION['user_id'];

            $sql = "INSERT INTO prodotto (nome, descrizione, prezzo, stato, ragioneRifiuto, fineAsta, idUtente, idAdmin) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);

            $stato = 'attesa';
            $ragione = null;
            $admin = null;

            $stmt->bind_param("ssdsssii", $name, $description, $price, $stato, $ragione, $endDate, $userId, $admin);
            $stmt->execute();

            $productId = $this->db->insert_id;
            $this->saveImages($productId, $loadedFile['images']);

            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function saveImages($productId, $images): void
    {
        $sql = "INSERT INTO immagini (immagine, idProdotto, idMessaggio) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $idMessaggio = null;

        foreach ($images['tmp_name'] as $index => $tmp_path) {
            if ($images['error'][$index] == 0 && !empty($tmp_path)) {
                $imageData = file_get_contents($tmp_path);
                
                $stmt->bind_param("sii", $imageData, $productId, $idMessaggio);
                $stmt->execute();
            }
        }
    }

    public function getProductsForUser(): array
    {
        if ($this->db === null)
            return [];

        try {
            $userId = $_SESSION['user_id'];
            $sql = "SELECT p.idProdotto, p.nome, p.prezzo, p.stato, p.fineAsta, 
                           (SELECT i.immagine FROM immagini i WHERE i.idProdotto = p.idProdotto LIMIT 1) AS immagineData
                    FROM prodotto p
                    WHERE p.idUtente = ?
                    ORDER BY p.idProdotto DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId); 
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            error_log("Error in getProductsForUser: " . $e->getMessage());
            return [];
        }
    }

    public function getFilteredProducts($filters, $isAdmin): array
    {
        if ($this->db === null)
            return [];

        if (isset($_SESSION['user_id']) && !$isAdmin) {
            $userId = $_SESSION['user_id'];
        } else {
           
            $userId =  -1;
        }

        try {
            if ($isAdmin) {
                $sql = "SELECT p.idProdotto, p.nome, p.prezzo, p.stato, p.fineAsta, 
                               (SELECT i.immagine FROM immagini i WHERE i.idProdotto = p.idProdotto LIMIT 1) AS immagineData
                        FROM prodotto p 
                        WHERE p.idUtente != ? AND p.stato IN ('attesa')";
            } else {
                $sql = "SELECT p.idProdotto, p.nome, p.prezzo, p.stato, p.fineAsta, 
                               (SELECT i.immagine FROM immagini i WHERE i.idProdotto = p.idProdotto LIMIT 1) AS immagineData
                        FROM prodotto p 
                        WHERE p.idUtente != ? AND p.stato IN ('esposto', 'asta')";
            }

            $types = "i";
            $params = [$userId];

            if (!empty($filters['search'])) {
                $sql .= " AND p.nome LIKE ?";
                $types .= "s";
                $params[] = "%" . $filters['search'] . "%";
            }

            if (!empty($filters['filter_type'])) {
                if ($filters['filter_type'] === 'auction') {
                    $sql .= " AND p.fineAsta IS NOT NULL";
                } elseif ($filters['filter_type'] === 'direct') {
                    $sql .= " AND p.fineAsta IS NULL";
                }
            }

            $sort = $filters['sort'] ?? 'newest';
            switch ($sort) {
                case 'price_asc':
                    $sql .= " ORDER BY p.prezzo ASC";
                    break;
                case 'price_desc':
                    $sql .= " ORDER BY p.prezzo DESC";
                    break;
                case 'ending_soon':
                    $sql .= " ORDER BY (p.fineAsta IS NULL), p.fineAsta ASC";
                    break;
                default:
                    $sql .= " ORDER BY p.idProdotto DESC";
                    break;
            }

            $stmt = $this->db->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            error_log("Error in getFilteredProducts: " . $e->getMessage());
            return [];
        }
    }

    public function deleteProduct($formData): bool
    {
        if ($this->db === null)
            throw new Exception("Servizio temporaneamente non disponibile.");
        $productId = $formData['idProdotto'];

        $this->db->begin_transaction();
        try {
            $this->deleteImagesByProductId($productId);
            $sql_product = "DELETE FROM prodotto WHERE idProdotto = ?";
            $stmt_product = $this->db->prepare($sql_product);
            $stmt_product->bind_param("i", $productId); 
            $stmt_product->execute();
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateProduct($formData, $loadedFile): void
    {
        if ($this->db === null)
            throw new Exception("Servizio non disponibile.");

        $productId = $formData['idProdotto'];
        $name = $formData['productName'];
        $description = $formData['productDescription'];
        $price = $formData['productPrice'];
        $endDate = (isset($formData['isAuction'])) ? $formData['auctionEndDate'] : null;
        $userId = $_SESSION['user_id'];

        $this->db->begin_transaction();
        try {
            $sql = "UPDATE prodotto SET nome=?, descrizione=?, prezzo=?, fineAsta=?, stato='attesa' WHERE idProdotto=? AND idUtente=?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssdsii", $name, $description, $price, $endDate, $productId, $userId);
            $stmt->execute();

            if (isset($formData['delete_images']) && is_array($formData['delete_images'])) {
                foreach ($formData['delete_images'] as $imgId) {
                    $this->deleteImageById($imgId);
                }
            }

            if (isset($loadedFile['images']) && $loadedFile['images']['error'][0] == 0) {
                $this->saveImages($productId, $loadedFile['images']);
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function changeProductStatus($productId, $decision, $rejectionReason = null): bool
    {
        $adminId = $_SESSION['user_id'];
        $newStatus = '';
        $reasonToSave = null;

        if ($decision === 'approve') {
            $queryCheck = "SELECT fineAsta FROM prodotto WHERE idProdotto = ?";
            $stmt = $this->db->prepare($queryCheck);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if ($product && !empty($product['fineAsta'])) {
                $newStatus = 'asta';
            } else {
                $newStatus = 'esposto';
            }
        } else {
            $newStatus = 'rifiutato';
            $reasonToSave = $rejectionReason;
        }

        $queryUpdate = "UPDATE prodotto SET stato = ?, ragioneRifiuto = ?, idAdmin = ? WHERE idProdotto = ?";
        $stmt = $this->db->prepare($queryUpdate);
        $stmt->bind_param("ssii", $newStatus, $reasonToSave, $adminId, $productId);
        $esito = $stmt->execute();
        $stmt->close();

        return $esito;
    }

    public function acceptOffer($idChat): mixed
    {
        try {
            $sqlCerca = "SELECT idProdotto FROM chat WHERE idChat = ?";
            $stmtCerca = $this->db->prepare($sqlCerca);
            $stmtCerca->bind_param('i', $idChat);
            $stmtCerca->execute();
            $result = $stmtCerca->get_result();
            $dati = $result->fetch_assoc();
            $stmtCerca->close();

            if (!$dati)
                return false;

            $idProdotto = $dati['idProdotto'];
            $sqlAggiorna = "UPDATE prodotto SET stato = 'venduto' WHERE idProdotto = ?";
            if ($stmtAggiorna = $this->db->prepare($sqlAggiorna)) {
                $stmtAggiorna->bind_param('i', $idProdotto);
                $stmtAggiorna->execute();
                $stmtAggiorna->close();
                return $idProdotto;
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Errore MySQLi: " . $this->db->error);
            return false;
        }
    }

    // UTILITY FUNCTIONS

    public function getProductById($productId): array|bool|null
    {
        if ($this->db === null)
            return null;
        $sql = "SELECT * FROM prodotto WHERE idProdotto = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deleteImagesByProductId($productId): void
    {
        $sql = "DELETE FROM immagini WHERE idProdotto = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
    }

    public function getProfileImageByProductId($productId): mixed
    {
        if ($this->db === null)
            return null;
        try {
            $sql = "SELECT i.immagine 
                    FROM prodotto p
                    JOIN utente u ON p.idUtente = u.idUtente
                    JOIN immagini i ON u.idImmagine = i.idImmagine
                    WHERE p.idProdotto = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['immagine'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getImagesByProductId($productId): array
    {
        if ($this->db === null)
            return [];
        try {
            $sql = "SELECT idImmagine, immagine FROM immagini WHERE idProdotto = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function deleteImageById($imageId)
    {
        $sql = "DELETE FROM immagini WHERE idImmagine = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $imageId);
        $stmt->execute();
    }

    public function getUserIdByProductId($productId): mixed
    {
        try {
            $query = "SELECT idUtente FROM prodotto WHERE idProdotto = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['idUtente'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function productStatus($idChat): mixed
    {
        if ($this->db === null)
            return false;
        $sql = "SELECT p.stato FROM prodotto p JOIN chat c ON p.idProdotto = c.idProdotto WHERE c.idChat = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt === false)
            return false;
        $stmt->bind_param("i", $idChat);
        $stmt->execute();
        $result = $stmt->get_result();
        $isSold = false;
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row["stato"];
    }

    public function isProductSold($idProdotto): bool
    {
        $query = "SELECT stato FROM prodotto WHERE idProdotto = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idProdotto);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['stato'] === 'venduto';
        }
        return false;
    }

    // --- CORRETTO QUI SOTTO ---
    function updateAuctions(): array|bool
    {
        if ($this->db === null)
            return false;
        $aggiornatiVenduti = 0;
        $aggiornatiDeserte = 0;

        $sqlVenduti = "
            UPDATE prodotto p
            SET p.stato = 'venduto'
            WHERE p.stato = 'asta' 
            AND p.fineAsta < NOW()
            AND EXISTS (
                SELECT 1 
                FROM offertaAsta oa 
                WHERE oa.idProdotto = p.idProdotto
            )
        ";

        if ($this->db->query($sqlVenduti)) {
            $aggiornatiVenduti = $this->db->affected_rows;
        } else {
            error_log("Errore aggiornamento aste vendute: " . $this->db->error);
        }

        $sqlDeserte = "
            UPDATE prodotto p
            SET p.stato = 'astaDeserta'
           WHERE p.stato IN ('asta', 'attesa')
            AND p.fineAsta < NOW()
            AND NOT EXISTS (
                SELECT 1 
                FROM offertaAsta oa 
                WHERE oa.idProdotto = p.idProdotto
            )
        ";

        if ($this->db->query($sqlDeserte)) {
            $aggiornatiDeserte = $this->db->affected_rows;
        } else {
            error_log("Errore aggiornamento aste deserte: " . $this->db->error);
        }

        return [
            "status" => "success",
            "venduti" => $aggiornatiVenduti,
            "deserte" => $aggiornatiDeserte
        ];
    }

    function addOfferForAuction($idProdotto, $bidValue)
    {
        if ($this->db === null)
            return false;
        $userId = $_SESSION["user_id"];
        $this->db->begin_transaction();

        try {
            $sqlCheck = "SELECT prezzo, stato, fineAsta FROM prodotto WHERE idProdotto = ? FOR UPDATE";
            $stmt = $this->db->prepare($sqlCheck);
            $stmt->bind_param("i", $idProdotto);
            $stmt->execute();
            $res = $stmt->get_result();
            $prodotto = $res->fetch_assoc();
            $stmt->close();

            if (!$prodotto)
                throw new Exception("Prodotto non trovato.");
            if ($prodotto['stato'] !== 'asta')
                throw new Exception("Il prodotto non è all'asta.");
            if (new DateTime($prodotto['fineAsta']) < new DateTime())
                throw new Exception("L'asta è scaduta.");

          

            $sqlProg = "SELECT COALESCE(MAX(progressivo), 0) + 1 AS prossimo_prog 
                        FROM offertaAsta 
                        WHERE idProdotto = ? AND idUtente = ?";
            $stmt = $this->db->prepare($sqlProg);
            $stmt->bind_param("ii", $idProdotto, $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $nuovoProgressivo = $row['prossimo_prog'];
            $stmt->close();

            $valoreIntero = (int) $bidValue; 

            $sqlInsert = "INSERT INTO offertaAsta (idProdotto, idUtente, progressivo, valore) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sqlInsert);
            $stmt->bind_param("iiii", $idProdotto, $userId, $nuovoProgressivo, $valoreIntero);

            if (!$stmt->execute())
                throw new Exception("Errore inserimento offerta.");
            $stmt->close();

            $sqlUpdate = "UPDATE prodotto SET prezzo = ? WHERE idProdotto = ?";
            $stmt = $this->db->prepare($sqlUpdate);
            $stmt->bind_param("di", $bidValue, $idProdotto);

            if (!$stmt->execute())
                throw new Exception("Errore aggiornamento prezzo.");
            $stmt->close();

            $this->db->commit();
            return ['success' => true, 'message' => "Offerta piazzata con successo!"];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
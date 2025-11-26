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

    public function saveProduct($formData, $loadedFile)
    {

        if ($this->db === null) {
            throw new Exception("Servizio temporaneamente non disponibile.");
        }
        $this->db->begin_transaction();

        try {
            $name = $formData['productName'];
            $description = $formData['productDescription'];
            $price = $formData['productPrice'];

            if (isset($formData['isAuction'])) {
                $endDate = $formData['auctionEndDate'];

            } else {
                $endDate = null;
            }

            $userId = $_SESSION['user_id'];




            $sql = "INSERT INTO prodotto (nome, descrizione, prezzo, stato, ragioneRifiuto, fineAsta, idUtente, idAdmin) 
                VALUES (?, ?, ?, ?, ?, ?,?,?)";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                $name,
                $description,
                $price,
                'attesa',
                null,
                $endDate,
                $userId,
                null
            ]);

            $productId = $this->db->insert_id;
            $this->saveImages($productId, $loadedFile['images']);

            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    public function saveImages($productId, $images)
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




    public function getProductsForUser()
    {


        if ($this->db === null) {
            return [];
        }

        try {


            $userId = $_SESSION['user_id'];



            $sql = "SELECT 
                    p.idProdotto, 
                    p.nome, 
                    p.prezzo, 
                    p.stato, 
                    p.fineAsta, 
                    (SELECT i.immagine FROM immagini i WHERE i.idProdotto = p.idProdotto LIMIT 1) AS immagineData
                FROM 
                    prodotto p
                WHERE 
                    p.idUtente = ?
                ORDER BY 
                    p.idProdotto DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);


            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            error_log("Error in getProductsForUser: " . $e->getMessage());
            return [];

        }
    }


    public function getFilteredProducts($filters, $isAdmin)
    {

        if ($this->db === null) {
            return [];
        }



        if (isset($_SESSION['user_id']) && !$isAdmin) {
            $userId = $_SESSION['user_id'];
        } else {
            $userId = -1;
        }


        try {

            if ($isAdmin) {

                $sql = "SELECT 
                    p.idProdotto, 
                    p.nome, 
                    p.prezzo, 
                    p.stato, 
                    p.fineAsta, 
                    (SELECT i.immagine FROM immagini i WHERE i.idProdotto = p.idProdotto LIMIT 1) AS immagineData
                FROM 
                    prodotto p 
                    
                    WHERE 
                   p.idUtente != ?  AND p.stato IN ('attesa')";

            } else {

                $sql = "SELECT 
                    p.idProdotto, 
                    p.nome, 
                    p.prezzo, 
                    p.stato, 
                    p.fineAsta, 
                    (SELECT i.immagine FROM immagini i WHERE i.idProdotto = p.idProdotto LIMIT 1) AS immagineData
                FROM 
                    prodotto p 
                    
                    WHERE 
                   p.idUtente != ? AND p.stato IN ('esposto', 'asta')";


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

            return $result->fetch_all(mode: MYSQLI_ASSOC);

        } catch (Exception $e) {
            error_log("Error in getFilteredProducts: " . $e->getMessage());
            return [];
        }
    }




    public function deleteProduct($formData)
    {

        if ($this->db === null) {
            throw new Exception("Servizio temporaneamente non disponibile.");
        }

        $productId = $formData['idProdotto'];


        $this->db->begin_transaction();
        try {

            $this->deleteImagesByProductId($productId);


            $sql_product = "DELETE FROM prodotto WHERE idProdotto = ?";
            $stmt_product = $this->db->prepare($sql_product);
            $stmt_product->execute([$productId]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }

    }


    public function updateProduct($formData, $loadedFile)
    {

        if ($this->db === null) {
            throw new Exception("Servizio non disponibile.");
        }

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

    public function changeProductStatus($productId, $decision, $rejectionReason = null)
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

        $queryUpdate = "UPDATE prodotto 
                    SET stato = ?, 
                        ragioneRifiuto = ?,
                        idAdmin = ?
                    WHERE idProdotto = ?";

        $stmt = $this->db->prepare($queryUpdate);

        $stmt->bind_param("ssii", $newStatus, $reasonToSave, $adminId, $productId);

        $esito = $stmt->execute();

        $stmt->close();

        return $esito;
    }

    //Funzioni di utility



    public function getProductById($productId)
    {

        if ($this->db === null) {
            error_log("getProductById fallito: Database non connesso.");
            return null;
        }
        $sql = "SELECT * FROM prodotto WHERE idProdotto = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deleteImagesByProductId($productId)
    {
        $sql = "DELETE FROM immagini WHERE idProdotto = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
    }
    public function getProfileImageByProductId($productId)
    {

        if ($this->db === null) {
            return null;
        }

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
            return $row['immagine'];

        } catch (Exception $e) {
            error_log("Error in getProfileImageByProductId: " . $e->getMessage());
            return null;
        }

    }

    public function getImagesByProductId($productId)
    {

        if ($this->db === null) {
            return [];
        }

        try {
            $sql = "SELECT idImmagine, immagine FROM immagini WHERE idProdotto = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
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


    public function getUserIdByProductId($productId)
    {
        try {
            $query = "SELECT idUtente FROM prodotto WHERE idProdotto = ?";


            $stmt = $this->db->prepare($query);

            $stmt->bind_param("i", $productId);

            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['idUtente'];


        } catch (Exception $e) {


            error_log("Error: " . $e->getMessage());
            return null;
        }
    }

}





?>
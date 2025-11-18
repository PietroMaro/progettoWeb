<?php
require_once __DIR__ . '/../db/database.php';


class productManager
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }





    public function saveProduct($formData, $loadedFile)
    {

        $name = $formData['productName'];
        $description = $formData['productDescription'];
        $price = $formData['productPrice'];

        if (isset($formData['isAuction'])) {
            $endDate = $formData['auctionEndDate'];

        } else {
            $endDate = null;
        }

        // $idUtente = $_SESSION['user_id'];
        $userId = 1; //fino alla creazione del login




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

        // $idUtente = $_SESSION['user_id'];
        $userId = 1; //fino alla creazione del login


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
    }


    // Funzioni che fanno query composte (che usano le transazioni)

    public function deleteProduct($formData)
    {

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
        $productId = $formData['idProdotto'];
        $name = $formData['productName'];
        $description = $formData['productDescription'];
        $price = $formData['productPrice'];
        $endDate = (isset($formData['isAuction'])) ? $formData['auctionEndDate'] : null;

        // $idUtente = $_SESSION['user_id'];
        $userId = 1; //fino alla creazione del login

        $this->db->begin_transaction();
        try {
            $sql = "UPDATE prodotto SET 
                            nome = ?, 
                            descrizione = ?, 
                            prezzo = ?, 
                            fineAsta = ?, 
                            stato = 'attesa'  
                        WHERE idProdotto = ? AND idUtente = ?";

            $stmt = $this->db->prepare($sql);

            $stmt->bind_param("ssdsii", $name, $description, $price, $endDate, $productId, $userId);
            $stmt->execute();


            if (isset($loadedFile['images']) && $loadedFile['images']['error'][0] == 0) {
                $this->deleteImagesByProductId($productId);
                $this->saveImages($productId, $loadedFile['images']);
            }

            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    //Funzioni di utility

    public function getProductById($productId)
    {
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

}





?>
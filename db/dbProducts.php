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

        foreach ($images['tmp_name'] as $tmp_path) {


            $imageData = file_get_contents($tmp_path);


            $stmt->execute([
                $imageData,
                $productId,
                null
            ]);

        }
    }
}


?>
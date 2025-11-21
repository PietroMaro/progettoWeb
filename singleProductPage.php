<?php
require_once 'bootstrap.php';

$handler = new ProductManager();

$fatalError = null;

try {
    if (!isset($_GET['id'])) {
        throw new Exception("Nessun prodotto selezionato.");
    }

    $productId = $_GET['id'];

    $prodottoTrovato = $handler->getProductById($productId);


    if ($prodottoTrovato === null) {
        throw new Exception("Prodotto non trovato o non disponibile.");
    }

    $templateParams["product"] = $prodottoTrovato;

    $templateParams["profileImg"] = $handler->getProfileImageByProductId($productId);
    $templateParams["productImages"] = $handler->getImagesByProductId($productId);

} catch (Exception $e) {

    error_log("Errore pagina prodotto: " . $e->getMessage());
    $fatalError = "Si è verificato un errore: " . $e->getMessage();
}





$templateParams["titolo"] = "Unisell - Product";
$templateParams["nome"] = "./templates/singleProductTemplate.php";
$templateParams["stylesheet"] = "css/singleProductPage.css";



require './templates/baseTemplate.php';
?>
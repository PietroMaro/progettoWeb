<?php
require_once 'bootstrap.php';
$products = [];

$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
try {
    $handler = new ProductManager();
    $chatManager = new ChatManager();


    $vincite = $handler->getAllWonAuctions();
    if (count($vincite) > 0) {
        foreach ($vincite as $vincita) {


            $idChat = $chatManager->checkChatExist($vincita['idProdotto'], $vincita['idVincitore'], $vincita['idVenditore']);
            if (!$idChat) {
                $idChat = $chatManager->createChat($vincita['idProdotto'], $vincita['idVenditore'], $vincita['idVincitore']);
            }
            $chatManager->addMessage(
                $vincita['idVincitore'],
                $idChat,
                "L'asta è finita e l'oggetto è stato vinto per: €" . $vincita['valore']
            );
        }
    }

    $handler->updateAuctions();


    $filters = [];
    if (isset($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }
    if (isset($_GET['filter_type'])) {
        $filters['filter_type'] = $_GET['filter_type'];
    }
    if (isset($_GET['sort'])) {
        $filters['sort'] = $_GET['sort'];
    }
    $products = $handler->getFilteredProducts($filters, $isAdmin);
} catch (Exception $e) {
    global $dbError;
    if (empty($dbError)) {
        $dbError = "Impossibile connettersi al Database. Riprova piu tardi";
    }
}
$templateParams["titolo"] = "Unisell - Home";
$templateParams["nome"] = "./templates/homeTemplate.php";
$templateParams["stylesheet"] = "css/homePage.css";
$templateParams["searchBar"] = "utils/searchBar.php";
$templateParams["products"] = $products;

require './templates/baseTemplate.php';
?>
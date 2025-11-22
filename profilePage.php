<?php
require_once 'bootstrap.php';

$handler = new UserManager();

$userId = null;


try {
    if (isset($_GET['userId'])) {
        $userId = $_GET['userId'];
    } elseif (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }


    $templateParams["userInfo"] = $handler->getUserInfo($userId);

} catch (Exception $e) {

    error_log("Errore pagina prodotto: " . $e->getMessage());
}

$templateParams["titolo"] = "Unisell - Profilo";
$templateParams["nome"] = "./templates/profileTemplate.php";
$templateParams["stylesheet"] = "css/profileTemplate.css";


require './templates/baseTemplate.php';
?>
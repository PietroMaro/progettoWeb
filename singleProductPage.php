<?php
require_once 'bootstrap.php';

$handler = new productManager();



if (isset($_GET['id'])) {
    $productId = $_GET['id'];
}



$templateParams["titolo"] = "Unisell - Product";
$templateParams["nome"] = "./templates/singleProductTemplate.php";
$templateParams["stylesheet"] = "css/singleProductPage.css";
$templateParams["product"] = $handler->getProductById($productId);
$templateParams["profileImg"] = $handler->getProfileImageByProductId($productId);;
$templateParams["productImages"] = $handler->getImagesByrProductId($productId);

require './templates/baseTemplate.php';
?>
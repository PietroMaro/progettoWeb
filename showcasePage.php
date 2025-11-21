<?php
require_once 'bootstrap.php';

$handler = new ProductManager();


$templateParams["titolo"] = "Unisell - Vetrina";
$templateParams["nome"] = "./templates/showcaseTemplate.php";
$templateParams["stylesheet"] = "css/showcasePage.css";

$templateParams["products"] = $handler->getProductsForUser();



require './templates/baseTemplate.php';
?>
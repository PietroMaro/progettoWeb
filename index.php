<?php
require_once 'bootstrap.php';

$handler = new productManager();


$templateParams["titolo"] = "Unisell - Home";
$templateParams["nome"] = "./templates/showcaseTemplate.php";
$templateParams["stylesheet"] = "css/showcasePage.css";

$templateParams["products"] = $handler->getProductsForUser();



require './templates/baseTemplate.php';
?>
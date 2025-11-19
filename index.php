<?php
require_once 'bootstrap.php';

$handler = new productManager();

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



$templateParams["titolo"] = "Unisell - Home";
$templateParams["nome"] = "./templates/homeTemplate.php";
$templateParams["stylesheet"] = "css/homePage.css";
$templateParams["searchBar"] = "utils/searchBar.php"; 
$templateParams["products"] = $handler->getFilteredProducts($filters);


require './templates/baseTemplate.php';
?>
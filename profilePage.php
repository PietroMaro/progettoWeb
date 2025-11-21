<?php
require_once 'bootstrap.php';


$handler = new UserManager();


$templateParams["titolo"] = "Unisell - Profilo";
$templateParams["nome"] = "./templates/profileTemplate.php";
$templateParams["stylesheet"] = "css/profileTemplate.css";
$templateParams["userInfo"] = $handler->getUserInfo(1); //$_SESSION



require './templates/baseTemplate.php';
?>
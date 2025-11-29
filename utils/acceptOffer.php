<?php

require_once __DIR__ . '/../bootstrap.php';




if ($_SERVER["REQUEST_METHOD"] == "POST") {



    $handler = new ProductManager();
    $idChat = $handler->acceptOffer($_POST["chatId"]);

    header("Location: ../index.php");
}
?>
<?php

require_once __DIR__ . '/../bootstrap.php';




if ($_SERVER["REQUEST_METHOD"] == "POST") {



    $handler = new ProductManager();
    $idChat = $handler->acceptOffer($_POST["chatId"]);

    unset($_SESSION['idChatSelected']);
    unset($_SESSION['listIdChatSelected']);
    unset($_SESSION['productNameChatSelected']);
    unset($_SESSION['productBlobChatSelected']);
    unset($_SESSION['userNameChatSelected']);
    unset($_SESSION['userBlobChatSelected']);

    header("Location: ../index.php");
}
?>
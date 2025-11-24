<?php
require_once '../bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new ProductManager();

    $productId = $_POST['id_oggetto'];
    $decision = $_POST['decision'];

    $rejectionReason = isset($_POST['ragione_rifiuto']) ? trim($_POST['ragione_rifiuto']) : null;


    $handler->changeProductStatus($productId, $decision, $rejectionReason);

    header("Location: ../index.php");
    exit();
}
?>
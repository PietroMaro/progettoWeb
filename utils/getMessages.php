<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db/dbChat.php';
header('Content-Type: application/json');
session_start();
$dbHandler = null; 
try {
    $dbHandler = new ChatManager();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

$idChat = $_SESSION['idChatSelected'] ?? null;
$idUser = $_SESSION['user_id'] ?? null;
$lastProgressivo = $_GET['last_prog'] ?? 0; 

if ($idChat === null || $idUser === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Chat ID or User ID missing.']);
    exit();
}

try {
    $newMessages = $dbHandler->getNewMessages($idChat, $idUser, $lastProgressivo);
    echo json_encode(['messages' => $newMessages]);
    
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching messages: ' . $e->getMessage()]);
}
exit();
?>
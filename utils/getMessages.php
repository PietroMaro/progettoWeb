<?php
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
    foreach ($newMessages as &$msg) {
        if (!empty($msg['immage_blob'])) {
            $msg['immage_blob'] = base64_encode($msg['immage_blob']);
        }
    }
    unset($msg);
    $jsonOutput = json_encode(['messages' => $newMessages]);
    if ($jsonOutput === false) {
        throw new Exception('JSON Encoding Failed: ' . json_last_error_msg());
    }
    echo $jsonOutput;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server Error: ' . $e->getMessage()]);
}
exit();
?>
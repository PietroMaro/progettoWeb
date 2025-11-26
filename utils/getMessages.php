<?php
// Increase memory limit for handling image processing
//ini_set('memory_limit', '256M'); 

ini_set('display_errors', 0); // Hide warnings from output (they break JSON)
ini_set('log_errors', 1);     // Log errors to server logs instead
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
    // 1. Get messages from DB
    $newMessages = $dbHandler->getNewMessages($idChat, $idUser, $lastProgressivo);

    // 2. ENCODE THE IMAGE
    // We use the exact alias from your SQL: 'immage_blob'
    foreach ($newMessages as &$msg) {
        if (!empty($msg['immage_blob'])) {
            $msg['immage_blob'] = base64_encode($msg['immage_blob']);
        }
    }
    unset($msg); // Break reference

    // 3. Encode to JSON
    $jsonOutput = json_encode(['messages' => $newMessages]);

    // 4. Check for errors
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
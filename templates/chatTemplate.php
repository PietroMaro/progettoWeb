<?php
$chatBlocksHtml = "";
$idChatSelected = null;
$dbHandler = null;
try {
    $dbHandler = new ChatManager();
    $productHandler = new ProductManager();
} catch (Exception $e) {
    $chatBlocksHtml = errorBlock();
}

$chatFinished = false;
$disableOfferButton = false;



?>



<?php

require_once __DIR__ . "/../utils/chatUtils.php";
require_once __DIR__ . '/../utils/offertaChat.php';
require_once __DIR__ . '/../utils/segnalaChat.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['is_new_chat_message'])) {

    if (!$dbHandler) {
        return;
    }

    $idSender = $_SESSION['user_id'] ?? null;
    $idChat = $_SESSION['idChatSelected'] ?? null;
    $messageContent = $_POST['chat-message'] ?? null;

    $immage_blob = null;
    if (isset($_FILES['chat-image']) && $_FILES['chat-image']['error'] == UPLOAD_ERR_OK) {

        $immage_blob = file_get_contents($_FILES['chat-image']['tmp_name']);

        if ($immage_blob === FALSE || $immage_blob === "") {
            error_log("Failed to read image content or file was empty for chat ID: " . $idChat);
            $immage_blob = null;
        }
    }

    if ($idSender !== null && $idChat !== null) {

        $hasContent = (!empty($messageContent) || $immage_blob !== null);

        if ($hasContent) {
            try {
                $dbHandler->addMessage($idSender, $idChat, $messageContent, $immage_blob);
                $_POST['chat-image'] = null;
                $_POST['chat-message'] = null;
            } catch (Exception $e) {
                error_log("Error adding message: " . $e->getMessage());
            }
        }
    } else {
        error_log("Missing required IDs for new chat message.");
    }
}




?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_selected_chat_list_id'])) {
    ifPostSetSession('new_selected_chat_id', 'idChatSelected');
    ifPostSetSession('new_selected_chat_list_id', 'listIdChatSelected');
    ifPostSetSession('new_selected_chat_product_name', 'productNameChatSelected');
    ifPostSetSession('new_selected_chat_product_blob', 'productBlobChatSelected');
    ifPostSetSession('new_selected_chat_user_name', 'userNameChatSelected');
    ifPostSetSession('new_selected_chat_user_blob', 'userBlobChatSelected');



    $chatStatus = $productHandler->productStatus($_SESSION['idChatSelected']);

    if ($chatStatus === "venduto") {
        $chatFinished = true;
        $disableOfferButton = true;

    } elseif ($chatStatus === "asta") {
        $disableOfferButton = true;
    } elseif ($chatStatus === "astaDeserta") {
        $chatFinished = true;
    }

}
?>

<?php
$idChat = -1;
if ($dbHandler) {
    try {
        $userChats = $dbHandler->getUserChats($_SESSION['user_id']);
        $chatBlocksHtml = '';
        if (empty($userChats)) {
            $chatBlocksHtml = noChatBlock();
        } else {
            $listIdChat = 0;
            $reversedChats = array_reverse($userChats);
            foreach ($reversedChats as $chatData) {
                $blobUtente = $chatData['immageNotYou'] ?? '';
                $blobProdotto = $chatData['immageProdotto'] ?? '';
                $nomeUtente = $chatData['nomeNotYou'] ?? '';
                $nomeProdotto = $chatData['nomeProdotto'] ?? '';
                $idChat = $chatData['idChat'] ?? '';


                $isSelected = ($idChat == ($_SESSION['idChatSelected'] ?? null));

                $chatBlocksHtml .= chatBlock(
                    $blobUtente,
                    $blobProdotto,
                    $nomeUtente,
                    $nomeProdotto,
                    $idChat,
                    $listIdChat,
                    $isSelected
                );
                $listIdChat += 1;
            }
        }
    } catch (Throwable $e) {
        $chatBlocksHtml = errorBlock();
    }
}
?>

<div id="chat-app">
    <aside aria-label="Lista contatti">
        <ul>
            <?= $chatBlocksHtml ?>
        </ul>
    </aside>
    <?= currentChat($chatFinished, $disableOfferButton) ?>
</div>

<?= offertaChatModal() ?>
<?= segnalaChatModal() ?>
<?= deleteChatModal($idChat) ?>

<?php
function chatBlock($blobUtente, $blobProdotto, $nomeUtente, $nomeProdotto, $chatId, $chatListId, $isSelected)
{
    $ariaLabel = "Chat con " . $nomeUtente;
    $currentAttr = $isSelected ? 'aria-current="true"' : '';

    return <<<HTML
    <li {$currentAttr}>
        <form action="#" method="POST" style="margin: 0; padding: 0; display: block;">
            
            <input type="hidden" name="new_selected_chat_id" value="{$chatId}">
            <input type="hidden" name="new_selected_chat_list_id" value="{$chatListId}">
            <input type="hidden" name="new_selected_chat_product_name" value="{$nomeProdotto}">
            <input type="hidden" name="new_selected_chat_product_blob" value="{$blobProdotto}">
            <input type="hidden" name="new_selected_chat_user_name" value="{$nomeUtente}">
            <input type="hidden" name="new_selected_chat_user_blob" value="{$blobUtente}">

            <button type="submit" aria-label="{$ariaLabel}" style="width: 100%; border: none; background: none; padding: 0; cursor: pointer;">
                <span style="display: flex; align-items: center; padding: 12px 15px; width: 100%;">
                    
                    <span aria-hidden="true" style="margin-right: 15px; display: block; position: relative;">
                        <img src="{$blobProdotto}" alt="{$nomeProdotto}">
                        <img src="{$blobUtente}" alt="{$nomeUtente}">
                    </span>
                    
                    <span class="text-info" style="text-align: left; flex-grow: 1; display: block;">
                        <span class="name-row" style="display: block;">
                            <strong>{$nomeUtente}</strong>
                        </span>
                        <small><strong> Prodotto: {$nomeProdotto}</strong></small>
                    </span>

                </span>
            </button>
        </form>
    </li>
    HTML;
}
?>

<?php
function singleChatOffer($isMine, $content, $messageProgressivo = 0, $chatFinished)
{
    $whoSent = $isMine ? "sent" : "received";

    if (!$isMine) {
        $headerText = "La tua offerta";
        $icon = '<i class="fas fa-arrow-up text-secondary"></i>';
        $footerHtml = <<<HTML
                <div class="mt-2 pt-2 border-top border-secondary-subtle text-muted small fst-italic text-center">
                    <i class="fas fa-clock me-1"></i> In attesa di risposta...
                </div>
            HTML;
    } else {
        $headerText = "Offerta ricevuta";
        $icon = '<i class="fas fa-tag text-success"></i>';
        $footerHtml = <<<HTML
                <div class="mt-3">
                    <form method="POST" action="utils/acceptOffer.php">

            <input type="hidden" name="chatId" value="{$_SESSION['idChatSelected']}">

                     <button type="submit" class="btn btn-success btn-sm w-100 fw-bold shadow-sm" style="border-radius: 20px;">
                            Accetta Offerta
                        </button>
                    </form>

                </div>
            HTML;
    }

    if ($chatFinished) {
        $footerHtml = <<<HTML
                <div class="mt-2 pt-2 border-top border-secondary-subtle text-muted small fst-italic text-center">
                    <i class="fas fa-clock me-1"></i> L'offerta è stata accettata!
                </div>
            HTML;
    }

    return <<<HTML
        <article data-type="{$whoSent}" data-progressivo="{$messageProgressivo}">
            <div style="min-width: 220px;">
                
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <strong>
                        {$headerText}                                         
                
                    </strong>    

                    </small>
                    {$icon}
                </div>
    
                <div class="text-center py-1">
                    <span class="display-6 fw-bold text-dark">€ {$content}</span>
                </div>
    
                {$footerHtml}
    
            </div>
        </article>
        HTML;
}

function currentChatHeader($disableOfferButton)
{
    if (!isset($_SESSION['idChatSelected'])) {
        return "";
    }
    $blobUser = $_SESSION['userBlobChatSelected'] ?? null;
    $blobProduct = $_SESSION['productBlobChatSelected'] ?? null;
    $nameUser = $_SESSION['userNameChatSelected'] ?? null;
    $offerButtonHtml = "";


    if ($disableOfferButton == false) {
        $offerButtonHtml = <<<BTN
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalOfferta">
                Offerta
            </button>
        BTN;
    }

    return <<<HTML
        <header class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
            <div class="user-info d-flex align-items-center">
                <div aria-hidden="true" class="d-flex align-items-center gap-2 me-3">
                    <img src="{$blobProduct}" alt="Image product" class="rounded-circle border">
                    <img src="{$blobUser}" alt="Image user" class="rounded-circle border border-2 border-white">
                </div>
                <h2 class="h5 m-0">{$nameUser}</h2>
            </div>

            <nav class="d-flex gap-2">
                {$offerButtonHtml}

                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalSegnala">
                    Segnala
                </button>

                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalCancella">
                    Cancella Chat
                </button>
            </nav>

            

        </header>
        HTML;
}

function currentChatFooter($chatFinished)
{
    if (!isset($_SESSION['idChatSelected']) || $chatFinished) {
        return "";
    }
    return <<<HTML
            <footer>
                <form id="chat-form" action="#" method="POST" enctype="multipart/form-data">

                    <div id="chat-input-container"> 

                        <div id="image-preview" style="display: none;">
                            <img id="preview-image" src="placeholder" alt="Selected Photo" style="max-height: 50px; border-radius: 4px; object-fit: cover;">
                            <button type="button" id="remove-image-btn" aria-label="Rimuovi immagine">
                                <i class="bi-x-circle-fill"></i>
                            </button>
                        </div>

                        <input type="file" id="image-upload" name="chat-image" accept="image/*" style="display: none;">

                        <label for="message-input">Scrivi un messaggio</label>
                        <input type="text" id="message-input" name="chat-message" placeholder="Scrivi un messaggio" autocomplete="off">
                    </div>

                    <label for="image-upload" class="input-trigger-btn">
                    <i class="bi-camera" aria-hidden="true"></i>
    
                    <span class="visually-hidden">Allega Foto o Immagine</span>
                        </label>
                    <input type="hidden" name="is_new_chat_message" value="true">

                    <button type="submit" aria-label="Invia Messaggio">
                        <i class="bi-send"></i>
                    </button>
                </form>
            </footer>
        HTML;
}
function deleteChatModal($idChat)
{
    return <<<HTML
    <div class="modal fade" id="modalCancella" tabindex="-1" aria-labelledby="modalCancellaLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalCancellaLabel">Conferma Cancellazione</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler cancellare questa chat? <br>
                <span class="text-danger"><strong>Attenzione:</strong> l'azione è irreversibile e tutti i messaggi verranno eliminati.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, annulla</button>
                
                <form action="utils/deleteChat.php" method="POST" style="display: inline;">
                    <input type="hidden" name="idChat" value="<?php echo $idChat; ?>">
                    <button type="submit" class="btn btn-danger">Sì, cancella</button>
                </form>
            </div>
        </div>
    </div>
    </div>
    HTML;
}
?>

<script>
    window.CHAT_CONFIG = {
        idChat: <?php echo json_encode($_SESSION['idChatSelected'] ?? null); ?>,
        userId: <?php echo json_encode($_SESSION['user_id'] ?? 0); ?>
    };
</script>

<script src="templates/chatTemplate.js"></script>
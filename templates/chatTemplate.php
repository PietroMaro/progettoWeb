<?php
    $chatBlocksHtml = "";
    $idChatSelected = null;
    $dbHandler = null; 
    try {
        $dbHandler = new ChatManager();
    } catch (Exception $e) {
        $chatBlocksHtml = errorBlock();
    }
?>

<?php

    require_once __DIR__ . '/../utils/offertaChat.php';
    require_once __DIR__ . '/../utils/segnalaChat.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['is_new_chat_message'])) {
        
        if(!$dbHandler){
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
        
        if(isset($_POST['new_selected_chat_id'])){$_SESSION['idChatSelected'] = $_POST['new_selected_chat_id'];}
        if(isset($_POST['new_selected_chat_list_id'])){$_SESSION['listIdChatSelected'] = $_POST['new_selected_chat_list_id'];}
        if(isset($_POST['new_selected_chat_product_name'])){$_SESSION['productNameChatSelected'] = $_POST['new_selected_chat_product_name'];}
        if(isset($_POST['new_selected_chat_product_blob'])){$_SESSION['productBlobChatSelected'] = $_POST['new_selected_chat_product_blob'];}
        if(isset($_POST['new_selected_chat_user_name'])){$_SESSION['userNameChatSelected'] = $_POST['new_selected_chat_user_name'];}
        if(isset($_POST['new_selected_chat_user_blob'])){$_SESSION['userBlobChatSelected'] = $_POST['new_selected_chat_user_blob'];}
    }
?>

<?php
    if($dbHandler){
        try {
            $userChats = $dbHandler->getUserChats($_SESSION['user_id']);
            $chatBlocksHtml = '';
            if (empty($userChats)) {
                $chatBlocksHtml = noChatBlock();
            } else {
                $listIdChat = 0;
                foreach ($userChats as $chatData) {
                    $blobUtente   = $chatData['immageNotYou'] ?? '';     
                    $blobProdotto = $chatData['immageProdotto'] ?? '';   
                    $nomeUtente   = $chatData['nomeNotYou'] ?? ''; 
                    $nomeProdotto = $chatData['nomeProdotto'] ?? ''; 
                    $idChat = $chatData['idChat'] ?? ''; 
                    $chatBlocksHtml .= chatBlock(
                        $blobUtente, 
                        $blobProdotto, 
                        $nomeUtente, 
                        $nomeProdotto,
                        $idChat,
                        $listIdChat,
                        $listIdChat == ($_SESSION['listIdChatSelected'] ?? null)
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
            <?=$chatBlocksHtml?>
        </ul>
    </aside>
    <?=currentChat()?>
</div>

<?=offertaChatModal()?>
<?=segnalaChatModal()?>

<?php
function chatBlock($blobUtente, $blobProdotto, $nomeUtente, $nomeProdotto, $chatId ,$chatListId ,$isSelected){
    $ariaLabel = "Chat con " . $nomeUtente;
    $currentAttr = $isSelected ? 'aria-current="true"' : '';
    return <<<HTML
    <li {$currentAttr}>
        <form action="" method="POST" style="margin: 0; padding: 0; display: block;">
            
            <input type="hidden" name="new_selected_chat_id" value="{$chatId}">
            <input type="hidden" name="new_selected_chat_list_id" value="{$chatListId}">
            <input type="hidden" name="new_selected_chat_product_name" value="{$nomeProdotto}">
            <input type="hidden" name="new_selected_chat_product_blob" value="{$blobProdotto}">
            <input type="hidden" name="new_selected_chat_user_name" value="{$nomeUtente}">
            <input type="hidden" name="new_selected_chat_user_blob" value="{$blobUtente}">

            <button type="submit" aria-label="{$ariaLabel}" style="width: 100%; border: none; background: none; padding: 0; cursor: pointer;">
                <div style="display: flex; align-items: center; padding: 12px 15px; width: 100%;">
                    <div aria-hidden="true" style="margin-right: 15px;">
                        <img src="{$blobProdotto}" alt="{$nomeProdotto}">
                        <img src="{$blobUtente}" alt="{$nomeUtente}">
                    </div>
                    <div class="text-info" style="text-align: left; flex-grow: 1;">
                        <div class="name-row">
                            <strong>{$nomeUtente}</strong>
                        </div>
                        <small>Prodotto: {$nomeProdotto}</small>
                    </div>
                </div>
            </button>
        </form>
    </li>
    HTML;
}
function noChatBlock() {
    return <<<HTML
    <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color:rgb(0, 0, 0); border: 1px solid #ffeeba; border-radius: 5px;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Nessuna Chat disponibile</h2>
        <p style="margin-bottom: 1.5rem;">Al momento non hai chat aperte, aspetta di esserre contattato se sei un venditore, o esplora i prodotti se vuoi contattare qualcuno</p>
    </div>
HTML;
}
function errorBlock() {
    return <<<HTML
    <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color:rgb(0, 0, 0); border: 1px solid #ffeeba; border-radius: 5px;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Nessuna Chat disponibile</h2>
        <p style="margin-bottom: 1.5rem;">Al momento il servizio è temporaneamente non disponibile. Ci scusiamo per il disagio.</p>
    </div>
HTML;
}
?>

<?php

    function noChatSelectBlock() {
        return <<<HTML
        <div class="alert alert-warning text-center p-5" style="background-color:rgb(211, 211, 211); color:rgb(0, 0, 0); border: 1px solid #ffeeba; border-radius: 5px;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Nessuna Chat disponibile</h2>
            <p style="margin-bottom: 1.5rem;">Seleziona una chat per visualizzare i messaggi</p>
        </div>
        HTML;
    }

    function currentChat(){
        $header = currentChatHeader();
        $body = currentChatBody();
        $footer = currentChatFooter(); 

        if($body == errorBlock()){
            $footer = "";
        }
        return <<<HTML
            <main role="main" aria-label="Conversazione corrente">
                        {$header}
                        {$body} 
                        {$footer} 
                    </main>
        HTML;
    }

    function currentChatBody(){
        try {
            $dbHandler = new ChatManager();
        } catch (Exception $e) {
            $dbHandler = null;
        }

        if (!isset($_SESSION['idChatSelected'])) {
            return noChatSelectBlock(); 
        } else {
            $idChat = $_SESSION['idChatSelected'];
            $result= "";
            try {
                if(!$dbHandler){
                    return errorBlock();
                }
                $history = $dbHandler->getChatHistory($idChat);
                if(empty($history)){
                    $result = noMessagesBlock();
                }else{
                    foreach($history as $row){
                        $isMine = ($row['idMandante'] != $_SESSION['user_id']);
                        $image_data = $row['image'] ?? null;
                        $messageProgressivo = $row['progressivo'] ?? null;
                        if($row['type'] === 'message'){
                            $result .= singleChatMessage($isMine,$row['content'],$image_data,$messageProgressivo);
                        } else if($row['type'] === 'offer'){
                            $result .= singleChatOffer($isMine,$row['content'],$messageProgressivo);
                        }
                    }
                }
            } catch (Throwable $e) {
                $result .= "<div style='color:red; padding: 20px;'>Error: " . $e->getMessage() . "</div>";
            }
            return <<<HTML
                <section aria-live="polite" aria-label="Cronologia messaggi">
                    {$result}
                </section>
            HTML;
        }
    }

    function singleChatMessage($isMine, $content, $image_data = null, $messageProgressivo = 0){
        $whoSent = $isMine ? "sent" : "received";
        $imageHtml = '';
        if ($image_data !== null && $image_data !== '') {
            $base64Image = base64_encode($image_data);
            $mimeType = 'image/jpeg'; 
            $imageSrc = 'data:' . $mimeType . ';base64,' . $base64Image;
            $imageHtml = <<<IMG
                <img src="{$image_data}" alt="C'è stato un errore nel caricare l'immagine, ci scusiamo per il disagio. Prova a ricarcare la pagina" style="max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 5px;">
            IMG;
        }
        $textHtml = !empty($content) ? "<p>{$content}</p>" : '';
        return <<<HTML
            <article data-type={$whoSent} data-progressivo={$messageProgressivo}>
                <div>
                    {$imageHtml}
                    {$textHtml}
                </div>
            </article>
        HTML;
    }

    function singleChatOffer($isMine, $content, $messageProgressivo = 0){
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
                    <button type="button" class="btn btn-success btn-sm w-100 fw-bold shadow-sm" style="border-radius: 20px;">
                        Accetta Offerta
                    </button>
                </div>
            HTML;
        }
    
        return <<<HTML
        <article data-type="{$whoSent}" data-progressivo="{$messageProgressivo}">
            <div style="min-width: 220px;">
                
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="fw-bold text-uppercase text-secondary" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        {$headerText}
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

    function currentChatHeader(){
        if(!isset($_SESSION['idChatSelected'])){
            return "";
        }
        $blobUser = $_SESSION['userBlobChatSelected'] ?? null;
        $blobProduct= $_SESSION['productBlobChatSelected'] ?? null;
        $nameUser = $_SESSION['userNameChatSelected'] ?? null;
        return <<<HTML
        <header class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
            <div class="user-info d-flex align-items-center">
                <div aria-hidden="true" class="d-flex align-items-center gap-2 me-3">
                    <img src="{$blobProduct}" alt="ALT" class="rounded-circle border">
                    <img src="{$blobUser}" alt="ALT" class="rounded-circle border border-2 border-white">
                </div>
                <h2 class="h5 m-0">{$nameUser}</h2>
            </div>

            <nav class="d-flex gap-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalOfferta">
                    Offerta
                </button>

                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalSegnala">
                    Segnala
                </button>
            </nav>

        </header>
        HTML;
    }

    function currentChatFooter(){
        if(!isset($_SESSION['idChatSelected'])){
            return "";
        }
        return <<<HTML
            <footer>
                <form id="chat-form" action="" method="POST" enctype="multipart/form-data">

                    <div id="chat-input-container"> 

                        <div id="image-preview" style="display: none;">
                            <img id="preview-image" src="" alt="Selected Photo" style="max-height: 50px; border-radius: 4px; object-fit: cover;">
                            <button type="button" id="remove-image-btn" aria-label="Rimuovi immagine">
                                <i class="bi-x-circle-fill"></i>
                            </button>
                        </div>

                        <input type="file" id="image-upload" name="chat-image" accept="image/*" style="display: none;">

                        <label for="message-input">Scrivi un messaggio</label>
                        <input type="text" id="message-input" name="chat-message" placeholder="Scrivi un messaggio" autocomplete="off">
                    </div>

                    <label for="image-upload" class="input-trigger-btn" aria-label="Allega Foto/Immagine">
                        <i class="bi-camera"></i> 
                    </label>

                    <input type="hidden" name="is_new_chat_message" value="true">

                    <button type="submit" aria-label="Invia Messaggio">
                        <i class="bi-send"></i>
                    </button>
                </form>
            </footer>
        HTML;
    }

    function noMessagesBlock() {
        return <<<HTML
        <div class="alert alert-warning text-center p-5" style="background-color:rgb(199, 199, 199); color:rgb(0, 0, 0); border: 1px solid #ffeeba; border-radius: 5px;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Nessun messagio</h2>
            <p style="margin-bottom: 1.5rem;">Al momento non ci sono messaggi in questa chat, non essere timido :)</p>
        </div>
    HTML;
    }
    
    function errorChatBlock() {
        return <<<HTML
        <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color:rgb(0, 0, 0); border: 1px solid #ffeeba; border-radius: 5px;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Chat non disponibile</h2>
            <p style="margin-bottom: 1.5rem;">Al momento il servizio è temporaneamente non disponibile. Ci scusiamo per il disagio.</p>
        </div>
        HTML;
    }
?>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const messageContainer = document.querySelector('main section[aria-label="Cronologia messaggi"]');
    if (messageContainer) {
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }
    const chatForm = document.getElementById('chat-form');
    const fileInput = document.getElementById('image-upload');
    const messageInput = document.getElementById('message-input');
    const sendButton = chatForm.querySelector('[type="submit"]'); 
    const previewContainer = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const removeBtn = document.getElementById('remove-image-btn');
    function checkFormValidity() {
        const hasText = messageInput.value.trim().length > 0;
        const hasImage = fileInput.files.length > 0;
        sendButton.disabled = !(hasText || hasImage);
    }
    checkFormValidity();
    messageInput.addEventListener('input', checkFormValidity);
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'flex';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
        checkFormValidity(); 
        messageInput.focus();
    });
    removeBtn.addEventListener('click', (e) => {
        e.preventDefault(); 
        fileInput.value = ''; 
        previewContainer.style.display = 'none';
        previewImage.src = '';
        checkFormValidity();
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    let lastReceivedProgressivo = 0;
    const POLLING_INTERVAL = 1000; 
    const messageContainer = document.querySelector('main section[aria-label="Cronologia messaggi"]');
    const idChat = <?php echo json_encode($_SESSION['idChatSelected'] ?? null); ?>;
    const currentUserId = <?php echo json_encode($_SESSION['user_id'] ?? 0); ?>;
    const base64ToBlob = (base64, mimeType = 'image/jpeg') => {
        try {
            const byteCharacters = atob(base64);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], { type: mimeType });
            return URL.createObjectURL(blob);
        } catch (e) {
            console.error("Failed to convert base64 to blob", e);
            return null;
        }
    };

    const initializeLastProgressivo = () => {
        const messages = messageContainer.querySelectorAll('article');
        messages.forEach(msg => {
            const prog = parseInt(msg.dataset.progressivo);
            if (!isNaN(prog) && prog > lastReceivedProgressivo) {
                lastReceivedProgressivo = prog;
            }
        });
    };

    const renderMessage = (row, idCurrentUser) => {
        const isMine = (row.idMandante == idCurrentUser); 
        const whoSent = !isMine ? "sent" : "received";
    
        if (row.type === 'offer') {
            let headerText, icon, footerHtml;

            if (isMine) {
                headerText = "La tua offerta";
                icon = '<i class="fas fa-arrow-up text-secondary"></i>';
                footerHtml = `
                    <div class="mt-2 pt-2 border-top border-secondary-subtle text-muted small fst-italic text-center">
                        <i class="fas fa-clock me-1"></i> In attesa di risposta...
                    </div>`;
            } else {
                headerText = "Offerta ricevuta";
                icon = '<i class="fas fa-tag text-success"></i>';
                footerHtml = `
                    <div class="mt-3">
                        <button type="button" class="btn btn-success btn-sm w-100 fw-bold shadow-sm" style="border-radius: 20px;">
                            Accetta Offerta
                        </button>
                    </div>`;
            }

            return `
            <article data-type="${whoSent}" data-progressivo="${row.progressivo}">
                <div style="min-width: 220px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="fw-bold text-uppercase text-secondary" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            ${headerText}
                        </small>
                        ${icon}
                    </div>
                    <div class="text-center py-1">
                        <span class="display-6 fw-bold text-dark">€ ${row.content}</span>
                    </div>
                    ${footerHtml}
                </div>
            </article>`;
        }

        let content = row.content || '';
        let base64String = row.immage_blob || null; 
        let imageHtml = '';
    
        if (base64String) {
            const blobUrl = base64ToBlob(base64String, 'image/jpeg');
            if (blobUrl) {
                imageHtml = `<img src="${blobUrl}" alt="Immagine" style="max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 5px;">`;
            }
        }

        const textHtml = content ? `<p>${content}</p>` : '';

        return `
            <article data-type="${whoSent}" data-progressivo="${row.progressivo}">
                <div>
                    ${imageHtml}
                    ${textHtml}
                </div>
            </article>
        `;
    };

    const pollForNewMessages = async () => {
        if (!messageContainer || !idChat) {
            return; 
        }
        try {
            const response = await fetch(`../utils/getMessages.php?last_prog=${lastReceivedProgressivo}`);
            if(!response.ok){ 
                return
                `       
                    <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color:rgb(0, 0, 0); border: 1px solid #ffeeba; border-radius: 5px;">
                        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Chat non disponibile</h2>
                        <p style="margin-bottom: 1.5rem;">Al momento il servizio è temporaneamente non disponibile. Ci scusiamo per il disagio.</p>
                    </div>
                `; 
                //throw new Error("Network response was not ok");
            }
            const data = await response.json();
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    const newMessageHtml = renderMessage(msg, currentUserId);
                    messageContainer.insertAdjacentHTML('beforeend', newMessageHtml);
                    
                    if (msg.progressivo > lastReceivedProgressivo) {
                        lastReceivedProgressivo = msg.progressivo;
                    }
                });
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        } catch (error) {
            console.error('Polling failed:', error);
        }
        setTimeout(pollForNewMessages, POLLING_INTERVAL);
    };
    if (messageContainer) {
        messageContainer.scrollTop = messageContainer.scrollHeight;
        initializeLastProgressivo();
        if (idChat !== null) {
            pollForNewMessages();
        }
    }
});
</script>
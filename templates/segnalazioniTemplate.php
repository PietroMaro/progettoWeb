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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    }
?>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_new_selected_chat_list_id'])) {
        if(isset($_POST['admin_new_selected_chat_id'])){$_SESSION['idChatSelected'] = $_POST['admin_new_selected_chat_id'];}
        if(isset($_POST['admin_new_selected_chat_list_id'])){$_SESSION['listIdChatSelected'] = $_POST['admin_new_selected_chat_list_id'];}
        if(isset($_POST['admin_new_selected_chat_reporter_name'])){$_SESSION['reporterNameChatSelected'] = $_POST['admin_new_selected_chat_reporter_name'];}
        if(isset($_POST['admin_new_selected_chat_reported_name'])){$_SESSION['reportedNameChatSelected'] = $_POST['admin_new_selected_chat_reported_name'];}
        if(isset($_POST['admin_new_selected_chat_reporter_blob'])){$_SESSION['reporterBlobChatSelected'] = $_POST['admin_new_selected_chat_reporter_blob'];}
        if(isset($_POST['admin_new_selected_chat_reported_blob'])){$_SESSION['reportedBlobChatSelected'] = $_POST['admin_new_selected_chat_reported_blob'];}
        if(isset($_POST['admin_new_selected_chat_type_report'])){$_SESSION['typeReportChatSelected'] = $_POST['admin_new_selected_chat_type_report'];}
        if(isset($_POST['admin_new_selected_chat_message_report'])){$_SESSION['messageReportChatSelected'] = $_POST['admin_new_selected_chat_message_report'];}
    }
?>

<?php
    if($dbHandler){
        try {
            $userChats = $dbHandler->getOpenReports();
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
                        $chatData['imageReporter'] ?? '', 
                        $chatData['imageReported'] ?? '', 
                        $chatData['nomeReporter'] ?? '', 
                        $chatData['nomeReported'] ?? '', 
                        $chatData['tipoSegnalazione'] ?? '', 
                        $chatData['testo'] ?? '', 
                        $chatData['idChat'] ?? '', 
                        $listIdChat, 
                        $_SESSION['listIdChatSelected'] === $listIdChat
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
function chatBlock($blobReporter, $blobReported, $nomeReporter, $nomeReported, $typeReport, $messageReport, $chatId ,$chatListId ,$isSelected){
    $ariaLabel = $nomeReporter . "segnala " . $nomeReported;
    $currentAttr = $isSelected ? 'aria-current="true"' : '';
    return <<<HTML
    <li {$currentAttr}>
        <form action="" method="POST" style="margin: 0; padding: 0; display: block;">
            
            <input type="hidden" name="admin_new_selected_chat_id" value="{$chatId}">
            <input type="hidden" name="admin_new_selected_chat_list_id" value="{$chatListId}">
            <input type="hidden" name="admin_new_selected_chat_reporter_name" value="{$nomeReporter}">
            <input type="hidden" name="admin_new_selected_chat_reported_name" value="{$nomeReported}">
            <input type="hidden" name="admin_new_selected_chat_reporter_blob" value="{$blobReporter}">
            <input type="hidden" name="admin_new_selected_chat_reported_blob" value="{$blobReported}">
            <input type="hidden" name="admin_new_selected_chat_type_report" value="{$typeReport}">
            <input type="hidden" name="admin_new_selected_chat_message_report" value="{$messageReport}">

            <button type="submit" aria-label="{$ariaLabel}" style="width: 100%; border: none; background: none; padding: 0; cursor: pointer;">
                <div style="display: flex; align-items: center; padding: 12px 15px; width: 100%;">
                    <div aria-hidden="true" style="margin-right: 15px;">
                        <img src="{$blobReporter}" alt="{$nomeReporter}">
                        <img src="{$blobReported}" alt="{$nomeReported}">
                    </div>
                    <div class="text-info" style="text-align: left; flex-grow: 1;">
                        <div class="name-row">
                            <strong>{$nomeReporter}</strong>
                        </div>
                        <small>segnala: {$nomeReported}</small>
                        <small>tipo: {$typeReport}</small>
                        <small>messaggio: {$messageReport}</small>
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
            $icon = '<i class="fas fa-arrow-up text-secondary"></i>';
        } else {
            $icon = '<i class="fas fa-tag text-success"></i>';
        }
    
        return <<<HTML
        <article data-type="{$whoSent}" data-progressivo="{$messageProgressivo}">
            <div style="min-width: 220px;">
                
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="fw-bold text-uppercase text-secondary" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        Offerta
                    </small>
                    {$icon}
                </div>
    
                <div class="text-center py-1">
                    <span class="display-6 fw-bold text-dark">€ {$content}</span>
                </div>
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
        </header>
        HTML;
    }

    function currentChatFooter(){
        if(!isset($_SESSION['idChatSelected'])){
            return "";
        }
        return <<<HTML
            <footer>
                <div class="d-flex justify-content-center align-items-center w-100 gap-3 py-2">
                    
                    <form action="" method="POST" style="margin: 0;">
                        <input type="hidden" name="offer_action" value="refuse">
                        <button type="submit" class="btn btn-danger px-4 py-2 fw-bold shadow-sm" style="border-radius: 25px; min-width: 130px;">
                            <i class="bi bi-x-circle me-2"></i>Rifiuta
                        </button>
                    </form>
    
                    <form action="" method="POST" style="margin: 0;">
                        <input type="hidden" name="offer_action" value="accept">
                        <button type="submit" class="btn btn-success px-4 py-2 fw-bold shadow-sm" style="border-radius: 25px; min-width: 130px;">
                            <i class="bi bi-check-circle me-2"></i>Accetta
                        </button>
                    </form>
    
                </div>
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
<?php

    require_once "utils/chatUtils.php";

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
        if(isset($_POST['current_chat_refuse'])){
            $dbHandler->deleteSegnalazione(
                $_SESSION['user_id'],
                $_SESSION['idChatSelected'],
                $_SESSION['typeReportChatSelected'],
                $_SESSION['reporterIdChatSelected'],
                $_SESSION['messageReportChatSelected']
            );
            unset($_SESSION['idChatSelected']);
        } 
        else if(isset($_POST['current_chat_accept'])){
            $dbHandler->banUserFromSegnalazione(
                $_SESSION['user_id'],
                $_SESSION['idChatSelected'],
                $_SESSION['typeReportChatSelected'],
                $_SESSION['reporterIdChatSelected'],
                $_SESSION['messageReportChatSelected'],
                $_SESSION['reportedIdChatSelected'],
            );
            unset($_SESSION['idChatSelected']);
        }

    }
?>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_new_selected_chat_list_id'])) {
        ifPostSetSession('admin_new_selected_chat_id','idChatSelected');
        ifPostSetSession('admin_new_selected_chat_list_id','listIdChatSelected');
        ifPostSetSession('admin_new_selected_chat_reporter_id','reporterIdChatSelected');
        ifPostSetSession('admin_new_selected_chat_reported_id','reportedIdChatSelected');
        ifPostSetSession('admin_new_selected_chat_reporter_name','reporterNameChatSelected');
        ifPostSetSession('admin_new_selected_chat_reported_name','reportedNameChatSelected');
        ifPostSetSession('admin_new_selected_chat_reporter_blob','reporterBlobChatSelected');
        ifPostSetSession('admin_new_selected_chat_reported_blob','reportedBlobChatSelected');
        ifPostSetSession('admin_new_selected_chat_type_report','typeReportChatSelected');
        ifPostSetSession('admin_new_selected_chat_message_report','messageReportChatSelected');
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
                        $chatData['idReporter'] ?? '', 
                        $chatData['idReported'] ?? '', 
                        $chatData['nomeReporter'] ?? '', 
                        $chatData['nomeReported'] ?? '', 
                        $chatData['tipoSegnalazione'] ?? '', 
                        $chatData['testo'] ?? '', 
                        $chatData['idChat'] ?? '', 
                        $listIdChat, 
                        ($_SESSION['listIdChatSelected'] ?? null) == $listIdChat
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
function chatBlock($blobReporter, $blobReported, $idReporter, $idReported, $nomeReporter, $nomeReported, $typeReport, $messageReport, $chatId ,$chatListId ,$isSelected){
    $ariaLabel = $nomeReporter . "segnala " . $nomeReported;
    $currentAttr = $isSelected ? 'aria-current="true"' : '';
    return <<<HTML
    <li {$currentAttr}>
        <form action="" method="POST" style="margin: 0; padding: 0; display: block;">
            
            <input type="hidden" name="admin_new_selected_chat_id" value="{$chatId}">
            <input type="hidden" name="admin_new_selected_chat_list_id" value="{$chatListId}">
            <input type="hidden" name="admin_new_selected_chat_reporter_id" value="{$idReporter}">
            <input type="hidden" name="admin_new_selected_chat_reported_id" value="{$idReported}">
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
        $blobReporter = $_SESSION['reporterBlobChatSelected'] ?? null;
        $blobReported = $_SESSION['reportedBlobChatSelected'] ?? null;
        $nameReporter = $_SESSION['reporterNameChatSelected'] ?? null;
        $nameReported = $_SESSION['reportedNameChatSelected'] ?? null;
        return <<<HTML
        <header class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
            <div class="user-info d-flex align-items-center">
                <div aria-hidden="true" class="d-flex align-items-center gap-2 me-3">
                    <img src="{$blobReporter}" alt="ALT" class="rounded-circle border">
                    <img src="{$blobReported}" alt="ALT" class="rounded-circle border border-2 border-white">
                </div>
                <h2 class="h5 m-0">{$nameReporter} Segnala {$nameReported}</h2>
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
                        <input type="hidden" name="current_chat_refuse" value="refuse">
                        <button type="submit" class="btn btn-danger px-4 py-2 fw-bold shadow-sm">
                            <i class="bi bi-x-circle me-2"></i>Rifiuta
                        </button>
                    </form>
    
                    <form action="" method="POST" style="margin: 0;">
                        <input type="hidden" name="current_chat_accept" value="accept">
                        <button type="submit" class="btn btn-success px-4 py-2 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-2"></i>Accetta
                        </button>
                    </form>
    
                </div>
            </footer>
        HTML;
    }
?>
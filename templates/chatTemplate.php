<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!isset($_POST['new_selected_chat_list_id'])){
          return;
        }

        if(isset($_POST['new_selected_chat_id'])){$_SESSION['idChatSelected'] = $_POST['new_selected_chat_id'];}
        if(isset($_POST['new_selected_chat_list_id'])){$_SESSION['listIdChatSelected'] = $_POST['new_selected_chat_list_id'];}
        if(isset($_POST['new_selected_chat_product_name'])){$_SESSION['productNameChatSelected'] = $_POST['new_selected_chat_product_name'];}
        if(isset($_POST['new_selected_chat_product_blob'])){$_SESSION['productBlobChatSelected'] = $_POST['new_selected_chat_product_blob'];}
        if(isset($_POST['new_selected_chat_user_name'])){$_SESSION['userNameChatSelected'] = $_POST['new_selected_chat_user_name'];}
        if(isset($_POST['new_selected_chat_user_blob'])){$_SESSION['userBlobChatSelected'] = $_POST['new_selected_chat_user_blob'];}
    }
?>
<?php
    $chatBlocksHtml = "";
    $idChatSelected = null;
    try {
        $dbHandler = new ChatManager();
    } catch (Exception $e) {
        $dbHandler = null;
        $chatBlocksHtml = errorBlock();
    }

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
    <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Nessuna Chat disponibile</h2>
        <p style="margin-bottom: 1.5rem;">Al momento hai chat aperte, aspetta di esserre contattato se sei un venditore, o esplora i prodotti se vuoi contattare qualcuno</p>
    </div>
HTML;
}
function errorBlock() {
    return <<<HTML
    <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Nessuna Chat disponibile</h2>
        <p style="margin-bottom: 1.5rem;">Al il servizio è temporaneamente non disponibile. Ci scusiamo per il disagio.</p>
    </div>
HTML;
}
?>

<?php
    function currentChat(){
        $header = currentChatHeader();
        return <<<HTML
            <main role="main" aria-label="Conversazione corrente">
                {$header}
                    <section aria-live="polite" aria-label="Cronologia messaggi">
                        <article data-type="received">
                            <div>
                                <p>Ciao! Come va?</p>
                                <time datetime="10:30">10:30</time>
                            </div>
                        </article>
                        <article data-type="sent">
                            <div>
                                <p>Tutto bene! Ho appena finito il login.</p>
                                <div class="msg-meta">
                                    <time datetime="10:32">10:32</time>
                                    <i class="fas fa-check-double" aria-label="Letto"></i>
                                </div>
                            </div>
                        </article>
                         <article data-type="received">
                            <div>
                                <p>Ottimo lavoro!</p>
                                <time datetime="10:33">10:33</time>
                            </div>
                        </article>
                    </section>
                    <footer>
                        <form action="" method="POST">
                            <button type="button" aria-label="Emoji">
                                <i class="far fa-smile"></i>
                            </button>
                            <button type="button" aria-label="Allega">
                                <i class="fas fa-plus"></i>
                            </button>
                            <label for="message-input">Scrivi un messaggio</label>
                            <input type="text" id="message-input" name="message" placeholder="Scrivi un messaggio" autocomplete="off">

                            <button type="submit" aria-label="Invia">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </footer>
                </main>
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
            <header>
                <div class="user-info">
                    <div aria-hidden="true">
                        <img src={$blobProduct} alt="">
                        <img src={$blobUser} alt="">
                    </div>
                    <h2>{$nameUser}</h2>
                </div>
                <nav>
                    <button type="button" aria-label="Cerca">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" aria-label="Allega">
                        <i class="fas fa-paperclip"></i>
                    </button>
                </nav>
            </header>
        HTML;
    }
?>



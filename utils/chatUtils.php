<?php
function ifPostSetSession($postName, $sessionName)
{
    if (isset($_POST[$postName])) {
        $_SESSION[$sessionName] = $_POST[$postName];
    }
}
function genericErrorBlockList($bgColor, $color, $title, $message)
{
    return <<<HTML
        <li  class="alert alert-warning text-center p-5" style="background-color: {$bgColor}; color:{$color}; border: 1px solid #ffeeba; border-radius: 5px;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">{$title}</h2>
            <p style="margin-bottom: 1.5rem;">{$message}</p>
        </li>
        HTML;
}

function genericErrorBlock($bgColor, $color, $title, $message, $id = "errorBlock")
{
    return <<<HTML
        <div  class="alert alert-warning text-center p-5" style="background-color: {$bgColor}; color:{$color}; border: 1px solid #ffeeba; border-radius: 5px;" id={$id}>
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">{$title}</h2>
            <p style="margin-bottom: 1.5rem;">{$message}</p>
        </div>
        HTML;
}

function noChatBlock()
{
    return genericErrorBlockList("#fff3cd", "rgb(0, 0, 0)", "Nessuna Chat disponibile", message: "Al momento non hai chat aperte, aspetta di esserre contattato se sei un venditore, o esplora i prodotti se vuoi contattare qualcuno");
}
function errorBlock()
{
    return genericErrorBlockList("#fff3cd", "rgb(0, 0, 0)", "Nessuna Chat disponibile", "Al momento il servizio è temporaneamente non disponibile. Ci scusiamo per il disagio");
}

function noChatSelectBlock()
{
    return genericErrorBlock("rgb(211, 211, 211)", "rgb(0, 0, 0)", "Nessuna Chat Selezionata", "Seleziona una chat per visualizzare i messaggi");
}

function noMessagesBlock()
{
    return genericErrorBlock("rgb(199, 199, 199)", "rgb(0, 0, 0)", "Nessun messagio", "Al momento non ci sono messaggi in questa chat, non essere timido :)","noMessageBlock");
}

function errorChatBlock()
{
    return genericErrorBlock("#fff3cd", "rgb(0, 0, 0)", "Chat non disponibile", "Al momento il servizio è temporaneamente non disponibile. Ci scusiamo per il disagio");
}

function currentChat($chatFinished = true, $disableOfferButton = true)
{
    $header = currentChatHeader($disableOfferButton);
    $body = currentChatBody($chatFinished);
    $footer = currentChatFooter();

    if ($body == errorBlock()) {
        $footer = "";
    }
    return <<<HTML
            <div class="currentChat"  aria-label="Conversazione corrente" role="dialog">
                        {$header}
                        {$body} 
                        {$footer} 
         </div>
        HTML;
}

function currentChatBody($chatFinished)
{
    try {
        $dbHandler = new ChatManager();
    } catch (Exception $e) {
        $dbHandler = null;
    }

    if (!isset($_SESSION['idChatSelected'])) {
        return noChatSelectBlock();
    } else {
        $idChat = $_SESSION['idChatSelected'];
        $result = "";
        try {
            if (!$dbHandler) {
                return errorBlock();
            }
            $history = $dbHandler->getChatHistory($idChat);
            if (empty($history)) {
                $result = noMessagesBlock();
            } else {
                foreach ($history as $row) {
                    $isMine = ($row['idMandante'] != $_SESSION['user_id']);
                    $image_data = $row['image'] ?? null;
                    $messageProgressivo = $row['progressivo'] ?? null;
                    if ($row['type'] === 'message') {
                        $result .= singleChatMessage($isMine, $row['content'], $image_data, $messageProgressivo);
                    } else if ($row['type'] === 'offer') {
                        $result .= singleChatOffer($isMine, $row['content'], $messageProgressivo, $chatFinished);
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

function singleChatMessage($isMine, $content, $image_data = null, $messageProgressivo = 0)
{
    $whoSent = $isMine ? "sent" : "received";
    $imageHtml = '';
    if ($image_data !== null && $image_data !== '') {
        $base64Image = base64_encode($image_data);
        $mimeType = 'image/jpeg';
        $imageSrc = 'data:' . $mimeType . ';base64,' . $base64Image;
        $imageHtml = <<<IMG
                <img src="{$image_data}" alt="Immagine-messaggio:$messageProgressivo">
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
?>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_offerta_chat'])) {
    if (!$dbHandler) {
        return;
    }

    try {
        $dbHandler->addNewOffertaChat(
            $_SESSION['idChatSelected'],
            $_SESSION['user_id'],
            $_POST['offer_amount']
        );
    } catch (Exception $e) {
    }
}
function offertaChatModal()
{
    return <<<HTML
      <div class="modal fade" id="modalOfferta" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
             <div class="modal-content">
                <div class="modal-header">
                   <h1 class="modal-title">Fai un'offerta</h1>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="#" method="POST">
                    <input type="hidden" name="new_offerta_chat" value="true">
                    <div class="modal-body">
                        <label for="offerAmount" class="visually-hidden">Prezzo</label>
                        <input type="number" 
                               class="form-control" 
                               id="offerAmount" 
                               name="offer_amount" 
                               placeholder="Inserisci il prezzo (€)" 
                               required 
                               min="0.01" 
                               step="0.01">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" name="submit_offer" class="btn btn-success">Invia Offerta</button>
                    </div>
                </form>
                </div>
          </div>
       </div>
    HTML;
}
?>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['segnalazione_chat'])) {
    if(!$dbHandler){
        return; 
    }

    //try{
        $dbHandler->addNewSegnalazione(
            $_POST['segnalazione_chat'],
            $_POST['segnalazione_desc'],
            $_SESSION['user_id'],
            $_SESSION['idChatSelected']
        );
        $_SESSION['report_sent'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    //} catch (Exception $e) {}
}
function segnalaChatModal(){
    return <<<HTML
    <div class="modal fade" id="modalSegnala" tabindex="-1" aria-hidden="true" style="z-index: 10055;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">Segnala Utente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="POST">
                    <input type="hidden" name="new_segnalazione" value="true">
                    
                    <div class="modal-body pt-2">
                        
                        <p class="text-center fw-bold text-dark mb-4 mt-2" 
                           style="font-size: 1.1rem; text-decoration: underline; text-decoration-color: #0d6efd;">
                           <span class="text-danger">Mandando questa segnalazione accetti in piena consapevolezza che gli admin potranno guardare la tua chat</span>
                        </p>

                        <div class="mb-4">
                            <label for="reportReason" class="form-label small text-muted fw-bold">Motivo</label>
                            <textarea class="form-control" 
                                      id="reportReason" 
                                      name="segnalazione_desc" 
                                      rows="3" 
                                      placeholder="Inserisci una breve descrizione (max 50 parole)" 
                                      maxlength="300" 
                                      style="resize: none; border-radius: 8px;"
                                      required></textarea>
                        </div>

                        <div class="row align-items-end">
                            <div class="col-7">
                                <label for="reportCategory" class="form-label small text-muted fw-bold">categoria</label>
                                <select class="form-select" id="reportCategory" name="segnalazione_chat" required style="border-radius: 8px;">
                                    <option value="" selected disabled>Seleziona...</option>
                                    <option value="molestia">Molestia</option>
                                    <option value="discriminazione">Discriminazione</option>
                                    <option value="volgaritá">Volgarità</option>
                                    <option value="truffa">Truffa</option>
                                    <option value="spam">Spam</option>
                                    <option value="altro">Altro</option>
                                </select>
                            </div>
                            
                            <div class="col-5 text-end">
                                <button type="submit" class="btn btn-danger w-100 py-2 fw-bold" style="border-radius: 8px; font-size: 1.1rem;">
                                    Report
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-0 p-2"></div>
                </form>

            </div>
        </div>
    </div>
    HTML;
}
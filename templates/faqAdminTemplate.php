<?php
$contentToDisplay = "";
try {
    $dbHandler = new FaqManager();
} catch (Exception $e) {
    $dbHandler = null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_faq'])) {
        try {
            $dbHandler->deleteFaqByTitle($_POST['delete_faq']);
        } catch (Exception $e) {
        }
    } else if (isset($_POST['create_faq'])) {
        try {
            $dbHandler->addFaq($_POST['faq_title'], $_POST['faq_desc']);
        } catch (Exception $e) {
        }
    }

}

if ($dbHandler) {
    try {
        $faqsMap = $dbHandler->getFaqs();
        if (empty($faqsMap)) {
            $contentToDisplay = errorBlock();
        } else {
            foreach ($faqsMap as $titolo => $descrizione) {
                $contentToDisplay .= faqBlock($titolo, $descrizione);
            }
        }
    } catch (Exception $e) {
        $contentToDisplay = errorBlock();
    }
}

$contentToDisplay .= newFaqForm();
?>


<div id="faq-page">

    <header>
        <h1>Unisell FAQ</h1>
    </header>



    <?= $contentToDisplay ?>

</div>

<?php
function faqBlock($titolo, $descrizione)
{
    return <<<HTML
        <details open class="mb-3 border rounded p-2 position-relative">
            <summary class="d-flex justify-content-between align-items-center list-unstyled" style="cursor: pointer; padding-right: 50px;">
                
                <span class="d-flex align-items-center gap-2">
                    <strong class="h5 m-0 d-inline-block">{$titolo}</strong>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 8l0 8"></path>
                        <path d="M8 12l4 4 4-4"></path>
                    </svg>
                </span>

            </summary>
            
            <form action="#" method="POST" 
                  onsubmit="return confirm('Sei sicuro di voler eliminare questa FAQ?');" 
                  class="position-absolute top-0 end-0 m-2"
                  style="z-index: 10;">
                <input type="hidden" name="delete_faq" value="{$titolo}">
                <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" aria-label="Elimina">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </form>
            
            <div class="mt-2 text-muted">
                <p>{$descrizione}</p>
            </div>
        </details>
    HTML;
}
function newFaqForm()
{
    return <<<HTML
    <div class="mb-5 p-4 border rounded bg-light shadow-sm">
        <h2 class="h5 mb-3 text-primary"><i class="fas fa-plus-circle me-2"></i>Aggiungi Nuova FAQ</h2>
        
        <form action="#" method="POST">
            <input type="hidden" name="create_faq" value="true">

            <div class="mb-3">
                <label for="faqTitle" class="form-label fw-bold small text-secondary">Titolo / Domanda</label>
                <input type="text" 
                       class="form-control" 
                       id="faqTitle" 
                       name="faq_title" 
                       placeholder="Es: Come posso resettare la password?" 
                       required>
            </div>

            <div class="mb-3">
                <label for="faqDesc" class="form-label fw-bold small text-secondary">Descrizione / Risposta</label>
                <textarea class="form-control" 
                          id="faqDesc" 
                          name="faq_desc" 
                          rows="4" 
                          placeholder="Inserisci la spiegazione qui..." 
                          required 
                          style="resize: vertical;"></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="fas fa-save me-2"></i>Salva FAQ
                </button>
            </div>
        </form>
    </div>
    HTML;
}
function errorBlock()
{
    return <<<HTML
    <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Nessuna FAQ disponibile</h2>
        <p style="margin-bottom: 1.5rem;">Al momento non ci sono domande da visualizzare o il servizio è temporaneamente non disponibile.</p>
        <button onclick="location.reload();" class="btn btn-outline-warning" style="color: #856404; border-color: #856404; background: transparent; padding: 5px 15px;">Riprova</button>
    </div>
HTML;
}
?>
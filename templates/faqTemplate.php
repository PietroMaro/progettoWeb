<?php
    $contentToDisplay = "";
    try {
        $dbHandler = new FaqManager();
    } catch (Exception $e) {
        $dbHandler = null;
        $contentToDisplay = errorBlock();
    }

    if($dbHandler){
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
?>


<div id="faq-page">
    
    <header>
        <h1>Unisell FAQ</h1>
    </header>

    <main>
        
        <?=$contentToDisplay?>
        
    </main>
</div>

<?php
function faqBlock($titolo,$descrizione){
    return <<<HTML
        <details open>
            <summary>
                <h2>{$titolo}</h2>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8l0 8"></path>
                    <path d="M8 12l4 4 4-4"></path>
                </svg>
            </summary>
            <div>
                <p>{$descrizione}</p>
            </div>
        </details>
    HTML; 
}
function errorBlock() {
    return <<<HTML
    <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Nessuna FAQ disponibile</h2>
        <p style="margin-bottom: 1.5rem;">Al momento non ci sono domande da visualizzare o il servizio è temporaneamente non disponibile.</p>
        <button onclick="location.reload();" class="btn btn-outline-warning" style="color: #856404; border-color: #856404; background: transparent; padding: 5px 15px;">Riprova</button>
    </div>
HTML;
}
?>
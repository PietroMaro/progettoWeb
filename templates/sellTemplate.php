<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {

        $handler = new productManager();
        $handler->saveProduct($_POST, $_FILES);


        exit;

    } catch (Exception $e) {
        echo "<h1>Errore</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
}

?>

<form action="" method="POST" enctype="multipart/form-data">

    <h1>Unisell</h1>

    <ul>
        <li>
            <label for="auctionSwitch">
                Vuoi che il prodotto sia venduto con un'asta? 
            </label>
            <input type="checkbox" role="switch" id="auctionSwitch" name="isAuction">
            <small>In questo caso il prezzo inserito sarà considerato la base d'asta</small>
        </li>

        <li>
            <label for="productName">Nome del prodotto</label>
            <input type="text" id="productName" name="productName" placeholder="Inserisci il nome del prodotto" required>
        </li>

        <li>
            <label for="productDescription">Descrizione del prodotto</label>
            <textarea id="productDescription" name="productDescription" rows="3"
                placeholder="Inserisci una breve desrizione del prodotto (massimo 50 parole)"></textarea>
        </li>

        <li>
            <label for="productPrice">Prezzo</label>
            <input type="number" id="productPrice" name="productPrice" placeholder="Inserisci il prezzo" min="0" step="0.01"
                required>
        </li>

        <li>
            <section>
                <label>Aggiungi immagini</label>
                <div id="image-preview-wrapper">
                    <span></span><span></span><span></span><span></span>
                </div>
                <label for="fileUpload" id="fileUploadLabel">
                   Inserisci le immagini
                </label>
                <input type="file" id="fileUpload" name="images[]" multiple>
            </section>
        </li>

        <li>
            <label for="auctionEndDate">Inserisci la data e ora di fine dell'asta</label>
            <input type="datetime-local" id="auctionEndDate" name="auctionEndDate">
        </li>

        <li>
            <button type="button">Modifica</button>
            <button type="submit">Vendi</button>
        </li>
    </ul>

</form>
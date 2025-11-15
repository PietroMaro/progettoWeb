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
            <label for="productName">Nome prodotto</label>
            <input type="text" id="productName" name="productName" placeholder="Inserisci il nome del prodotto"
                required>
        </li>
        <li>
            <label for="productDescription">Descrizione prodotto</label>
            <textarea id="productDescription" name="productDescription" rows="3"
                placeholder="Inserisci una breve desrizione del prodotto (massimo 50 parole)"></textarea>
        </li>
        <li>
            <label for="productPrice">Inserisci prezzo</label>
            <input type="number" id="productPrice" name="productPrice" placeholder="Inserisci prezzo" min="0"
                step="0.01" required>
        </li>
        <li>
            <section>
                <label>Aggiungi immagini</label>
                <div data-role="image-preview-wrapper">
                </div>
                <label for="fileUpload">
                    Aggiungi immagini
                </label>
                <input type="file" id="fileUpload" name="images[]" multiple accept="image/*">
            </section>
        </li>
        <li data-role="auctionDateContainer" style="display: none;">
            <label for="auctionEndDate">Inserisci la data e ora di fine dell'asta</label>
            <input type="datetime-local" id="auctionEndDate" name="auctionEndDate">
        </li>
        <li>
            <button type="button">Modifica</button>
            <button type="submit">Vendi</button>
        </li>
    </ul>
</form>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- Logica Asta (CORRETTA) ---
        const auctionSwitch = document.getElementById('auctionSwitch');
        // CORRETTO: Cerca il 'data-role' sull'elemento <li>
        const auctionDateContainer = document.querySelector('li[data-role="auctionDateContainer"]');
        const auctionDateInput = document.getElementById('auctionEndDate');

        auctionSwitch.addEventListener('change', function () {
            if (this.checked) {
                auctionDateContainer.style.display = 'block';
                auctionDateInput.required = true;
            } else {
                auctionDateContainer.style.display = 'none';
                auctionDateInput.required = false;
                auctionDateInput.value = '';
            }
        });


        // --- Logica Anteprima Immagini (CORRETTA) ---
        const fileInput = document.getElementById('fileUpload');
        // CORRETTO: Cerca il 'data-role' sull'elemento <div>
        const previewWrapper = document.querySelector('div[data-role="image-preview-wrapper"]');

        fileInput.addEventListener('change', function (event) {
            previewWrapper.innerHTML = '';

            if (event.target.files && event.target.files.length > 0) {
                const files = Array.from(event.target.files).slice(0, 4);

                files.forEach(file => {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        previewWrapper.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        });

    });
</script>
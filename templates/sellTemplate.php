<?php
$errorMessage = null; // Inizializza la variabile per l'errore

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $handler = new productManager();
        $handler->saveProduct($_POST, $_FILES);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {

        echo "<div class='alert alert-danger'>Errore: " . $e->getMessage() . "</div>";

    }
}
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9">

            <form action="" method="POST" enctype="multipart/form-data"
                class="card shadow-sm border-success p-4 p-md-5">

                <h1 class="h2 text-center text-success fw-bold mb-4">Unisell</h1>

                <div class="form-check form-switch p-0 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <label class="form-check-label" for="auctionSwitch">
                            Vuoi che il prodotto sia venduto con un'asta?
                        </label>
                        <small class="form-text text-muted d-block">
                            In questo caso il prezzo inserito sarà considerato la base d'asta
                        </small>
                    </div>
                    <input class="form-check-input" type="checkbox" role="switch" id="auctionSwitch" name="isAuction">
                </div>

                <div class="mb-3">
                    <label for="productName" class="form-label">Nome prodotto</label>
                    <input type="text" class="form-control" id="productName" name="productName"
                        placeholder="Inserisci il nome del prodotto" required maxlength="50">
                </div>

                <div class="mb-3">
                    <label for="productDescription" class="form-label">Descrizione prodotto</label>
                    <textarea class="form-control" id="productDescription" name="productDescription" rows="3"
                        placeholder="Inserisci una breve desrizione del prodotto" maxlength="200" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="productPrice" class="form-label">Inserisci prezzo</label>
                    <input type="number" class="form-control" id="productPrice" name="productPrice"
                        placeholder="Inserisci prezzo" min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Aggiungi immagini</label>

                    <div data-role="image-preview-wrapper" class="row row-cols-4 g-2 mb-3">
                    </div>

                    <label for="fileUpload" class="btn btn-outline-success w-100">
                        Aggiungi immagini
                    </label>
                    <input type="file" id="fileUpload" name="images[]" multiple accept="image/*" required
                        class="d-none">
                </div>

                <div class="mb-3" data-role="auctionDateContainer" style="display: none;">
                    <label for="auctionEndDate" class="form-label">Inserisci la data e ora di fine dell'asta</label>
                    <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate">
                </div>

                <div class="row g-2 mt-4">
                    <div class="col">
                        <button type="button" class="btn btn-outline-success w-100 btn-lg">Modifica</button>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-success w-100 btn-lg">Vendi</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const auctionSwitch = document.getElementById('auctionSwitch');
        const auctionDateContainer = document.querySelector('div[data-role="auctionDateContainer"]');
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

        if (auctionSwitch.checked) {
            auctionSwitch.dispatchEvent(new Event('change'));
        }

        const fileInput = document.getElementById('fileUpload');
        const previewWrapper = document.querySelector('div[data-role="image-preview-wrapper"]');

        fileInput.addEventListener('change', function (event) {
            previewWrapper.innerHTML = '';

            if (event.target.files && event.target.files.length > 0) {
                const files = Array.from(event.target.files).slice(0, 4);

                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const col = document.createElement('div');
                        col.className = 'col';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-fluid rounded';

                        col.appendChild(img);
                        previewWrapper.appendChild(col);
                    }
                    reader.readAsDataURL(file);
                });
            }
        });
    });
</script>
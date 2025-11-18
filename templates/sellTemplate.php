<?php


$handler = new productManager();

$productToEdit = null;
$isEditing = false;

if (isset($_GET['edit_id'])) {
    $productId = (int) $_GET['edit_id'];
    $productToEdit = $handler->getProductById($productId);

    if ($productToEdit) {
        $isEditing = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {

        if ($isEditing) {
            $handler->updateProduct($_POST, $_FILES);


        } else {
            $handler->saveProduct($_POST, $_FILES);

        }

        header("Location: showcasePage.php");
        exit;
    } catch (Exception $e) {
        $e->getMessage();
    }
}
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9">



            <form action="" method="POST" enctype="multipart/form-data"
                class="card shadow-sm border-success p-4 p-md-5">

                <h1 class="h2 text-center text-success fw-bold mb-4">
                    <?= $isEditing ? 'Modifica Prodotto' : 'Vendi Prodotto' ?>
                </h1>

                <input type="hidden" name="idProdotto" value="<?= $productToEdit['idProdotto'] ?? '' ?>">

                <div class="form-check form-switch p-0 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <label class="form-check-label" for="auctionSwitch">
                            Vuoi che il prodotto sia venduto con un'asta?
                        </label>
                        <small class="form-text text-muted d-block">
                            In questo caso il prezzo inserito sarà considerato la base d'asta
                        </small>
                    </div>
                    <input class="form-check-input" type="checkbox" role="switch" id="auctionSwitch" name="isAuction"
                        <?php if ($isEditing && !empty($productToEdit['fineAsta']))
                            echo 'checked'; ?>>
                </div>

                <div class="mb-3">
                    <label for="productName" class="form-label">Nome prodotto</label>
                    <input type="text" class="form-control" id="productName" name="productName"
                        placeholder="Inserisci il nome del prodotto" required maxlength="50"
                        value="<?= htmlspecialchars($productToEdit['nome'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="productDescription" class="form-label">Descrizione prodotto</label>
                    <textarea class="form-control" id="productDescription" name="productDescription" rows="3"
                        placeholder="Inserisci una breve desrizione del prodotto" maxlength="200"
                        required><?= htmlspecialchars($productToEdit['descrizione'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="productPrice" class="form-label">Inserisci prezzo</label>
                    <input type="number" class="form-control" id="productPrice" name="productPrice"
                        placeholder="Inserisci prezzo" min="0" step="0.01" required
                        value="<?= htmlspecialchars($productToEdit['prezzo'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Aggiungi immagini</label>

                    <?php if ($isEditing): ?>
                        <div class="alert alert-info py-2 px-3 mb-2 small">
                            <i class="bi bi-info-circle"></i> Caricando nuove immagini sostituirai quelle esistenti.
                        </div>
                    <?php endif; ?>

                    <div data-role="image-preview-wrapper" class="row row-cols-4 g-2 mb-3">
                    </div>

                    <label for="fileUpload" class="btn btn-outline-success w-100">
                        <?= $isEditing ? 'Sostituisci immagini' : 'Aggiungi immagini' ?>
                    </label>
                    <input type="file" id="fileUpload" name="images[]" multiple accept="image/*" class="d-none"
                        <?= !$isEditing ? 'required' : '' ?>>
                </div>

                <div class="mb-3" data-role="auctionDateContainer" style="display: none;">
                    <label for="auctionEndDate" class="form-label">Inserisci la data e ora di fine dell'asta</label>
                    <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate"
                        value="<?= !empty($productToEdit['fineAsta']) ? date('Y-m-d\TH:i', strtotime($productToEdit['fineAsta'])) : '' ?>">
                </div>

                <div class="row g-2 mt-4">
                    <div class="col">
                        <a href="showcasePage.php"
                            class="btn btn-outline-secondary w-100 btn-lg text-decoration-none">Annulla</a>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <?= $isEditing ? 'Salva Modifiche' : 'Vendi' ?>
                        </button>
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
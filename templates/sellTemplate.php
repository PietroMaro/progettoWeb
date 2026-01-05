<?php


$handler = new productManager();

$productToEdit = null;
$existingImages = [];
$isEditing = false;


if (isset($_GET['edit_id'])) {

    try {
        $productId = (int) $_GET['edit_id'];
        $productToEdit = $handler->getProductById($productId);


        $isEditing = true;
        $existingImages = $handler->getImagesByProductId($productId);

    } catch (Exception $e) {
        $e->getMessage();
        $erroreMsg = "Impossibile salvare il prodotto. Controlla i dati o riprova più tardi.";
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
        $erroreMsg = "Impossibile salvare il prodotto. Controlla i dati o riprova più tardi.";
    }
}
?>

<div class="container my-5">

    <?php if (!empty($erroreMsg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Attenzione!</strong> <?= htmlspecialchars($erroreMsg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9">



            <form action="#" method="POST" enctype="multipart/form-data"
                class="card shadow-sm border-success p-4 p-md-5 needs-validation" novalidate>

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
                            In questo caso il prezzo inserito sarà considerato la base d'asta.
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
                        value="<?= htmlspecialchars($productToEdit['nome'] ?? "") ?> ">
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
                        placeholder="Inserisci prezzo, massimo 9999€" min="0" max="9999" step="0.01" required
                        value="<?= htmlspecialchars($productToEdit['prezzo'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <?php if ($isEditing && !empty($existingImages)): ?>
                        <?php

                        $numero = 0;
                        ?>

                        <fieldset>
                            <legend>
                                Selezione delle immagini da eliminare
                            </legend>
                            <div class="card p-3 mb-3 bg-light">
                                <p class="small text-muted mb-2">Seleziona le immagini che vuoi <strong>eliminare</strong>:
                                </p>
                                <div class="row row-cols-2 row-cols-md-4 g-3">
                                    <?php foreach ($existingImages as $img): ?>
                                        <div class="col position-relative">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="position-absolute top-0 end-0 p-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input border-danger" type="checkbox"
                                                            name="delete_images[]" value="<?= $img['idImmagine'] ?>"
                                                            id="img-<?= $img['idImmagine'] ?>">
                                                    </div>
                                                </div>

                                                <label for="img-<?= $img['idImmagine'] ?>" class="cursor-pointer h-100">
                                                    <img src="data:image/jpeg;base64,<?= base64_encode($img['immagine']) ?>"
                                                        class="card-img-top h-100 object-fit-cover rounded"
                                                        alt=<?= "Immagine_prodotto:$numero " ?>>
                                                </label>
                                                <?php

                                                $numero++;
                                                ?>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </fieldset>
                    <?php endif; ?>

                    <div data-role="image-preview-wrapper" class="row row-cols-4 g-2 mb-3"></div>

                    <label for="fileUpload" class="btn btn-outline-success w-100">
                        <i class="bi bi-cloud-upload"></i>
                        <?= $isEditing ? 'Carica altre foto' : 'Carica foto' ?>
                    </label>

                    <input type="file" id="fileUpload" name="images[]" multiple accept="image/*" class="d-none"
                        <?= (!$isEditing) ? 'required' : '' ?>>
                </div>
                <div class="mb-3" data-role="auctionDateContainer">
                    <label for="auctionEndDate" class="form-label">Inserisci la data e ora di fine dell'asta</label>
                    <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate"
                        min="<?= date('Y-m-d\TH:i', strtotime('+15 minutes')) ?>" value="<?php if (!empty($productToEdit['fineAsta'])) {
                               echo date('Y-m-d\TH:i', strtotime($productToEdit['fineAsta']));
                           } ?>">
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
</div>

<script src="scripts/sellPageScript.js"></script>
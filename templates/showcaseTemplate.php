<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idProdotto'])) {
    try {

        $handler->deleteProduct($_POST);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Errore: " . $e->getMessage() . "</div>";
    }
}
?>





<main class="container my-4">

    <?php if (empty($products)): ?>

        <div class="alert alert-warning text-center p-5">
            <h3>Nessun prodotto disponibile</h3>
            <p>Al momento non ci sono prodotti da visualizzare o il servizio è temporaneamente non disponibile.</p>
            <button onclick="location.reload()" class="btn btn-outline-secondary mt-3">Riprova</button>
        </div>

    <?php else: ?>

        <div class="row gx-md-3">
            <?php foreach ($products as $product): ?>

                <div class="col-12 col-md-4 mb-4">
                    <article class="card h-100">

                        <img src="data:image/jpeg;base64,<?= base64_encode($product['immagineData']) ?>"
                            alt="<?= htmlspecialchars($product['nome']) ?>">

                        <header class="card-header d-flex justify-content-between align-items-center">

                            <span class="fw-bold">
                                <?php if (!empty($product['fineAsta'])): ?>
                                    Asta
                                <?php else: ?>
                                    Vendita diretta
                                <?php endif; ?>
                            </span>

                            <div class="d-flex gap-1">

                                <?php if ($product['stato'] == 'approvato'): ?>
                                    <span class="badge bg-success rounded-pill" title="Approvato">Si</span>
                                <?php elseif ($product['stato'] == 'attesa'): ?>
                                    <span class="badge bg-secondary rounded-pill" title="In attesa">At</span>
                                <?php elseif ($product['stato'] == 'rifiutato'): ?>
                                    <span class="badge bg-danger rounded-pill" title="Rifiutato">No</span>
                                <?php endif; ?>

                                <?php if (!empty($product['fineAsta'])): ?>
                                    <span class="badge bg-info rounded-pill" title="Asta">As</span>
                                <?php endif; ?>
                            </div>
                        </header>

                        <section class="card-body d-flex flex-column">

                            <p class="d-flex flex-column">
                                <span class="fw-bold fs-5"><?= htmlspecialchars($product['nome']) ?></span>
                                <span class="text-muted"><?= htmlspecialchars(number_format($product['prezzo'], 2)) ?> €</span>
                                <?php if (!empty($product['fineAsta'])): ?>
                                    <span class="text-muted small"><?= $product['fineAsta'] ?></span>
                                <?php endif; ?>
                            </p>

                            <div class="mt-auto d-flex gap-2 justify-content-end">

                                <a href="sellProductPage.php?edit_id=<?= $product['idProdotto'] ?>"
                                    class="btn btn-sm btn-warning">
                                    Modifica
                                </a>

                                <form action="#" method="POST" class="m-0"> <input type="hidden" name="idProdotto"
                                        value="<?= $product['idProdotto'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Cancella</button>
                                </form>
                            </div>
                        </section>

                    </article>
                </div>
            <?php endforeach; ?>

        </div>


    <?php endif; ?>

</main>
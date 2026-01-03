
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = $_POST['idProdotto'];
    $handler->deleteProduct( $productId);
    header("Location: " . $_SERVER['PHP_SELF']);

}

?>

<div class="container my-4">

    <?php if (empty($products)): ?>
        <div class="alert alert-warning text-center p-5">
            <h3>Nessun prodotto disponibile</h3>
            <p>Al momento non ci sono prodotti da visualizzare.</p>
            <button onclick="location.reload()" class="btn btn-outline-secondary mt-3">Riprova</button>
        </div>
    <?php else: ?>

        <div class="row gx-md-3">
            <?php foreach ($products as $product): ?>

                <?php
                $isVenduto = $handler->isProductSold($product['idProdotto']);

                $isActiveState = in_array($product['stato'], ['asta', 'esposto']);

                $msgModifica = "Modificando questo prodotto le chat in corso verranno eliminate cosi come le offerte e il processo di approvazione ricomincerà.";
                $msgElimina = "Il prodotto verrà eliminato cosi come le chat legate ad esso.";
                ?>

                <div class="col-12 col-md-4 mb-4">
                    <article class="card h-100">

                        <img src="data:image/jpeg;base64,<?= base64_encode($product['immagineData']) ?>" class="card-img-top"
                            style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($product['nome']) ?>">

                        <header class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">
                                <?= !empty($product['fineAsta']) ? 'Asta' : 'Vendita diretta' ?>
                            </span>

                            <div class="d-flex gap-1">
                                <?php if ($isVenduto): ?>
                                    <span class="badge bg-primary rounded-pill">Venduto</span>
                                <?php elseif ($product['stato'] == 'attesa'): ?>
                                    <span class="badge bg-secondary rounded-pill">In attesa</span>
                                <?php elseif ($product['stato'] == 'rifiutato'): ?>
                                    <span class="badge bg-danger rounded-pill">No</span>
                                <?php elseif ($product['stato'] == 'astaDeserta'): ?>
                                    <span class="badge bg-danger rounded-pill">Deserta</span>
                                <?php elseif ($product['stato'] == 'asta' || $product['stato'] == 'esposto'): ?>
                                    <span class="badge bg-success rounded-pill">In vendita</span>
                                <?php endif; ?>
                            </div>
                        </header>

                        <section class="card-body d-flex flex-column">
                            <p class="d-flex flex-column">
                                <span class="fw-bold fs-5"><?= htmlspecialchars($product['nome']) ?></span>
                                <span class="text-muted"><?= htmlspecialchars(number_format($product['prezzo'], 2)) ?> €</span>
                                <?php if (!empty($product['fineAsta'])): ?>
                                    <span class="text-muted small">Scadenza: <?= $product['fineAsta'] ?></span>
                                <?php endif; ?>
                            </p>

                            <div class="mt-auto d-flex gap-2 justify-content-end align-items-center" style="min-height: 38px;">

                                <?php if (!$isVenduto): ?>

                                    <a href="sellProductPage.php?edit_id=<?= $product['idProdotto'] ?>"
                                        class="btn btn-sm btn-warning" <?php if ($isActiveState): ?>
                                            onclick="return confirm('<?= htmlspecialchars($msgModifica, ENT_QUOTES) ?>');" 
                                            <?php endif; ?>> 
                                            Modifica


                                    </a>

                                    <form action="#" method="POST" class="m-0">
                                        <input type="hidden" name="idProdotto" value="<?= $product['idProdotto'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" <?php if ($isActiveState): ?>
                                                onclick="return confirm('<?= htmlspecialchars($msgElimina, ENT_QUOTES) ?>');" 
                                                <?php else: ?> 
                                                    onclick="return confirm('Sei sicuro di voler eliminare questo prodotto?');"
                                            <?php endif; ?>>
                                            Cancella
                                        </button>
                                    </form>

                                <?php else: ?>
                                    <span class="text-muted small fst-italic">
                                        <i class="bi bi-lock-fill"></i> Chiuso
                                    </span>
                                <?php endif; ?>

                            </div>
                        </section>

                    </article>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>
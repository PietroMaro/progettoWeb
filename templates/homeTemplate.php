<div class="container my-4">
    <?php if (empty($products)): ?>

        <div class="alert alert-warning text-center p-5 ">
            <h1>Nessun prodotto disponibile</h1>
            <p>Al momento non ci sono prodotti da visualizzare o il servizio è temporaneamente non disponibile.</p>
            <button onclick="location.reload()" class="btn btn-outline-secondary mt-3 text-dark">Riprova</button>
        </div>

    <?php else: ?>



        <div class="row gx-md-3">
            <?php foreach ($products as $product): ?>

                <div class="col-12 col-md-4 mb-4">
                    <a href="singleProductPage.php?id=<?= $product['idProdotto'] ?>" class="text-decoration-none text-dark">

                        <article class="card h-100 card-hover ">

                            <img src="data:image/jpeg;base64,<?= base64_encode($product['immagineData']) ?>"
                                class="card-img-top" alt="<?= htmlspecialchars($product['nome']) ?>">

                            <div class="card-body d-flex flex-column p-3">

                                <header class="mb-3">

                                    <div class=" text-success ">
                                        <?php if (!empty($product['fineAsta'])): ?>
                                            <h2 class="fs-2">Asta</h2>
                                        <?php else: ?>
                                            <h2 class="fs-2">Vendita diretta</h2>
                                        <?php endif; ?>


                                    </div>


                                    <h3 class="card-title fs-4  mb-3  text-capitalize">
                                        <?= htmlspecialchars($product['nome']) ?>
                                    </h3>

                                    <?php if (!empty($product['fineAsta'])): ?>
                                        <h4 class=" fs-5  mb-0">Finisce tra:</h4>
                                        <div class="auction-timer fs-5  text-success " data-deadline="<?= $product['fineAsta'] ?>">
                                        </div>
                                    <?php endif; ?>
                                </header>
                                <div class=" pt-3 border-top">
                                    <span class="fs-4 fw-bold text-dark d-block">
                                        <?= number_format($product['prezzo'], 2) ?> €
                                    </span>
                                    <?php if (!empty($product['fineAsta'])): ?>
                                        <small class="text-muted">Offerta attuale</small>
                                    <?php endif; ?>
                                </div>



                            </div>

                        </article>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php endif; ?>




<script src="scripts/homePageScript.js"></script>
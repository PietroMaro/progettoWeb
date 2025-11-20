<main class="container-fluid d-flex justify-content-center my-4">

    <?php if (isset($fatalError)): ?>

        <div class="alert alert-warning text-center p-5">
            <h3>Nessun prodotto disponibile</h3>
            <p>Al momento non ci sono prodotti da visualizzare o il servizio è temporaneamente non disponibile.</p>
            <button onclick="location.reload()" class="btn btn-outline-secondary mt-3">Riprova</button>
        </div>

    <?php else: ?>
        <div class="col-12 col-md-10 col-lg-8 col-xl-7">
            <article class="card border-0 shadow-lg rounded-4 overflow-hidden">

                <header class="bg-light position-relative">

                    <?php if (count($productImages) > 0): ?>
                        <div id="carouselProduct" class="carousel slide" data-bs-ride="carousel">

                            <div class="carousel-inner">
                                <?php foreach ($productImages as $index => $imgRow): ?>
                                    <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($imgRow['immagine']); ?>"
                                            class="d-block w-100 h-100" alt="Foto Prodotto">
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($productImages) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselProduct"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon p-3 bg-dark rounded-circle bg-opacity-25"
                                        aria-hidden="true"></span>
                                    <span class="visually-hidden">Precedente</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselProduct"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon p-3 bg-dark rounded-circle bg-opacity-25"
                                        aria-hidden="true"></span>
                                    <span class="visually-hidden">Successivo</span>
                                </button>
                            <?php endif; ?>

                        </div>


                    <?php endif; ?>
                </header>

                <section class="card-body p-4 p-lg-5">
                    <div class="row align-items-start">

                        <div class="col-md-3 d-flex flex-column align-items-center text-center border-end-md mb-4 mb-md-0">

                            <div class="avatar-container mb-3">

                                <img src="data:image/jpeg;base64,<?= base64_encode($profileImg); ?>"
                                    class="rounded-circle border border-3 border-success p-1" alt="Venditore" />
                            </div>

                            <h2 class="text-success fw-bold display-6 mb-0">
                                <?php echo number_format((float) $product["prezzo"], 2, ',', ''); ?> €
                            </h2>
                            <span class="text-muted small">Prezzo richiesto</span>
                        </div>

                        <div class="col-md-9 ps-md-5 d-flex flex-column justify-content-between">
                            <div>
                                <h1 class="fw-bold text-dark mb-3 display-5">
                                    <?= $product["nome"]; ?>
                                </h1>

                                <p class="text-secondary fs-5">
                                    <?= nl2br($product["descrizione"]); ?>

                                </p>
                            </div>

                            <div class="mt-4 pt-4 border-top">
                                <button type="button"
                                    class="btn btn-success btn-lg w-100 rounded-pill py-3 fw-bold fs-4 shadow-sm text-uppercase tracking-wide hover-scale">
                                    Contatta il venditore
                                </button>
                            </div>
                        </div>

                    </div>
                </section>

            </article>
        </div>

    <?php endif; ?>
</main>
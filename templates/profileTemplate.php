<?php

if (!isset($userInfo)): ?>
    <div class="alert alert-warning text-center p-5">
        <p>Al momento il servizio è temporaneamente non disponibile.</p>
        <button onclick="location.reload()" class="btn btn-outline-secondary mt-3">Riprova</button>
    </div>
<?php else: ?>

    <div class="container mt-5">

        <header class="row mb-4">
            <div class="col-md-4 text-center">
                <h1 class="mb-3">
                    <?php echo htmlspecialchars($userInfo['nome']); ?>
                </h1>


                <img src="<?= $userInfo['imgSrc'];
                ?>" alt="Profile Pic" class="rounded-circle p-1">
            </div>

            <div class="col-md-8 d-flex align-items-center">
                <div class="card w-100 h-75 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-center text-muted text-center p-4">
                        <em>
                            <?php
                            if (!empty($userInfo['descrizione'])) {
                                echo nl2br(htmlspecialchars($userInfo['descrizione']));
                            } else {
                                echo "Nessuna descrizione disponibile.";
                            }
                            ?>
                        </em>
                    </div>
                </div>
            </div>
        </header>

        <hr>
        <div class="row">

            <section class="col-md-4 text-center pt-4">
                <div class="mb-3">
                    <strong>Name</strong><br>
                    <span><?php echo htmlspecialchars($userInfo['nome']); ?></span>
                </div>
                <div class="mb-3">
                    <strong>Surname</strong><br>
                    <span><?php echo htmlspecialchars($userInfo['cognome']); ?></span>
                </div>
                <div class="mb-3">
                    <strong>Email</strong><br>
                    <a href="mailto:<?php echo htmlspecialchars($userInfo['email']); ?>">
                        <?php echo htmlspecialchars($userInfo['email']); ?>
                    </a>
                </div>
            </section>

            <aside class="col-md-8">
                <h2 class="mb-4 text-end">Has sold</h2>

                <div class="row g-3 justify-content-end">
                    <?php
                    if (!empty($userInfo['soldProducts'])):
                        foreach ($userInfo['soldProducts'] as $product):
                            $imgProd = $product['imgSrc'];
                            ?>
                            <div class="col-6 col-sm-4 col-lg-3 text-center">
                                <div class="card shadow-sm mb-1">

                                    <img src="<?php echo $imgProd; ?>" class="card-img-top p-2"
                                        alt="<?php echo htmlspecialchars($product['nome']); ?>">
                                </div>
                                <small><?php echo htmlspecialchars($product['nome']); ?></small>
                            </div>

                            <?php
                        endforeach;
                    else:
                        ?>
                        <div class="col-12 text-end text-muted">
                            <p>Nessun prodotto venduto finora.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>

        </div>
    </div>

<?php endif; ?>
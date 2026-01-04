<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {




    if (!isset($_SESSION["user_id"])) {

        header("Location: index.php");
        exit();

    } elseif (isset($_POST['place_bid'])) {
        $bidAmount = $_POST['bid_amount'];
        $handler->addOfferForAuction($productId, $bidAmount);
        header("Location: index.php");

    } else {
        $chatManager = new ChatManager();
        $idChat = $chatManager->createChat($productId, $sellerId);

        if ($idChat) {
            $_SESSION['idChatSelected'] = $idChat;
            $_SESSION['userNameChatSelected'] = $chatManager->getNomeUtenteFromId($sellerId);
            $_SESSION['userBlobChatSelected'] = $chatManager->getImmageOfUserFromId($sellerId);
            $_SESSION['productNameChatSelected'] = $chatManager->getNomeProdottoFromId($productId);
            $_SESSION['productBlobChatSelected'] = $chatManager->getImmageOfProdottoFromId($productId);


            $_SESSION['listIdChatSelected'] = null;

            header("Location: chat.php");
            exit();
        }
    }
}



?>

<div class="container-fluid d-flex justify-content-center my-4">

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
                                    <span class="carousel-control-prev-icon p-3 bg-dark rounded-circle bg-opacity-75"
                                        aria-hidden="true"></span>
                                    <span class="visually-hidden">Precedente</span>
                                </button>

                                <button class="carousel-control-next" type="button" data-bs-target="#carouselProduct"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon p-3 bg-dark rounded-circle bg-opacity-75"
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
                                <a href="profilePage.php?userId=<?= $sellerId; ?>">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($profileImg); ?>"
                                        class="rounded-circle border border-3 border-success p-1" alt="Venditore" />
                                </a>
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

                                <?php if ($isAdmin): ?>
                                    <form action="utils/changeStatus.php" method="POST" id="adminActionForm">
                                        <input type="hidden" name="id_oggetto" value="<?= $productId; ?>">
                                        <div class="d-flex gap-2 w-100">
                                            <button type="button" class="btn btn-danger btn-lg flex-grow-1 rounded-pill"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                Rifiuta
                                            </button>
                                            <button type="submit" name="decision" value="approve"
                                                class="btn btn-success btn-lg flex-grow-1 rounded-pill">
                                                Approva
                                            </button>
                                        </div>


                                        <div class="modal fade" id="rejectModal" tabindex="-1"
                                            aria-labelledby="rejectModalLabel" aria-hidden="true" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <strong class="modal-title" id="rejectModalLabel">Motivazione
                                                            Rifiuto</strong>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="ragioneRifiuto" class="form-label">Perché stai
                                                                rifiutando questo prodotto?</label>
                                                            <textarea class="form-control" id="ragioneRifiuto"
                                                                name="ragione_rifiuto" rows="3" maxlength="200"
                                                                placeholder="Massimo 200 caratteri..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annulla</button>
                                                        <button type="submit" name="decision" value="reject"
                                                            class="btn btn-danger">Conferma Rifiuto</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                <?php else: ?>

                                    <div class="d-flex gap-2 w-100">

                                        <?php
                                        $isUserLoggedIn = isset($_SESSION['user_id']);
                                        $disabledAttr = $isUserLoggedIn ? '' : 'disabled';
                                        $tooltipText = $isUserLoggedIn ? '' : 'title="Accedi per effettuare questa operazione"';
                                        ?>

                                        <form method="POST" action="" class="flex-grow-1">
                                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill"
                                                <?= $disabledAttr ?>         <?= $tooltipText ?>>
                                                <?php if ($isUserLoggedIn): ?>
                                                    Contatta venditore
                                                <?php else: ?>
                                                    Accedi per contattare
                                                <?php endif; ?>
                                            </button>
                                        </form>

                                        <?php if ($product['stato'] === 'asta'): ?>
                                            <button type="button" class="btn btn-success btn-lg flex-grow-1 rounded-pill text-white"
                                                data-bs-toggle="modal" data-bs-target="#bidModal" <?= $disabledAttr ?>
                                                <?= $tooltipText ?>>
                                                Fai Offerta
                                            </button>
                                        <?php endif; ?>

                                    </div>

                                <?php endif; ?>

                            </div>
                        </div>

                    </div>
                </section>

            </article>
        </div>

        <?php if ($product['stato'] === 'asta'): ?>
            <?php
            $currentPrice = (float) $product["prezzo"];
            $minBid = $currentPrice + 0.5;
            $minBidFormatted = number_format($minBid, 2, '.', '');
            $minBidDisplay = number_format($minBid, 2, ',', '');
            ?>
            <div class="modal fade" id="bidModal" tabindex="-1" aria-labelledby="bidModalLabel" aria-hidden="true" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h2 class="modal-title fw-bold" id="bidModalLabel">Fai la tua offerta</h2>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form method="POST" action="">
                            <div class="modal-body p-4">
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <div>
                                        L'offerta minima è il prezzo attuale + 0.50€ .
                                    </div>
                                </div>

                                <div class="text-center mb-4">
                                    <span class="text-muted d-block">Prezzo Attuale</span>
                                    <span class="display-6 fw-bold text-success"><?= number_format($currentPrice, 2, ',', '') ?>
                                        €</span>
                                </div>

                                <div class="mb-3">
                                    <label for="bidAmount" class="form-label fw-bold">La tua offerta (€)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">€</span>
                                        <input type="number" class="form-control fw-bold border-success" id="bidAmount"
                                            name="bid_amount" step="0.01" min="<?= $minBidFormatted ?>"
                                            value="<?= $minBidFormatted ?>" required>
                                    </div>
                                    <div class="form-text text-success mt-2">
                                        Minimo richiesto: <strong><?= $minBidDisplay ?> €</strong>
                                    </div>
                                </div>

                                <input type="hidden" name="place_bid" value="true">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-success fw-bold text-white w-50">Conferma Offerta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
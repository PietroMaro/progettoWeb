<main class="container my-4">

    <div class="row gx-md-3">
        <?php foreach ($products as $product): ?>

            <div class="col-12 col-md-4 mb-4">
                <a href="product.php?id=<?= $product['idProdotto'] ?>" class="text-decoration-none text-dark">

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
                                        <div class="auction-timer fs-5  text-success " data-deadline="<?= $product['fineAsta'] ?>"></div>
                                    <?php endif; ?>
                            </header>
                            <div class=" pt-3 border-top">
                                <span class="fs-4 fw-bold text-dark d-block">
                                    <?= number_format($product['prezzo'], 2) ?> €
                                </span>
                                <?php if (!empty($product['fineAsta'])): ?>
                                    <small class="text-muted" style="font-size: 0.8rem">Offerta attuale</small>
                                <?php endif; ?>
                            </div>



                        </div>

                    </article>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        function updateTimers() {
            const timers = document.querySelectorAll('.auction-timer');
            const now = new Date().getTime();

            timers.forEach(timer => {

                const deadlineStr = timer.getAttribute('data-deadline').replace(" ", "T");
                const deadline = new Date(deadlineStr).getTime();

                const distance = deadline - now;

                if (distance < 0) {
                    timer.innerHTML = "SCADUTA";
                    timer.classList.remove('text-success');
                    timer.classList.add('text-danger');
                } else {
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                    let output = "";
                    if (days > 0) output += `${days}g `;
                    output += `${hours}h ${minutes}m`;

                    timer.innerHTML = output;
                }
            });
        }


        updateTimers();
        setInterval(updateTimers, 60000);
    });
</script>
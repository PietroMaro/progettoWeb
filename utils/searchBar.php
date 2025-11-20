<section class="container mb-4">
    <form action="index.php" method="GET">

        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">

                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <input type="text" class="form-control border-start-0 ps-0" name="search"
                                placeholder="Cerca un prodotto" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <select class="form-select" name="filter_type" id="filterType">
                            <option value="all" <?= (isset($_GET['filter_type']) && $_GET['filter_type'] == 'all') ? 'selected' : '' ?>>
                                Tutto
                            </option>
                            <option value="auction" <?= (isset($_GET['filter_type']) && $_GET['filter_type'] == 'auction') ? 'selected' : '' ?>>
                                Solo Aste
                            </option>
                            <option value="direct" <?= (isset($_GET['filter_type']) && $_GET['filter_type'] == 'direct') ? 'selected' : '' ?>>
                                Vendita Diretta
                            </option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3">
                        <select class="form-select" name="sort" id="sortSelect">
                            <option value="newest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : '' ?>>
                                Più recenti
                            </option>
                            <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>
                                Prezzo: Basso &rarr; Alto
                            </option>
                            <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>
                                Prezzo: Alto &rarr; Basso
                            </option>
                            <option value="ending_soon" id="optionEndingSoon" <?= (isset($_GET['sort']) && $_GET['sort'] == 'ending_soon') ? 'selected' : '' ?>>
                                In scadenza
                            </option>
                        </select>
                    </div>

                    <div class="col-12 col-md-1">
                        <button type="submit" class="btn btn-success w-100">
                            Vai
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </form>
</section>

<script src="scripts/searchBarScript.js"></script>
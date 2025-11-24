<!doctype html>
<html lang="it">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <script type="text/javascript" src="https://livejs.com/live.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <?php if (isset($templateParams)) {
    extract($templateParams);
  } ?>

  <title><?= $titolo ?></title>

  <link rel="stylesheet" href="css/style.css">


  <link rel="stylesheet" href="<?= $stylesheet ?>">

</head>

<body>

  <header>
    <?php

    require_once "utils/navbar.php";
    echo navbar();





    if (isset($searchBar)) {
      require($searchBar);
    }
    ?>

  </header>

  <main>
    <?php

    if (isset($nome)) {
      require($nome);
    }
    ?>
  </main>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

</body>

</html>
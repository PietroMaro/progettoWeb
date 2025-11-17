<?php



function navbar()
{
    return <<<HTML

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container-fluid">
    
    <a class="navbar-brand" href="index.php">
    </a>

    <button class="navbar-toggler" type="button" 
            data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" 
            aria-controls="navbarNav" 
            aria-expanded="false" 
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="sellProductPage.php">Vendi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="showcasePage.php">Vetrina</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profilo</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="faq.php">FAQ</a>
        </li>
      </ul>
      
      <ul class="navbar-nav ms-auto">
         <li class="nav-item">
            <a class="nav-link btn btn-outline-success btn-sm" href="login.php" >
              Sign In &rarr;
            </a>
         </li>
      </ul>

    </div>
  </div>
</nav>

HTML;
}
?>
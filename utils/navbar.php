<?php 



function navbar(){
    return <<<HTML
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Home</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#">Vendi <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Vetrina</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Profilo</a>
      </li>
      <li class="nav-item">
        <a class="nav-link " href="#">FAQ</a>
      </li>
    </ul>
  </div>
</nav>
HTML;
}
?>
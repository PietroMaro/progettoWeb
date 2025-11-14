<?php

function navbar()
{

    return <<<HTML
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    
    <a class="navbar-brand" href="/progettoWeb/index.php">
        <div class="logo-square"></div>
    </a>
    
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
    
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/progettoWeb/index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/progettoWeb/sellProductPage.php">Vendi</a>
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
        
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link btn-sign-in" href="#">
                    Sign in &rarr;
                </a>
            </li>
        </ul>
        
    </div>
    
</nav>
HTML;
}
?>
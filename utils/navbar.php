<?php
require_once "utils/login.php";
function navbar()
{
    global $dbError;
    global $activeView; 
    $modalHtml = loginForm($dbError,$activeView);

    $toastHtml = 
    <<<HTML
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
          <div id="loginToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-white" style="background-color: var(--colore-principale);">
              <strong class="me-auto">Unisell</strong>
              <small>Ora</small>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
              Login effettuato con successo! 👋
            </div>
          </div>
        </div>
    HTML;

    $toastScript = "";
    if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
      $toastScript = 
      <<<JS
      <script>
      document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.getElementById('loginToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
        });
      </script>
      JS;
      unset($_SESSION['login_success']);
    }

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
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="sellProductPage.php">Vendi</a></li>
            <li class="nav-item"><a class="nav-link" href="showcasePage.php">Vetrina</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php">Profilo</a></li>
            <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
          </ul>
          
          <ul class="navbar-nav ms-auto">
             <li class="nav-item">
                <button 
                  type="button" 
                  class="nav-link btn btn-outline-success btn-sm" 
                  data-bs-toggle="modal" 
                  data-bs-target="#loginModal" 
                  onclick="resetLoginModal()"
                >
                   Sign In &rarr;
                </button>
             </li>
          </ul>

        </div>
      </div>
    </nav>

    $modalHtml

    $toastHtml
    $toastScript
  HTML;
}
?>
<?php
require_once __DIR__ . "/signup.php";

global $dbError; 

try {
    $dbHandler = new UserManager();
} catch (Exception $e) {
    $dbHandler = null;
    if (empty($dbError)) {
        $dbError = "Servizio non disponibile: Impossibile connettersi al Database.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($dbHandler) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        try {
            $userId = $dbHandler->login($email, $password);
            if ($userId) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['user_id'] = $userId; 
                header("Location: index.php");
                exit(); 
            } else {
                $dbError = "Email o Password errati.";
            }
        } catch (Exception $e) {
            $dbError = "Errore tecnico durante il login.";
        }
    } 
}
function loginForm($errorMessage = null)
{
    $signUpForm = signUpForm();
    $alertHtml = "";
    $autoOpenScript = "";
    $isPostRequest = ($_SERVER["REQUEST_METHOD"] === "POST");
    if ($errorMessage && $isPostRequest) {
        $alertHtml = <<<HTML
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <div>
                    $errorMessage
                </div>
            </div>
        HTML;
        $autoOpenScript = <<<JS
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    myModal.show();
                });
            </script>
        JS;
    }

    return <<<HTML
      <link rel="stylesheet" href="css/style.css"> 
      <link rel="stylesheet" href="css/login.css"> 

      <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body p-4"> 
              <div id="login-container">
                <h1 class="unisell-title" style="font-size: 2rem; margin-bottom: 1.5rem;">Unisell</h1>
                <h1 class="unisell-title" style="font-size: 2rem; margin-bottom: 1.5rem;">Login</h1>

                $alertHtml

                <form action="index.php" method="POST" class="needs-validation" novalidate>
                  <div class="mb-3">
                      <label for="modal-email" class="form-label">Email</label>
                      <input type="email" class="form-control" id="modal-email" name="email" required placeholder="Inserisci la tua email">
                      <div class="invalid-feedback">Inserisci un indirizzo email valido.</div>
                  </div>

                  <div class="mb-4">
                      <label for="modal-password" class="form-label">Password</label>
                      <input type="password" class="form-control" id="modal-password" name="password" required placeholder="Inserisci la tua password">
                      <div class="invalid-feedback">La password è obbligatoria.</div>
                  </div>

                  <button type="submit" class="btn btn-unisell-primary">Sign In</button>
                </form>

                <div class="register-link">
                  Non hai un account? 
                  <a href="#" onclick="switchView('register'); return false;">Registrati</a>
                </div>
              </div>
              
              $signUpForm
            </div>
          </div>
        </div>
      </div>

      $autoOpenScript

      <script>
      function switchView(view) {
          const loginContainer = document.getElementById('login-container');
          const registerContainer = document.getElementById('register-container');
          if (view === 'register') {
              loginContainer.classList.add('d-none');
              registerContainer.classList.remove('d-none');
          } else {
              registerContainer.classList.add('d-none');
              loginContainer.classList.remove('d-none');
          }
      }

      function resetLoginModal() {
          const errorAlert = document.querySelector('#loginModal .alert');
          if (errorAlert) {
              errorAlert.remove();
          }
          
          const form = document.querySelector('#loginModal form');
          if (form) {
              form.classList.remove('was-validated');
              form.reset();
          }
      }

      (function () {
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
              if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
              }
              form.classList.add('was-validated')
            }, false)
        })
      })()
      </script>
  HTML;
}
?>
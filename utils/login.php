<?php
require_once __DIR__ . "/signup.php";

global $dbError; 
$activeView = 'login'; 
try {
    $dbHandler = new UserManager();
} catch (Exception $e) {
    $dbHandler = null;
    if (empty($dbError)) {
        $dbError = "Servizio non disponibile: Impossibile connettersi al Database.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(
      isset($_POST['new_selected_chat_list_id']) ||
      isset($_POST['admin_new_selected_chat_list_id']) ||
      isset($_POST['productName']) ||
      isset($_POST['chat-image']) ||
      isset($_POST['chat-message'])  ||
      isset($_POST['is_new_chat_message']) || 
      isset($_POST['new_offerta_chat']) ||
      isset($_POST['segnalazione_chat']) ||
      isset($_POST['delete_faq']) ||
      isset($_POST['create_faq']) ||
      isset($_POST['current_chat_refuse']) ||
      isset($_POST['current_chat_accept']) 

    ){
      return;
    }
    $isLogin = !isset($_POST['nome']) ;
    if ($dbHandler) {
      if($isLogin){
        $activeView = 'login'; 
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        try {
            if($dbHandler->isBanned($email,$password)){
              $dbError = "L'account è stato eliminato a seguito di una segnalazione, se si hanno domande contattare unisell.helpdesk@gmail.com";
            }
            else{
              $userId = $dbHandler->login($email, $password, false);
              if ($userId) {
                  activateLoginSession($userId, false);
              } else {
                  $userId = $dbHandler->login($email, $password, true);
                  if ($userId) {
                    activateLoginSession($userId, true);
                  }else{
                    $dbError = "Email o Password errati.";
                  }
              }
           }
        } catch (Exception $e) {
            $dbError = "Errore tecnico durante il login.";
        }
    }
    else{
      $activeView = 'register'; 
      $nomeForm = $_POST['nome'] ?? '';
      $cognome = $_POST['cognome'] ?? '';
      $username = $_POST['username'] ?? '';
      $descrizione = $_POST['descrizione'] ?? '';
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';
      $propic = $_FILES['propic'] ?? null;
      try {
          $dbHandler->registerUser(
              $propic, 
              $nomeForm , 
              $cognome, 
              $username, 
              $descrizione, 
              $email, 
              $password
          );
          $userId = $dbHandler->login($email, $password, false);
          if ($userId) {
              activateLoginSession($userId, false);
          } else {
              header("Location: index.php");
              exit();
          }
      } catch (Exception $e) {
          if ($e->getMessage() === "Questa email è già registrata.") {
              $dbError = "L'email inserita è già in uso. Scegline un'altra.";
          } else {
              $dbError = "Errore tecnico durante la registrazione. Riprova più tardi.";
          }
      }
    } 
  }
}

function activateLoginSession($userId, $isAdmin){
  if (session_status() === PHP_SESSION_NONE) session_start();
  $_SESSION['user_id'] = $userId; 
  $_SESSION['login_success'] = true;
  $_SESSION['is_admin'] = $isAdmin;
  header(header: "Location: index.php");
  exit();
}

function loginForm($errorMessage = null, $view = 'login')
{
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

                    if ('$view' === 'register') {
                        switchView('register');
                    }
                });
            </script>
        JS;
    }
    $signUpForm = signUpForm($alertHtml);

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
                  <a href="#" onclick="switchView('register'); resetLoginModal(); return false;">Registrati</a>
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
        const errorAlerts = document.querySelectorAll('#loginModal .alert');
        errorAlerts.forEach(alert => alert.remove());
          
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
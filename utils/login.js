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

function validateLoginData () {
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
}

validateLoginData();
<?php
function signUpForm($alertHtml)
{
    //TODO mettere un immagine locale
    $profileIcon = '<svg id="profile-placeholder" xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#1a1a1a" class="bi bi-person-fill" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>';

    return <<<HTML
    <div id="register-container" class="d-none">
        <h1 class="unisell-title" style="font-size: 2rem; margin-bottom: 1.5rem;">Crea Account</h1>
        
        $alertHtml
        
        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            
            <div class="row mb-3">
                
                <div class="col-4 d-flex flex-column align-items-center">
                    <label class="form-label" style="color: #212529; font-weight: 500;">profile Photo</label>
                    
                    <div class="mt-1 position-relative" style="cursor: pointer;" onclick="document.getElementById('propic-input').click();">
                        
                        $profileIcon
                        
                        <img id="profile-preview" src="" alt="Preview" style="display: none; width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                        
                        <div style="font-size: 0.7rem; color: var(--colore-principale); margin-top: 5px; text-align: center;">
                            Clicca per caricare
                        </div>
                    </div>

                    <input type="file" id="propic-input" name="propic" accept="image/*" style="display: none;" onchange="previewProfileImage(this)">
                </div>

                <div class="col-8">
                    <div class="mb-2">
                        <label for="reg-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="reg-name" name="nome" required placeholder="Inserisci il tuo nome">
                    </div>
                    <div>
                        <label for="reg-surname" class="form-label">Surname</label>
                        <input type="text" class="form-control" id="reg-surname" name="cognome" required placeholder="Inserisci il tuo cognome">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="reg-username" class="form-label">Username</label>
                <input type="text" class="form-control" id="reg-username" name="username" required placeholder="Inserisci il tuo username">
            </div>

            <div class="mb-3">
                <label for="reg-desc" class="form-label">Descrizione profilo</label>
                <textarea class="form-control" id="reg-desc" name="descrizione" rows="2" placeholder="Inserisci una breve descrizione (max 50 parole)"></textarea>
            </div>

            <div class="mb-3">
                <label for="reg-email" class="form-label">Email</label>
                <input type="email" class="form-control" id="reg-email" name="email" required placeholder="Inserisci la tua email">
            </div>

            <div class="mb-3">
                <label for="reg-password" class="form-label">Password</label>
                <input type="password" class="form-control" id="reg-password" name="password" required placeholder="Inserisci la tua password">
            </div>

            <button type="submit" class="btn btn-unisell-primary">Sign Up</button>
        </form>

        <div class="register-link">
            Hai già un account? 
            <a href="#" onclick="switchView('login'); resetLoginModal(); return false;">Accedi</a>
        </div>
    </div>

    <script src="utils\signup.js"></script>
HTML;
}
?>
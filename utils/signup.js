function previewProfileImage(input) {
        const placeholder = document.getElementById('profile-placeholder');
        const preview = document.getElementById('profile-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                placeholder.style.display = 'none';
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
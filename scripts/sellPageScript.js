document.addEventListener('DOMContentLoaded', function () {

    const auctionSwitch = document.getElementById('auctionSwitch');
    const auctionDateContainer = document.querySelector('div[data-role="auctionDateContainer"]');
    const auctionDateInput = document.getElementById('auctionEndDate');
    const form = document.querySelector('form');
    const fileInput = document.getElementById('fileUpload');
    const previewWrapper = document.querySelector('div[data-role="image-preview-wrapper"]');


    auctionSwitch.addEventListener('change', function () {
        if (this.checked) {
            auctionDateContainer.style.display = 'block';
            auctionDateInput.required = true;
        } else {
            auctionDateContainer.style.display = 'none';
            auctionDateInput.required = false;

        }
    });

    if (auctionSwitch.checked) {
        auctionSwitch.dispatchEvent(new Event('change'));
    }



    fileInput.addEventListener('change', function (event) {
        previewWrapper.innerHTML = '';

        if (event.target.files && event.target.files.length > 0) {
            const files = Array.from(event.target.files).slice(0, 4);

            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const col = document.createElement('div');
                    col.className = 'col';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-fluid rounded';

                    col.appendChild(img);
                    previewWrapper.appendChild(col);
                }
                reader.readAsDataURL(file);
            });
        }
    });


    form.addEventListener('submit', function (event) {
        const allDeleteCheckboxes = document.querySelectorAll('input[name="delete_images[]"]');
        const totalExistingImages = allDeleteCheckboxes.length;

        const checkedDeleteCheckboxes = document.querySelectorAll('input[name="delete_images[]"]:checked');
        const imagesToDelete = checkedDeleteCheckboxes.length;

        let newImages = 0;
        if (fileInput.files) {
            newImages = fileInput.files.length;
        }

        const finalCount = (totalExistingImages - imagesToDelete) + newImages;

        if (finalCount <= 0) {
            event.preventDefault();

            alert("Attenzione: Non puoi salvare il prodotto senza immagini.");

            fileInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});

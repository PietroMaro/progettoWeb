document.addEventListener('DOMContentLoaded', function () {
    const filterType = document.getElementById('filterType');
    const sortSelect = document.getElementById('sortSelect');
    const optionEndingSoon = document.getElementById('optionEndingSoon');

    function toggleEndingSoonOption() {
        if (filterType.value === 'direct') {
            optionEndingSoon.hidden = true;
            optionEndingSoon.disabled = true;


            if (sortSelect.value === 'ending_soon') {
                sortSelect.value = 'newest';
            }
        } else {
            optionEndingSoon.hidden = false;
            optionEndingSoon.disabled = false;
        }
    }

    filterType.addEventListener('change', toggleEndingSoonOption);


    toggleEndingSoonOption();
});
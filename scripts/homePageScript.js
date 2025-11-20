 document.addEventListener('DOMContentLoaded', () => {

        function updateTimers() {
            const timers = document.querySelectorAll('.auction-timer');
            const now = new Date().getTime();

            timers.forEach(timer => {

                const deadlineStr = timer.getAttribute('data-deadline').replace(" ", "T");
                const deadline = new Date(deadlineStr).getTime();

                const distance = deadline - now;

                if (distance < 0) {
                    timer.innerHTML = "SCADUTA";
                    timer.classList.remove('text-success');
                    timer.classList.add('text-danger');
                } else {
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                    let output = "";
                    if (days > 0) output += `${days}g `;
                    output += `${hours}h ${minutes}m`;

                    timer.innerHTML = output;
                }
            });
        }


        updateTimers();
        setInterval(updateTimers, 60000);
    });
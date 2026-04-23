document.addEventListener("DOMContentLoaded", () => {
    const timers = document.querySelectorAll('.auction-timer');

    timers.forEach(timer => {
        const endTime = new Date(timer.dataset.endtime).getTime();

        const updateTimer = setInterval(() => {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                clearInterval(updateTimer);
                timer.innerHTML = "<div class='time-box'><span>Status</span>CLOSED</div>";
                
                // Disable bid button if present
                const bidForm = timer.closest('.bid-info')?.querySelector('form');
                if(bidForm) {
                    const btn = bidForm.querySelector('button');
                    const input = bidForm.querySelector('input');
                    if(btn) { btn.disabled = true; btn.textContent = 'Auction Closed'; }
                    if(input) input.disabled = true;
                }
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timer.innerHTML = `
                <div class="time-box">${days}<span>Days</span></div>
                <div class="time-box">${hours.toString().padStart(2, '0')}<span>Hours</span></div>
                <div class="time-box">${minutes.toString().padStart(2, '0')}<span>Mins</span></div>
                <div class="time-box">${seconds.toString().padStart(2, '0')}<span>Secs</span></div>
            `;
        }, 1000);
    });
});

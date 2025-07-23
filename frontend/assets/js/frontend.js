document.addEventListener("DOMContentLoaded", () => {
    const countdownWrapper = document.querySelector('.wizemamo-countdown');
    if (!countdownWrapper) return;

    const target = countdownWrapper.getAttribute('data-target');
    const parts = JSON.parse(countdownWrapper.getAttribute('data-parts') || '[]');
    if (!target || !parts.length) return;

    const updateCountdown = () => {
        const now = new Date().getTime();
        const targetTime = new Date(target).getTime();
        const diff = targetTime - now;

        if (diff <= 0) {
            countdownWrapper.innerHTML = '<p>Countdown ends. This website will be live soon!</p>';
            return;
        }

        const seconds = Math.floor(diff / 1000) % 60;
        const minutes = Math.floor(diff / (1000 * 60)) % 60;
        const hours = Math.floor(diff / (1000 * 60 * 60)) % 24;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));

        const values = {days, hours, minutes, seconds};
        const labels = {
            days: ['day', 'days'],
            hours: ['hour', 'hours'],
            minutes: ['min', 'mins'],
            seconds: ['sec', 'secs']
        };

        countdownWrapper.innerHTML = '';

        parts.forEach(part => {
            const value = values[part];
            const label = value === 1 ? labels[part][0] : labels[part][1];
            const box = document.createElement('div');
            box.className = 'wizemamo-countdown-item';
            box.innerHTML = `<strong>${value}</strong><span>${label}</span>`;
            countdownWrapper.appendChild(box);
        });
    };

    updateCountdown();
    setInterval(updateCountdown, 1000);
});

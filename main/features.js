document.addEventListener('DOMContentLoaded', () => {
    const featureSections = document.querySelectorAll('.feature-section');

    function checkInView() {
        featureSections.forEach(section => {
            const rect = section.getBoundingClientRect();
            const isInView = (rect.top >= 0 && rect.bottom <= window.innerHeight) ||
                             (rect.bottom >= 0 && rect.top <= window.innerHeight);
            if (isInView) {
                section.classList.add('in-view');
            }
        });
    }

    window.addEventListener('scroll', checkInView);
    window.addEventListener('resize', checkInView);
    checkInView(); // Initial check on load
});
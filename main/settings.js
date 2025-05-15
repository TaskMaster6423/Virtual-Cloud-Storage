document.addEventListener('DOMContentLoaded', () => {
    const settingsNav = document.querySelector('.settings-nav');
    const settingsSections = document.querySelectorAll('.settings-section');

    if (settingsNav && settingsSections) {
        settingsNav.addEventListener('click', (event) => {
            if (event.target.tagName === 'A') {
                event.preventDefault();
                const targetSection = event.target.closest('li').dataset.section;

                settingsSections.forEach(section => {
                    section.classList.remove('active');
                });

                const activeSection = document.getElementById(targetSection);
                if (activeSection) {
                    activeSection.classList.add('active');
                }
            }
        });
    }

    // Example of saving changes (simulated)
    const saveButtons = document.querySelectorAll('.save-button');
    saveButtons.forEach(button => {
        button.addEventListener('click', () => {
            alert('Changes saved (simulated)!');
            // In a real application, you would send the updated data to your backend.
            // You could add a visual confirmation animation here (e.g., a checkmark icon that fades in and out).
        });
    });

     // Example of changing password (simulated)
    const changePasswordButton = document.querySelector('.change-password-button');
    if(changePasswordButton){
        changePasswordButton.addEventListener('click', () => {
            alert('Change password functionality (simulated)!');
            // In a real application, you would show a modal or navigate to a change password page.
        });
    }

    const changePaymentButton = document.querySelector('.change-payment-button');
    if(changePaymentButton){
         changePaymentButton.addEventListener('click', () => {
            alert('Change payment method functionality (simulated)!');
            // In a real application, you would show a modal or navigate to a payment settings page.
        });
    }
});
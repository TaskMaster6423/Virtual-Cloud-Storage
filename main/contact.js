document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');
    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const messageError = document.getElementById('messageError');
    const formSuccess = document.getElementById('formSuccess');
    const formErrorDiv = document.getElementById('formError');

    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();
            let isValid = true;

            // Basic form validation
            if (nameInput.value.trim() === '') {
                nameError.textContent = 'Please enter your name.';
                isValid = false;
            } else {
                nameError.textContent = '';
            }

            if (emailInput.value.trim() === '') {
                emailError.textContent = 'Please enter your email address.';
                isValid = false;
            } else if (!isValidEmail(emailInput.value.trim())) {
                emailError.textContent = 'Please enter a valid email address.';
                isValid = false;
            } else {
                emailError.textContent = '';
            }

            if (messageInput.value.trim() === '') {
                messageError.textContent = 'Please enter your message.';
                isValid = false;
            } else {
                messageError.textContent = '';
            }

            if (isValid) {
                // Simulate form submission (replace with actual AJAX call)
                console.log('Form submitted:', {
                    name: nameInput.value,
                    email: emailInput.value,
                    subject: document.getElementById('subject').value,
                    message: messageInput.value
                });

                // Display success message and reset form
                formSuccess.style.display = 'block';
                formErrorDiv.style.display = 'none';
                contactForm.reset();

                // Optionally hide success message after a few seconds
                setTimeout(() => {
                    formSuccess.style.display = 'none';
                }, 3000);
            } else {
                formSuccess.style.display = 'none';
                formErrorDiv.style.display = 'none'; // Ensure error message is hidden if now invalid
            }
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Initial load animation for hero section
    const heroTitle = document.querySelector('.contact-header h1');
    const heroParagraph = document.querySelector('.contact-header p');

    if (heroTitle) {
        heroTitle.classList.add('animate'); // You'
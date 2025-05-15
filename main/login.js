document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const googleLoginButton = document.querySelector('.google-login-button');

    if (loginForm) {
        loginForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent default form submission for now
            const usernameEmailInput = document.getElementById('username-email');
            const passwordInput = document.getElementById('password');

            // In a real application, you would send this data to your backend for authentication.
            console.log('Logging in with:', usernameEmailInput.value, passwordInput.value);
            alert('Simulating login...');
            // window.location.href = '/dashboard'; // Redirect to dashboard after successful login
        });
    }

    if (googleLoginButton) {
        googleLoginButton.addEventListener('click', () => {
            // In a real application, you would initiate the Google Sign-In flow here.
            console.log('Initiating Google Login...');
            alert('Redirecting to Google for login (simulated)...');
            // window.location.href = '/google-auth'; // Redirect to your Google Auth endpoint
        });
    }
});
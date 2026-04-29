// resources/js/app.js

document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.querySelector('#password');
    const toggleButton = document.querySelector('#togglePassword');
    const eyeIcon = document.querySelector('#eyeIcon');

    if (toggleButton && passwordInput) {
        toggleButton.addEventListener('click', function () {
            // Toggle tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

        
        });
    }
});
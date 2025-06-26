import './bootstrap';
  import 'preline';
  import 'flowbite';
  document.addEventListener('DOMContentLoaded', function () {
  const languageDropdown = document.getElementById('languageDropdown');
  if (languageDropdown) {
  languageDropdown.addEventListener('click', function() {
  // Custom JS if needed, otherwise Bootstrap's JS will handle it
        






}
);
  
}

}
);
  
// resources/js/app.js (or a new modals.js, then import it)

document.addEventListener('alpine:init', () => {
    Alpine.store('modals', {
        showLogin: false,
        showRegister: false,
        // Add other modal states here

        openLogin() {
            this.showLogin = true;
        },
        closeLogin() {
            this.showLogin = false;
        },
        openRegister() {
            this.showRegister = true;
        },
        closeRegister() {
            this.showRegister = false;
        },
        // ... more functions as needed
    });
});
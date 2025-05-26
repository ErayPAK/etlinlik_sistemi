document.addEventListener('DOMContentLoaded', function() {
    const girisForm = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const sifreInput = document.getElementById('sifre');
    const girisMesajiDiv = document.getElementById('girisMesaji');

    girisForm.addEventListener('submit', function(event) {
        if (emailInput.value.trim() === '' || sifreInput.value.trim() === '') {
            event.preventDefault();
            girisMesajiDiv.textContent = 'Lütfen e-posta ve şifrenizi girin.';
            girisMesajiDiv.className = 'hatali';
            return;
        }
        // İlerleyen aşamalarda daha kapsamlı kontroller eklenebilir.
    });
});
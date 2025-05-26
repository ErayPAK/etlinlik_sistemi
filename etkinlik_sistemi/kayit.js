document.addEventListener('DOMContentLoaded', function() {
    const kayitForm = document.querySelector('form');
    const sifreInput = document.getElementById('sifre');
    const sifreTekrarInput = document.getElementById('sifreTekrar');
    const kayitMesajiDiv = document.getElementById('kayitMesaji');

    kayitForm.addEventListener('submit', function(event) {
        if (sifreInput.value !== sifreTekrarInput.value) {
            event.preventDefault();
            kayitMesajiDiv.textContent = 'Şifreler eşleşmiyor!';
            return;
        }
        if (sifreInput.value.length < 6) {
            event.preventDefault();
            kayitMesajiDiv.textContent = 'Şifre en az 6 karakter olmalıdır.';
            return;
        }
        // Şifreler eşleşiyorsa ve uzunluk yeterliyse form gönderilir (sunucu tarafında işlenir).
    });
});
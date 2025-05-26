$(document).ready(function() {
    // Sepeti getir fonksiyonu
    function sepetiGetir() {
        $.ajax({
            url: 'backend/sepeti_getir.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) { // Yanıtı 'response' içinde alıyoruz
                if (response && response.hata) { // Hata kontrolü
                    console.error("Sepet getirilirken hata oluştu:", response.hata);
                    $('#sepet-listesi').html('<p>Sepet yüklenirken bir hata oluştu.</p>');
                } else {
                    sepetiListele(response); // Hata yoksa sepetiListele fonksiyonunu çağır
                }
            },
            error: function(xhr, status, error) {
                console.error("Sepet getirilirken hata oluştu:", error);
                $('#sepet-listesi').html('<p>Sepet yüklenirken bir hata oluştu.</p>');
            }
        });
    }

    // Sepeti listele fonksiyonu
    function sepetiListele(sepet) {
        const sepetListesiDiv = $('#sepet-listesi');
        sepetListesiDiv.empty();
        let toplamTutar = 0;

        if (sepet && Object.keys(sepet).length > 0) {
            $.each(sepet, function(sepetId, urun) { // Sepet ID ve urun nesnesini alıyoruz
                const satirToplam = urun.bilet_fiyati * urun.adet;
                toplamTutar += satirToplam;

                const urunDiv = $('<div class="sepet-urun">');
                urunDiv.append(`<h3>${urun.baslik}</h3>`);
                urunDiv.append(`<p>Bilet Türü: ${urun.bilet_turu}</p>`);
                urunDiv.append(`<p>Fiyat: ${urun.bilet_fiyati} TL</p>`);
                urunDiv.append(`<p>Adet: ${urun.adet}</p>`);
                urunDiv.append(`<p>Satır Toplamı: ${satirToplam.toFixed(2)} TL</p>`);

                // Sepetten çıkar butonu
                const cikarButonu = $('<button class="sepette-cikar">Sepetten Çıkar</button>');
                cikarButonu.data('sepet_id', sepetId); // Doğru sepetId'yi gönderiyoruz.
                urunDiv.append(cikarButonu);

                sepetListesiDiv.append(urunDiv);
            });

            const toplamTutarDiv = $('#sepet-toplam');
            toplamTutarDiv.html(`<b>Toplam Tutar: ${toplamTutar.toFixed(2)} TL</b>`);

            // Sepetten çıkar butonlarına tıklanma olayını ekliyoruz.
            $('.sepette-cikar').on('click', function() {
                const sepetId = $(this).data('sepet_id');
                $.ajax({
                    url: 'backend/sepetten_cikar.php',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        sepet_id: sepetId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            sepetiGetir(); // Sepeti yeniden yükle
                        } else if (response.hata) {
                            alert(response.hata);
                        } 
                        else {
                            alert("Bir hata oluştu!");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Sepetten çıkarma hatası:", error);
                        alert("Ürün sepetten çıkarılırken bir hata oluştu.");
                    }
                });
            });
        } else {
            sepetListesiDiv.append('<p>Sepetinizde ürün bulunmamaktadır.</p>');
            $('#odeme-yap-butonu').hide();
        }
    }

    // Sayfa yüklendiğinde sepeti getir
    sepetiGetir();
});

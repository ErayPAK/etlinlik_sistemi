$(document).ready(function() {
    function etkinlikleriListele(etkinlikler) {
        const etkinlikListesiDiv = $('#etkinlik-listesi');
        etkinlikListesiDiv.empty();
    
        if (etkinlikler.length > 0) {
            $.each(etkinlikler, function(index, etkinlik) {
                const etkinlikDiv = $('<div class="etkinlik">');
                etkinlikDiv.append(`<h3>${etkinlik.baslik}</h3>`);
                etkinlikDiv.append(`<p>${etkinlik.aciklama.substring(0, 100)}...</p>`);
                etkinlikDiv.append(`<p>Tarih: ${etkinlik.tarih}</p>`);
                etkinlikDiv.append(`<p>Tam Bilet Fiyatı: ${etkinlik.bilet_fiyati_tam} TL</p>`);
                etkinlikDiv.append(`<p>Öğrenci Bilet Fiyatı: ${etkinlik.bilet_fiyati_ogrenci} TL</p>`);
                etkinlikDiv.append(`<p>Kalan Kontenjan: ${etkinlik.kontenjan}</p>`);
                if (etkinlik.gorsel_url) {
                    etkinlikDiv.append(`<img src="${etkinlik.gorsel_url}" alt="${etkinlik.baslik}" style="max-width: 100px;">`);
                }
                const biletTuruSecimi = $('<div class="bilet-turu-secimi">');
                biletTuruSecimi.append('<label>Bilet Türü: </label>');
                // Buradaki değişiklik: "Tam" ve "Öğrenci" metinlerini ekledik
                const tamBiletSecenek = $('<input type="radio" name="bilet_turu_' + etkinlik.id + '" value="Tam" checked> Tam');
                const ogrenciBiletSecenek = $('<input type="radio" name="bilet_turu_' + etkinlik.id + '" value="Öğrenci"> Öğrenci');
                biletTuruSecimi.append(tamBiletSecenek);
                biletTuruSecimi.append(ogrenciBiletSecenek);
                etkinlikDiv.append(biletTuruSecimi);
    
                const sepeteEkleButonu = $('<button class="sepete-ekle">Sepete Ekle</button>');
                sepeteEkleButonu.data('id', etkinlik.id);
                sepeteEkleButonu.data('bilet_fiyati_tam', etkinlik.bilet_fiyati_tam);
                sepeteEkleButonu.data('bilet_fiyati_ogrenci', etkinlik.bilet_fiyati_ogrenci);
                etkinlikDiv.append(sepeteEkleButonu);
                etkinlikListesiDiv.append(etkinlikDiv);
            });
        } else {
            etkinlikListesiDiv.append('<p>Henüz etkinlik bulunmuyor.</p>');
        }
    
        $('.sepete-ekle').on('click', function() {
            const etkinlikId = $(this).data('id');
            const biletFiyatiTam = $(this).data('bilet_fiyati_tam');
            const biletFiyatiOgrenci = $(this).data('bilet_fiyati_ogrenci');
            const biletTuru = $('input[name="bilet_turu_' + etkinlikId + '"]:checked').val();
    
            let biletFiyati = (biletTuru === 'Tam') ? biletFiyatiTam : biletFiyatiOgrenci;
    
            $.ajax({
                url: 'backend/sepete_ekle.php',
                method: 'POST',
                dataType: 'json',
                data: { etkinlik_id: etkinlikId, bilet_turu: biletTuru, bilet_fiyati: biletFiyati },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        sepetiGetir();
                    } else {
                        alert(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Sepete ekleme hatası:", error);
                    alert("Sepete eklenirken bir hata oluştu.");
                }
            });
        });
    }
    
    function sepetiGetir() {
        $.ajax({
            url: 'backend/sepeti_getir.php',
            method: 'GET',
            dataType: 'json',
            success: function(sepet) {
                sepetiListele(sepet);
            },
            error: function(xhr, status, error) {
                console.error("Sepet getirilirken hata oluştu:", error);
                $('#sepet-listesi').html('<p>Sepet yüklenirken bir hata oluştu.</p>');
            }
        });
    }

    function sepetiListele(sepet) {
        const sepetListesiDiv = $('#sepet-listesi');
        sepetListesiDiv.empty();
        let toplamTutar = 0;

        if (sepet && Object.keys(sepet).length > 0) {
            $.each(sepet, function(index, urun) {
                const satirToplam = urun.bilet_fiyati * urun.adet;
                toplamTutar += satirToplam;

                const urunDiv = $('<div class="sepet-urun">');
                urunDiv.append(`<h3>${urun.baslik}</h3>`);
                urunDiv.append(`<p>Bilet Türü: ${urun.bilet_turu}</p>`);
                urunDiv.append(`<p>Fiyat: ${urun.bilet_fiyati} TL</p>`);
                urunDiv.append(`<label>Adet: </label>`);
                
                // Adet artırma butonu
                const adetArtirButonu = $('<button class="adet-artir">+</button>');
                adetArtirButonu.data('etkinlik_id', urun.etkinlik_id);
                adetArtirButonu.data('bilet_turu', urun.bilet_turu);
                urunDiv.append(adetArtirButonu);

                urunDiv.append(`<span class="adet">${urun.adet}</span>`); // Adet bilgisini göster

                 // Adet azaltma butonu
                const adetAzaltButonu = $('<button class="adet-azalt">-</button>');
                adetAzaltButonu.data('etkinlik_id', urun.etkinlik_id);
                adetAzaltButonu.data('bilet_turu', urun.bilet_turu);
                urunDiv.append(adetAzaltButonu);
               
                urunDiv.append(`<p>Satır Toplamı: ${satirToplam.toFixed(2)} TL</p>`);
                
                // Sepetten Çıkar Butonu
                const sepettenCikarButonu = $('<button class="sepetten-cikar">X</button>');
                sepettenCikarButonu.data('etkinlik_id', urun.etkinlik_id);  // Etkinlik ID'sini sakla
                sepettenCikarButonu.data('bilet_turu', urun.bilet_turu); // Bilet türünü sakla
                urunDiv.append(sepettenCikarButonu);
                sepetListesiDiv.append(urunDiv);
            });

            const toplamTutarDiv = $('#sepet-toplam');
            toplamTutarDiv.html(`<b>Toplam Tutar: ${toplamTutar.toFixed(2)} TL</b>`);
            
             // Ödeme Yap Butonu
            const odemeYapButonu = $('<button id="odeme-yap-butonu">Ödeme Yap</button>');
            odemeYapButonu.on('click', function() {
                // Ödeme sayfasına yönlendirme (simülasyon)
                $.ajax({
                    url: 'backend/bileti_al.php',
                    method: 'POST',
                    dataType: 'json',
                    data: { etkinlik_id: 1 }, // Sepetteki ilk etkinliğin ID'sini gönderiyoruz (basit bir örnek).
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            // Sepeti temizledikten sonra sepeti yeniden yükle
                            sepetiGetir();
                            // İsteğe bağlı: Başka bir sayfaya yönlendirme
                            // window.location.href="index.html";
                        } else {
                            alert(response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Bilet alım hatası:", error);
                        alert("Bilet alınırken bir hata oluştu.");
                    }
                });
            });
            sepetListesiDiv.append(odemeYapButonu);

        } else {
            sepetListesiDiv.append('<p>Sepetinizde ürün bulunmamaktadır.</p>');
             $('#odeme-yap-butonu').hide();
        }
        
        // Sepetten Çıkar İşlemi
        $('.sepetten-cikar').on('click', function() {
            const etkinlikId = $(this).data('etkinlik_id');
             const biletTuru = $(this).data('bilet_turu');
            
            $.ajax({
                url: 'backend/sepetten_cikar.php',
                method: 'POST',
                dataType: 'json',
                data: { etkinlik_id: etkinlikId, bilet_turu: biletTuru },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        sepetiGetir(); // Sepeti güncelle
                    } else {
                        alert(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Sepetten çıkarma hatası:", error);
                    alert("Ürün sepetten çıkarılırken bir hata oluştu.");
                }
            });
        });

        // Adet artırma işlemi
        $('.adet-artir').on('click', function() {
            const etkinlikId = $(this).data('etkinlik_id');
            const biletTuru = $(this).data('bilet_turu');
            
             $.ajax({
                url: 'backend/sepete_ekle.php', // Aynı endpoint'i kullanıyoruz, backend'de kontrol yapacağız
                method: 'POST',
                dataType: 'json',
                data: { etkinlik_id: etkinlikId, bilet_turu: biletTuru }, // bilet_turu'nu da gönderiyoruz
                success: function(response) {
                    if (response.success) {
                        sepetiGetir(); // Sepeti ve dolayısıyla adet bilgisini güncelle
                    } else {
                        alert(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Adet artırma hatası:", error);
                    alert("Adet artırılırken bir hata oluştu.");
                }
            });
        });

        // Adet azaltma işlemi
        $('.adet-azalt').on('click', function() {
            const etkinlikId = $(this).data('etkinlik_id');
             const biletTuru = $(this).data('bilet_turu');

            $.ajax({
                url: 'backend/sepetten_cikar.php', // Mevcut endpoint'i kullanıyoruz
                method: 'POST',
                dataType: 'json',
                data: { etkinlik_id: etkinlikId, bilet_turu: biletTuru },
                success: function(response) {
                    if (response.success) {
                        sepetiGetir(); // Sepeti ve dolayısıyla adet bilgisini güncelle
                    } else {
                        alert(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Adet azaltma hatası:", error);
                    alert("Adet azaltılırken bir hata oluştu.");
                }
            });
        });
    }

    // Etkinlikleri getir ve listele
    $.ajax({
        url: 'backend/etkinlikleri_getir.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            etkinlikleriListele(data);
        },
        error: function(xhr, status, error) {
            console.error("Etkinlikler getirilirken hata oluştu:", error);
            $('#etkinlik-listesi').html('<p>Etkinlikler yüklenirken bir hata oluştu.</p>');
        }
    });
    
     // Sepeti getir
    sepetiGetir();
    
    //Ödeme yap sayfası
    $(document).on('click','#odeme-yap-butonu',function(){
        $.ajax({
            url: 'backend/bileti_al.php',
            method: 'POST',
            dataType: 'json',
            data: { etkinlik_id: 1 }, // Sepetteki ilk etkinliğin ID'sini gönderiyoruz (basit bir örnek).
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // İsteğe bağlı: Başka bir sayfaya yönlendirme veya sepeti temizleme
                    window.location.href="index.html";
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Bilet alım hatası:", error);
                alert("Bilet alınırken bir hata oluştu.");
            }
        });
    })
});

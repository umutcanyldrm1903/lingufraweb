<?php

$commonSteps = [
    [
        'title' => 'Hedefini netlestir',
        'description' => 'Genel ingilizce, speaking, is ingilizcesi ya da sinav ihtiyacini belirle ve dersi buna gore sekillendir.',
    ],
    [
        'title' => 'Seviyeni olc',
        'description' => 'Kisa bir seviye tespiti ile hangi noktadan baslayacagini ve hangi becerilere agirlik verecegini gor.',
    ],
    [
        'title' => 'Egitmeni sec',
        'description' => 'Programina, saatlerine ve hedeflerine uygun egitmeni inceleyip birebir ders akisini planla.',
    ],
    [
        'title' => 'Duzenli ritim kur',
        'description' => 'Haftalik ders duzeni, odev takibi ve tekrarlarla kalici ilerleme sagla.',
    ],
];

return [
    'pages' => [
        'hub' => [
            'route' => 'english-private-lessons',
            'meta_title' => 'Ingilizce Ozel Ders | Online ve Birebir Ders Secenekleri',
            'meta_description' => 'Turkiye genelinde online ingilizce ozel ders, birebir ders planlari, speaking, is ingilizcesi ve sehir bazli programlari karsilastir.',
            'meta_keywords' => 'ingilizce ders, ingilizce ozel ders, online ingilizce ozel ders, birebir ingilizce dersi, ingilizce konusma dersi, is ingilizcesi ozel ders',
            'breadcrumb' => 'Ingilizce Ozel Ders',
            'eyebrow' => 'Ozel ders rotasi',
            'title' => 'Turkiye genelinde online ve birebir ingilizce ozel ders',
            'lead' => 'Genel ingilizce, speaking, is ingilizcesi ve sehir bazli programlar arasindan sana uygun ders tipini sec. Egitmenler, seviye tespiti ve kurumsal cozumler ayni akista bir araya gelsin.',
            'facts' => ['Online ders', 'Birebir plan', 'Speaking odagi', 'Sehir bazli secenekler'],
            'stats' => [
                ['value' => 'Canli', 'label' => 'Online birebir ders akisi'],
                ['value' => 'Speaking', 'label' => 'Akicilik ve ozguven odakli calisma'],
                ['value' => 'Is', 'label' => 'Toplanti ve sunum hazirligi'],
            ],
            'benefits_title' => 'Dogru ders tipini bulmak neden daha hizli ilerletir?',
            'benefits' => [
                [
                    'title' => 'Tek hedef yerine dogru hedef',
                    'description' => 'Ingilizce ders arayan herkes ayni ihtiyaca sahip degil. Speaking, genel ingilizce ve is hedefleri farkli plan ister.',
                ],
                [
                    'title' => 'Takvime uygun birebir plan',
                    'description' => 'Sabah, aksam ya da hafta sonu duzenine gore ilerleyen birebir akisla dersin kopmaz.',
                ],
                [
                    'title' => 'Sehirden bagimsiz esneklik',
                    'description' => 'Istanbul, Ankara ya da Izmir fark etmeksizin ders ritmini uzaktan ve duzenli sekilde surdurebilirsin.',
                ],
                [
                    'title' => 'Net sonraki adim',
                    'description' => 'Egitmen secimi, seviye tespiti ve iletisim adimlari ayni yol uzerinde ilerledigi icin karar vermek kolaylasir.',
                ],
            ],
            'focus_title' => 'Hangi ingilizce ozel ders sayfasina bakmalisin?',
            'tracks' => [
                [
                    'title' => 'Online Ingilizce Ozel Ders',
                    'description' => 'Evden ya da ofisten canli birebir ders yapmak isteyenler icin esnek program akisi.',
                    'route' => 'english-private-lessons.online',
                    'link_label' => 'Online ders detaylari',
                ],
                [
                    'title' => 'Ingilizce Konusma Dersi',
                    'description' => 'Akicilik, telaffuz ve gunluk pratik uzerine calismak isteyenler icin konusma odakli rota.',
                    'route' => 'english-private-lessons.speaking',
                    'link_label' => 'Konusma derslerini incele',
                ],
                [
                    'title' => 'Is Ingilizcesi Ozel Ders',
                    'description' => 'Toplanti, sunum, e-posta ve mulakat gibi profesyonel ihtiyaclara yonelik birebir dersler.',
                    'route' => 'english-private-lessons.business',
                    'link_label' => 'Is ingilizcesi sayfasina git',
                ],
                [
                    'title' => 'Sehir Bazli Rotalar',
                    'description' => 'Istanbul, Ankara ve Izmir icin farkli tempo ve hedeflere uygun ders planlarini karsilastir.',
                    'route' => 'english-private-lessons.istanbul',
                    'link_label' => 'Sehir sayfalarini ac',
                ],
            ],
            'steps' => $commonSteps,
            'faq' => [
                [
                    'question' => 'Ingilizce ozel ders ile kurs arasindaki fark nedir?',
                    'answer' => 'Ozel ders, programi senin hedeflerine ve hizina gore sekillendirir. Speaking, is ingilizcesi ya da sinav ihtiyacina gore icerik daha hizli ozellesir.',
                ],
                [
                    'question' => 'Online ingilizce ozel ders verimli olur mu?',
                    'answer' => 'Duzenli program, birebir geribildirim ve dogru materyal kullanimi ile online dersler oldukca verimli ilerler. Esnek saat secenegi de devamliligi artirir.',
                ],
                [
                    'question' => 'Hangi sayfadan baslamam daha dogru?',
                    'answer' => 'Genel bir karsilastirma istiyorsan bu sayfadan basla. Hedefin netse online, speaking, is ingilizcesi ya da sehir bazli sayfaya gecerek daha hizli karar verebilirsin.',
                ],
                [
                    'question' => 'Derse baslamadan once seviyemi olcmeli miyim?',
                    'answer' => 'Evet. Kisa bir seviye tespiti, gereksiz tekrarlarin onune gecer ve ilk dersten itibaren dogru odaga gecmeni saglar.',
                ],
            ],
            'primary_action' => [
                'route' => 'all-instructors',
                'label' => 'Egitmenleri incele',
            ],
            'secondary_actions' => [
                [
                    'route' => 'placement-test.show',
                    'label' => 'Seviyeni ogren',
                ],
                [
                    'route' => 'contact.index',
                    'label' => 'Danismanlik al',
                ],
            ],
            'related_pages' => ['online', 'speaking', 'business', 'istanbul', 'ankara', 'izmir'],
            'area_served' => 'Turkey',
        ],
        'online' => [
            'route' => 'english-private-lessons.online',
            'meta_title' => 'Online Ingilizce Ozel Ders | Esnek ve Canli Birebir Ders',
            'meta_description' => 'Online ingilizce ozel ders ile evden ya da ofisten canli birebir ders al, programini esnek saatlerle surdur.',
            'meta_keywords' => 'online ingilizce ozel ders, online ingilizce ders, canli birebir ingilizce dersi, uzaktan ingilizce ozel ders',
            'breadcrumb' => 'Online Ingilizce Ozel Ders',
            'eyebrow' => 'Online ders plani',
            'title' => 'Online ingilizce ozel ders ile duzenli ve esnek ilerle',
            'lead' => 'Evde, ofiste ya da seyahatteyken dersi aksatmadan surdurmek istiyorsan online birebir ders akisi en hizli secenektir. Saatlerini koru, geribildirimi aninda al, dersi kendi tempona gore yonet.',
            'facts' => ['Canli birebir ders', 'Esnek saat secimi', 'Tekrar dostu akis', 'Evden baglan'],
            'stats' => [
                ['value' => 'Esnek', 'label' => 'Calisma saatlerine uygun plan'],
                ['value' => 'Tek ekran', 'label' => 'Materyal ve not takibi kolay'],
                ['value' => 'Duzenli', 'label' => 'Haftalik ritmi korumak daha kolay'],
            ],
            'benefits_title' => 'Online birebir ders neden tercih ediliyor?',
            'benefits' => [
                [
                    'title' => 'Ulasim kaybi yok',
                    'description' => 'Yolda gecen zamani azaltir, derse ayni enerjiyle girmeni saglar.',
                ],
                [
                    'title' => 'Ders notlari tek yerde',
                    'description' => 'Paylasilan ekran, dokuman ve odev akisi ayni ortamda toplandigi icin takip kolaylasir.',
                ],
                [
                    'title' => 'Aksam ve hafta sonu ritmi',
                    'description' => 'Yogun calisanlar icin mesai sonrasi ya da hafta sonu duzeni daha rahat kurulur.',
                ],
                [
                    'title' => 'Birebir geribildirim',
                    'description' => 'Eksik kaldigin noktalara aninda donulerek ders verimi yukari cekilir.',
                ],
            ],
            'focus_title' => 'Online ders akisinda hangi basliklar one cikiyor?',
            'tracks' => [
                [
                    'title' => 'Genel Ingilizce',
                    'description' => 'Dil bilgisi, kelime bilgisi ve gundelik kullanim becerilerini birlikte toparla.',
                    'route' => 'english-private-lessons',
                    'link_label' => 'Genel rota',
                ],
                [
                    'title' => 'Speaking Calismalari',
                    'description' => 'Online ortamda role-play ve anlik konusma egzersizleriyle akiciligi hizlandir.',
                    'route' => 'english-private-lessons.speaking',
                    'link_label' => 'Speaking rotasi',
                ],
                [
                    'title' => 'Is Odakli Dersler',
                    'description' => 'Toplanti, sunum ve e-posta dili gibi profesyonel ihtiyaclari ayri bloklarla calis.',
                    'route' => 'english-private-lessons.business',
                    'link_label' => 'Is ingilizcesi',
                ],
                [
                    'title' => 'Seviye Tespiti',
                    'description' => 'Dersi gereksiz tekrarlarla uzatmadan dogru seviyeden baslat.',
                    'route' => 'placement-test.show',
                    'link_label' => 'Teste basla',
                ],
            ],
            'steps' => $commonSteps,
            'faq' => [
                [
                    'question' => 'Online ingilizce ozel ders icin her seferinde ayni egitmenle mi ilerlenir?',
                    'answer' => 'Duzenli ilerleme icin ayni egitmenle devam etmek genelde daha sagliklidir. Boylece hedefler, eksikler ve tempo daha net takip edilir.',
                ],
                [
                    'question' => 'Online derslerde speaking gelisir mi?',
                    'answer' => 'Evet. Canli konusma pratikleri, role-play calismalari ve duzenli tekrar ile speaking odagi online derslerde de guclu sekilde ilerler.',
                ],
                [
                    'question' => 'Hangi ogrenciler online dersi daha rahat surdurur?',
                    'answer' => 'Calisan profesyoneller, universite ogrencileri ve sehir ici ulasim kaybi yasamak istemeyen kisiler online akistan daha cok fayda gorur.',
                ],
            ],
            'primary_action' => [
                'route' => 'all-instructors',
                'label' => 'Online ders icin egitmen bul',
            ],
            'secondary_actions' => [
                [
                    'route' => 'placement-test.show',
                    'label' => 'Seviyeni olc',
                ],
                [
                    'route' => 'contact.index',
                    'label' => 'Program sor',
                ],
            ],
            'related_pages' => ['hub', 'speaking', 'business', 'istanbul'],
            'area_served' => 'Turkey',
        ],
        'speaking' => [
            'route' => 'english-private-lessons.speaking',
            'meta_title' => 'Ingilizce Konusma Dersi | Akicilik ve Speaking Odakli Ozel Ders',
            'meta_description' => 'Ingilizce konusma dersi ile speaking, telaffuz ve ozguven becerilerini canli birebir derslerle guclendir.',
            'meta_keywords' => 'ingilizce konusma dersi, speaking dersi, speaking odakli ingilizce ozel ders, akici ingilizce konusma',
            'breadcrumb' => 'Ingilizce Konusma Dersi',
            'eyebrow' => 'Speaking odagi',
            'title' => 'Ingilizce konusma dersi ile daha akici ve rahat konus',
            'lead' => 'Kelime biliyor ama konusurken duruyorsan sorun genelde pratik eksigidir. Speaking odakli ozel ders; akicilik, telaffuz ve ozguven tarafini ayni anda gelistirir.',
            'facts' => ['Speaking pratigi', 'Telaffuz geri bildirimi', 'Rol canlandirma', 'Ozguven artisi'],
            'stats' => [
                ['value' => 'Akici', 'label' => 'Daha uzun ve rahat cevaplar'],
                ['value' => 'Net', 'label' => 'Telaffuz uzerine dogrudan geri bildirim'],
                ['value' => 'Gercekci', 'label' => 'Gunluk senaryolarla pratik'],
            ],
            'benefits_title' => 'Speaking dersinde neler degisir?',
            'benefits' => [
                [
                    'title' => 'Susma esigi duser',
                    'description' => 'Hazir cevap beklemek yerine anlik konusma kurmayi ogrenirsin.',
                ],
                [
                    'title' => 'Daha dogal cumle akisi',
                    'description' => 'Ezber yerine kaliplar ve gercek kullanim uzerinden konusma kolaylasir.',
                ],
                [
                    'title' => 'Telaffuz daha anlasilir olur',
                    'description' => 'Sadece dogru kelimeyi bilmek degil, anlasilir sekilde soylemek de calisilir.',
                ],
                [
                    'title' => 'Gerginlik azalir',
                    'description' => 'Surekli pratik ve birebir ortam, hata yapma korkusunu azaltir.',
                ],
            ],
            'focus_title' => 'Speaking odakli derslerde hangi alanlar one cikar?',
            'tracks' => [
                [
                    'title' => 'Gunluk Konusma',
                    'description' => 'Tanisma, soru sorma, fikir belirtme ve anlik tepki verme becerilerini toparla.',
                    'route' => 'all-instructors',
                    'link_label' => 'Speaking egitmenleri',
                ],
                [
                    'title' => 'Mulakat Hazirligi',
                    'description' => 'Is gorusmeleri ve kendini anlatma pratikleri ile daha net cevaplar ver.',
                    'route' => 'english-private-lessons.business',
                    'link_label' => 'Is odakli sayfa',
                ],
                [
                    'title' => 'Sunum ve Toplanti',
                    'description' => 'Kendini tanitma, proje anlatma ve soru cevap bolumlerinde daha rahat ilerle.',
                    'route' => 'english-private-lessons.business',
                    'link_label' => 'Profesyonel speaking',
                ],
                [
                    'title' => 'Online Konusma Rutini',
                    'description' => 'Evden duzenli speaking pratigi yapmak isteyenler icin online akisi kur.',
                    'route' => 'english-private-lessons.online',
                    'link_label' => 'Online speaking',
                ],
            ],
            'steps' => $commonSteps,
            'faq' => [
                [
                    'question' => 'Ingilizce konusma dersi baslangic seviyesinde alinabilir mi?',
                    'answer' => 'Evet. Speaking calismasi sadece ileri seviyeler icin degildir. Baslangic seviyesinde de kucuk ama duzenli pratiklerle konusma cesareti hizla artar.',
                ],
                [
                    'question' => 'Speaking icin dil bilgisi tam olmadan baslanir mi?',
                    'answer' => 'Baslanir. Speaking dersinde gerekli dil bilgisi ihtiyaca gore dersin icine yerlestirilir. Tum gramerin bitmesini beklemek gerekmez.',
                ],
                [
                    'question' => 'Sadece speaking mi, yoksa genel ingilizce ile birlikte mi ilerlemeliyim?',
                    'answer' => 'Hedefine gore degisir. Konusurken tikanma yasiyorsan speaking agirlikli plan daha hizli sonuc verir; altyapi eksigi buyukse genel ingilizce ile birlikte ilerlemek daha dogrudur.',
                ],
            ],
            'primary_action' => [
                'route' => 'all-instructors',
                'params' => ['tag' => 'category_speaking'],
                'label' => 'Speaking egitmenlerini gore',
            ],
            'secondary_actions' => [
                [
                    'route' => 'placement-test.show',
                    'label' => 'Speaking icin seviyeni olc',
                ],
                [
                    'route' => 'contact.index',
                    'label' => 'Konusma programi sor',
                ],
            ],
            'related_pages' => ['hub', 'online', 'business', 'izmir'],
            'area_served' => 'Turkey',
        ],
        'business' => [
            'route' => 'english-private-lessons.business',
            'meta_title' => 'Is Ingilizcesi Ozel Ders | Toplanti, Sunum ve Mail Odakli Calisma',
            'meta_description' => 'Is ingilizcesi ozel ders ile toplanti, sunum, e-posta ve mulakat becerilerini birebir canli derslerle guclendir.',
            'meta_keywords' => 'is ingilizcesi ozel ders, business english private lesson, profesyonel ingilizce dersi, toplanti ingilizcesi',
            'breadcrumb' => 'Is Ingilizcesi Ozel Ders',
            'eyebrow' => 'Profesyonel hedef',
            'title' => 'Is ingilizcesi ozel ders ile profesyonel iletisimini guclendir',
            'lead' => 'Toplantiya katilmak, mail yazmak, sunum yapmak ya da mulakatta kendini net ifade etmek istiyorsan is ingilizcesi odakli birebir ders plani gerekir.',
            'facts' => ['Toplanti dili', 'Sunum hazirligi', 'Mail yazimi', 'Mulakat pratigi'],
            'stats' => [
                ['value' => 'Toplanti', 'label' => 'Daha rahat katilim ve yanit'],
                ['value' => 'Sunum', 'label' => 'Kendini daha net ifade etme'],
                ['value' => 'Mail', 'label' => 'Daha dogru profesyonel yazisma'],
            ],
            'benefits_title' => 'Is odakli dersler hangi alanlarda fayda saglar?',
            'benefits' => [
                [
                    'title' => 'Gercek is senaryolari',
                    'description' => 'Rapor sunma, soru cevaplama, brief verme ve e-posta akislari dersin icine dahil edilir.',
                ],
                [
                    'title' => 'Sektorune uygun dil',
                    'description' => 'Genel kaliplar yerine kullandigin is diline yakin cumle yapilari ve kelimeler calisilir.',
                ],
                [
                    'title' => 'Mulakat hazirligi',
                    'description' => 'Ozgecmis anlatimi, deneyim aktarma ve teknik olmayan soru cevap bolumleri daha rahat hale gelir.',
                ],
                [
                    'title' => 'Kurumsal duzenle uyum',
                    'description' => 'Mesai saatleri, toplanti trafigi ve kisa ama etkili ders bloklariyla plan surdurulebilir.',
                ],
            ],
            'focus_title' => 'Is ingilizcesi derslerinde hangi moduller one cikar?',
            'tracks' => [
                [
                    'title' => 'Toplanti ve Sunum',
                    'description' => 'Fikir sunma, itiraz karsilama, soru cevaplama ve toplantida net konusma becerileri.',
                    'route' => 'all-instructors',
                    'link_label' => 'Is odakli egitmenler',
                ],
                [
                    'title' => 'E-posta ve Yazili Iletisim',
                    'description' => 'Daha profesyonel ve dogal yazismalar icin kullanilan kaliplari oturt.',
                    'route' => 'contact.index',
                    'link_label' => 'Programi sor',
                ],
                [
                    'title' => 'Mulakat ve Kariyer',
                    'description' => 'Is gorusmeleri ve terfi sureclerinde kendini daha guvenli ifade etmeyi calis.',
                    'route' => 'english-private-lessons.speaking',
                    'link_label' => 'Speaking destegi',
                ],
                [
                    'title' => 'Kurumsal Ekipler',
                    'description' => 'Bireysel ihtiyaclarin yaninda ekip bazli egitim plani dusunenler icin kurumsal akisa gec.',
                    'route' => 'corporate.index',
                    'link_label' => 'Kurumsal sayfa',
                ],
            ],
            'steps' => $commonSteps,
            'faq' => [
                [
                    'question' => 'Is ingilizcesi ozel ders ile genel ingilizce ayni sey midir?',
                    'answer' => 'Degildir. Genel ingilizce daha genis bir altyapi sunar; is ingilizcesi ise toplanti, sunum, mail ve mulakat gibi profesyonel alanlara odaklanir.',
                ],
                [
                    'question' => 'Sifirdan is ingilizcesi calisilir mi?',
                    'answer' => 'Baslangic seviyesinde once temel altyapi kurulur, sonra profesyonel senaryolar eklenir. Seviye tespiti bu dengeyi belirlemek icin onemlidir.',
                ],
                [
                    'question' => 'Kurumsal egitim ile bireysel is ingilizcesi dersi nasil ayrilir?',
                    'answer' => 'Bireysel dersler kisinin gorev ve hedeflerine odaklanir. Kurumsal egitim ise ekiplerin ortak ihtiyaclarini, seviye dagilimini ve kurum icindeki kullanim alanlarini kapsar.',
                ],
            ],
            'primary_action' => [
                'route' => 'all-instructors',
                'params' => ['tag' => 'category_business'],
                'label' => 'Is odakli egitmenleri gore',
            ],
            'secondary_actions' => [
                [
                    'route' => 'corporate.index',
                    'label' => 'Kurumsal egitime gec',
                ],
                [
                    'route' => 'contact.index',
                    'label' => 'Program detayini sor',
                ],
            ],
            'related_pages' => ['hub', 'online', 'speaking', 'ankara'],
            'area_served' => 'Turkey',
        ],
        'istanbul' => [
            'route' => 'english-private-lessons.istanbul',
            'meta_title' => 'Istanbul Ingilizce Ozel Ders | Yogun Takvime Uygun Birebir Plan',
            'meta_description' => 'Istanbul ingilizce ozel ders arayanlar icin yogun is, okul ve ulasim temposuna uygun online birebir ders plani.',
            'meta_keywords' => 'istanbul ingilizce ozel ders, istanbul online ingilizce dersi, istanbul birebir ingilizce',
            'breadcrumb' => 'Istanbul Ingilizce Ozel Ders',
            'eyebrow' => 'Sehir bazli rota',
            'title' => 'Istanbul icin yogun tempoya uygun ingilizce ozel ders',
            'lead' => 'Istanbul gibi hizli bir sehirde dersin surmesi icin esnek saat, net hedef ve ulasim kaybi yaratmayan bir plan gerekir. Online birebir akis bu nedenle en rahat surdurulen modellerden biridir.',
            'facts' => ['Mesai sonrasi ders', 'Yogun takvime uyum', 'Ulasim kaybi yok', 'Ders ritmi korunur'],
            'stats' => [
                ['value' => 'Aksam', 'label' => 'Mesai sonrasi uygun akis'],
                ['value' => 'Hafta sonu', 'label' => 'Programi bozmadan ders'],
                ['value' => 'Birebir', 'label' => 'Sehir temposuna gore esnek plan'],
            ],
            'benefits_title' => 'Istanbul temponda ders surdurmenin kilit noktalari',
            'benefits' => [
                [
                    'title' => 'Ulasim yerine derse odak',
                    'description' => 'Sehir ici yol ve trafik yuku olmadan dersi zamaninda baslatmak daha kolay olur.',
                ],
                [
                    'title' => 'Calisanlar icin esnek bloklar',
                    'description' => 'Mesai cikisi ya da hafta sonu icin kisa ama duzenli ders ritmi kurulabilir.',
                ],
                [
                    'title' => 'Universite ve is dengesine uygun',
                    'description' => 'Ders saatlerini okul, staj ya da is temposuna gore daha rahat planlayabilirsin.',
                ],
                [
                    'title' => 'Speaking ve is odaklari kolay birlesir',
                    'description' => 'Speaking ve profesyonel iletisim ihtiyaclari ayni planda ele alinabilir.',
                ],
            ],
            'focus_title' => 'Istanbul sayfasinda hangi rotalar one cikiyor?',
            'tracks' => [
                [
                    'title' => 'Mesai Sonrasi Online Ders',
                    'description' => 'Aksam saatlerinde duzenli ilerlemek isteyen profesyoneller icin en pratik model.',
                    'route' => 'english-private-lessons.online',
                    'link_label' => 'Online rota',
                ],
                [
                    'title' => 'Speaking ve Ozguven',
                    'description' => 'Sosyal ve profesyonel hayatta daha rahat konusmak isteyenler icin speaking odagi.',
                    'route' => 'english-private-lessons.speaking',
                    'link_label' => 'Speaking sayfasi',
                ],
                [
                    'title' => 'Is Ingilizcesi',
                    'description' => 'Toplanti, sunum ve yabanci musteri iletisimleri icin profesyonel akis.',
                    'route' => 'english-private-lessons.business',
                    'link_label' => 'Is odakli plan',
                ],
                [
                    'title' => 'Seviyeni Olc',
                    'description' => 'Yogun tempo icinde zamani dogru kullanmak icin dogru seviyeden basla.',
                    'route' => 'placement-test.show',
                    'link_label' => 'Seviye tespiti',
                ],
            ],
            'steps' => $commonSteps,
            'faq' => [
                [
                    'question' => 'Istanbul icin en uygun ders saatleri hangileri olur?',
                    'answer' => 'Genelde mesai sonrasi ve hafta sonu bloklari daha rahat surdurulur. En iyi saat secimi gunluk rutinine ve enerji seviyene gore belirlenmelidir.',
                ],
                [
                    'question' => 'Istanbul ingilizce ozel ders sayfasindaki programlar yuz yuze mi?',
                    'answer' => 'Bu rota ozellikle online ve birebir ders akisini on plana cikarir. Boylece sehir ici ulasim kaybi olmadan ders duzeni korunur.',
                ],
                [
                    'question' => 'Istanbul icin speaking mi, genel ingilizce mi daha cok tercih edilir?',
                    'answer' => 'Cogu kullanici speaking ve is odakli planlara yonelir; ancak temel altyapi eksigi varsa genel ingilizce ile beraber ilerlemek daha dogru olur.',
                ],
            ],
            'primary_action' => [
                'route' => 'all-instructors',
                'label' => 'Istanbul temposuna uygun egitmen bul',
            ],
            'secondary_actions' => [
                [
                    'route' => 'placement-test.show',
                    'label' => 'Programa seviyeni ekle',
                ],
                [
                    'route' => 'contact.index',
                    'label' => 'Saat planini sor',
                ],
            ],
            'related_pages' => ['hub', 'online', 'speaking', 'ankara', 'izmir'],
            'area_served' => 'Istanbul, Turkey',
        ],
        'ankara' => [
            'route' => 'english-private-lessons.ankara',
            'meta_title' => 'Ankara Ingilizce Ozel Ders | Duzenli ve Hedef Odakli Ders Plani',
            'meta_description' => 'Ankara ingilizce ozel ders arayanlar icin duzenli calisma, sinav hazirligi ve profesyonel hedeflere uygun birebir plan.',
            'meta_keywords' => 'ankara ingilizce ozel ders, ankara online ingilizce dersi, ankara birebir ingilizce',
            'breadcrumb' => 'Ankara Ingilizce Ozel Ders',
            'eyebrow' => 'Sehir bazli rota',
            'title' => 'Ankara icin duzenli ve hedef odakli ingilizce ozel ders',
            'lead' => 'Ankarada ingilizce ders ihtiyaci cogu zaman duzenli calisma, sinav hazirligi ya da profesyonel gelisim hedefleriyle gelir. Birebir ve planli akis bu hedeflerde daha net sonuc verir.',
            'facts' => ['Duzenli calisma', 'Sinav hedefi', 'Profesyonel gelisim', 'Program disiplini'],
            'stats' => [
                ['value' => 'Planli', 'label' => 'Haftalik takip duzeni'],
                ['value' => 'Net hedef', 'label' => 'Sinav ya da kariyer odagi'],
                ['value' => 'Birebir', 'label' => 'Eksige gore ilerleme'],
            ],
            'benefits_title' => 'Ankara rotasinda hangi avantajlar one cikar?',
            'benefits' => [
                [
                    'title' => 'Takibi kolay ders ritmi',
                    'description' => 'Daha duzenli program kurmak isteyenler icin haftalik hedefleri surdurmek daha kolay olur.',
                ],
                [
                    'title' => 'Akademik ve profesyonel denge',
                    'description' => 'Universite, kamu ya da ozel sektor hedefleri icin farkli moduller ayni planda birlestirilebilir.',
                ],
                [
                    'title' => 'Sinav odakli ilerleme',
                    'description' => 'YDS, IELTS ya da TOEFL gibi hedefler icin altyapi ve uygulama dersleri ayri ayri duzenlenebilir.',
                ],
                [
                    'title' => 'Net geri bildirim',
                    'description' => 'Birebir duzende hangi alanda eksik oldugun daha erken gorulur ve program hizla duzeltilir.',
                ],
            ],
            'focus_title' => 'Ankara sayfasinda hangi planlar one cikiyor?',
            'tracks' => [
                [
                    'title' => 'Sinav Hazirligi',
                    'description' => 'YDS, IELTS ve TOEFL gibi hedefler icin duzenli birebir destek.',
                    'route' => 'placement-test.show',
                    'link_label' => 'Seviyeni gor',
                ],
                [
                    'title' => 'Is Ingilizcesi',
                    'description' => 'Kurumsal yazisma, toplanti ve sunum dili icin birebir profesyonel rota.',
                    'route' => 'english-private-lessons.business',
                    'link_label' => 'Profesyonel rota',
                ],
                [
                    'title' => 'Online Ozel Ders',
                    'description' => 'Evden duzenli ve disiplinli bicimde ilerlemek isteyenler icin pratik akis.',
                    'route' => 'english-private-lessons.online',
                    'link_label' => 'Online ders sayfasi',
                ],
                [
                    'title' => 'Speaking Takviyesi',
                    'description' => 'Temel altyapiya ek olarak akici konusma tarafini ayrica guclendir.',
                    'route' => 'english-private-lessons.speaking',
                    'link_label' => 'Speaking sayfasi',
                ],
            ],
            'steps' => $commonSteps,
            'faq' => [
                [
                    'question' => 'Ankara ingilizce ozel ders arayan biri once neye bakmali?',
                    'answer' => 'Ilk olarak hedefini netlestirmen gerekir: sinav, speaking, genel gelisim ya da is ingilizcesi. Sonra uygun egitmen ve ders ritmi secilir.',
                ],
                [
                    'question' => 'Ankara icin online ders yeterli olur mu?',
                    'answer' => 'Evet. Duzenli takip ve birebir geribildirim ile online dersler Ankara icin oldukca verimli ilerler.',
                ],
                [
                    'question' => 'Sinav odakli ilerlerken speaking de calisilabilir mi?',
                    'answer' => 'Calisilabilir. Ancak ana hedef sinavsa once temel eksikler ve soru tipleri toparlanir, speaking ise destek modulu olarak eklenir.',
                ],
            ],
            'primary_action' => [
                'route' => 'all-instructors',
                'label' => 'Ankara hedeflerine uygun egitmen bul',
            ],
            'secondary_actions' => [
                [
                    'route' => 'placement-test.show',
                    'label' => 'Seviye tespiti yap',
                ],
                [
                    'route' => 'contact.index',
                    'label' => 'Ders planini sor',
                ],
            ],
            'related_pages' => ['hub', 'business', 'online', 'istanbul', 'izmir'],
            'area_served' => 'Ankara, Turkey',
        ],
        'izmir' => [
            'route' => 'english-private-lessons.izmir',
            'meta_title' => 'Izmir Ingilizce Ozel Ders | Speaking ve Profesyonel Hedefler Icin Plan',
            'meta_description' => 'Izmir ingilizce ozel ders arayanlar icin speaking, profesyonel iletisim ve esnek saatli online birebir ders plani.',
            'meta_keywords' => 'izmir ingilizce ozel ders, izmir online ingilizce dersi, izmir birebir ingilizce',
            'breadcrumb' => 'Izmir Ingilizce Ozel Ders',
            'eyebrow' => 'Sehir bazli rota',
            'title' => 'Izmir icin speaking ve profesyonel hedeflere uygun ingilizce ozel ders',
            'lead' => 'Izmirde ingilizce ozel ders arayan kullanicilar genelde daha rahat ama duzenli bir program, speaking gucu ve profesyonel kullanim tarafini bir arada istiyor. Online birebir rota bunu esnek sekilde tasir.',
            'facts' => ['Speaking gucu', 'Esnek saatler', 'Profesyonel iletisim', 'Online birebir akis'],
            'stats' => [
                ['value' => 'Speaking', 'label' => 'Gunluk ve akici konusma odagi'],
                ['value' => 'Esnek', 'label' => 'Yasama uygun ders planlama'],
                ['value' => 'Profesyonel', 'label' => 'Is ve musteri iletisim destegi'],
            ],
            'benefits_title' => 'Izmir rotasinda hangi basliklar one cikiyor?',
            'benefits' => [
                [
                    'title' => 'Speaking agirlikli ilerleme',
                    'description' => 'Gunluk konusma ve sosyal iletisim tarafini hizla canlandirmak isteyenler icin birebir speaking planlari daha verimli olur.',
                ],
                [
                    'title' => 'Esnek ama kopmayan program',
                    'description' => 'Ders saatlerini rahat kurarken haftalik ritmi kaybetmemek kolaylasir.',
                ],
                [
                    'title' => 'Turizm ve ihracat diline yakin kullanim',
                    'description' => 'Musteri iletisimleri, tanitim ve profesyonel yazisma tarafinda daha net bir dil kullanimi gelisir.',
                ],
                [
                    'title' => 'Online birebir pratik',
                    'description' => 'Konusma agirlikli dersler icin duzenli canli seanslar sureklilik saglar.',
                ],
            ],
            'focus_title' => 'Izmir sayfasinda hangi ders tipleri one cikiyor?',
            'tracks' => [
                [
                    'title' => 'Speaking Ozel Ders',
                    'description' => 'Akicilik ve gundelik konusma becerisini one alan birebir akis.',
                    'route' => 'english-private-lessons.speaking',
                    'link_label' => 'Speaking rotasi',
                ],
                [
                    'title' => 'Online Ingilizce Ozel Ders',
                    'description' => 'Evden ya da ofisten rahatca surdurulebilen canli ders plani.',
                    'route' => 'english-private-lessons.online',
                    'link_label' => 'Online rota',
                ],
                [
                    'title' => 'Is ve Musteri Iletisimi',
                    'description' => 'Profesyonel konusma, mail ve sunum tarafini toparlamak isteyenler icin uygun rota.',
                    'route' => 'english-private-lessons.business',
                    'link_label' => 'Is odakli rota',
                ],
                [
                    'title' => 'Genel Ozel Ders Rotasi',
                    'description' => 'Tum ozel ders seceneklerini ayni yerde karsilastirmak isteyenler icin merkez sayfa.',
                    'route' => 'english-private-lessons',
                    'link_label' => 'Merkez sayfa',
                ],
            ],
            'steps' => $commonSteps,
            'faq' => [
                [
                    'question' => 'Izmir icin speaking mi yoksa genel ingilizce mi daha dogru?',
                    'answer' => 'Eger amacin daha rahat konusmak ve gunluk iletisimde duraksamayi azaltmaksa speaking odakli rota daha hizli sonuc verir. Temel altyapi eksiginde genel plan da eklenebilir.',
                ],
                [
                    'question' => 'Izmirde online birebir ders yeterince duzenli surer mi?',
                    'answer' => 'Evet. Ders saati sabitlenip haftalik hedefler netlestiginde online birebir dersler cok duzenli sekilde ilerler.',
                ],
                [
                    'question' => 'Profesyonel kullanim ile speaking ayni planda calisilabilir mi?',
                    'answer' => 'Calisilabilir. Ozellikle musteri iletisimleri ve toplantilar icin speaking ile is ingilizcesi modulleri bir arada planlanabilir.',
                ],
            ],
            'primary_action' => [
                'route' => 'all-instructors',
                'label' => 'Izmir icin uygun egitmeni bul',
            ],
            'secondary_actions' => [
                [
                    'route' => 'placement-test.show',
                    'label' => 'Seviyeni ogren',
                ],
                [
                    'route' => 'contact.index',
                    'label' => 'Programi sor',
                ],
            ],
            'related_pages' => ['hub', 'speaking', 'online', 'istanbul', 'ankara'],
            'area_served' => 'Izmir, Turkey',
        ],
    ],
];

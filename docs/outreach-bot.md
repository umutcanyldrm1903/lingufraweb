# Outreach Bot

Bu kurulum, Lusha + OpenAI + mevcut Laravel mail altyapisini kullanarak kontrollu B2B outreach akisi kurar.

## Ne Kuruldu

- `outreach_campaigns`, `outreach_leads`, `outreach_messages`, `outreach_suppressions` tablolari
- Lusha istemcisi: `app/Services/Outreach/LushaClient.php`
- GPT mesaj uretimi: `app/Services/Outreach/OutreachAiComposer.php`
- Gonderim ve suppression mantigi: `app/Services/Outreach/OutreachAutomationService.php`
- SMTP gonderimi icin mailable: `app/Mail/OutreachMail.php`
- IMAP reply sync servisi: `app/Services/Outreach/ImapReplySyncService.php`
- Komutlar:
  - `php artisan outreach:create-campaign`
  - `php artisan outreach:import-lusha`
  - `php artisan outreach:enrich-lusha`
  - `php artisan outreach:generate`
  - `php artisan outreach:approve`
  - `php artisan outreach:send`
  - `php artisan outreach:sync-replies`

## Admin Panel

Migration tamamlandiktan sonra admin panelde sol menude `Outreach Bot` gorunur.

- Kampanya olusturma ve duzenleme
- Lusha search payload JSON yapistirarak lead import etme
- Eksik emailleri enrich etme
- GPT ile draft uretme
- Draftlari tek tek veya toplu onaylama
- Mesajlari duzenleme ve gonderme
- IMAP reply sync tetikleme
- `Settings -> Crediential Setting -> Outreach Providers` alanindan OpenAI, Lusha ve IMAP bilgilerini yonetme

## Gerekli Ayarlar

Istersen `.env`, istersen admin paneldeki `Outreach Providers` sekmesinden su alanlari doldur:

```env
OPENAI_API_KEY=
OPENAI_BASE_URL=https://api.openai.com/v1
OPENAI_OUTREACH_MODEL=gpt-5-mini

LUSHA_API_KEY=
LUSHA_BASE_URL=https://api.lusha.com
LUSHA_SEARCH_PATH=/prospecting/contact/search
LUSHA_ENRICH_PATH=/prospecting/contact/enrich

OUTREACH_IMAP_HOST=
OUTREACH_IMAP_PORT=993
OUTREACH_IMAP_ENCRYPTION=ssl
OUTREACH_IMAP_USERNAME=
OUTREACH_IMAP_PASSWORD=
OUTREACH_IMAP_MAILBOX=INBOX
```

SMTP bu projede mevcut global email ayarlari ile gonderiliyor. Admin panelindeki mail ayarlarinda kurumsal mailbox bilgilerini girmen gerekir.

## Onemli Notlar

- Bu kurulum `php_imap` varsa native IMAP kullanir. `php_imap` yoksa socket tabanli IMAP fallback kullanir.
- `php_imap` olmasa bile hosting tarafinin disari `IMAPS` veya `STARTTLS` baglantilarina izin vermesi gerekir.
- `pdo_mysql` yoksa Laravel MySQL veritabani baglanamaz. Bu kisim uygulama icinden degistirilemez; hosting PHP paketinde acik olmasi gerekir.
- Sistem otomatik spam bypass mantigi kurmaz. Rate limit, send window, approval ve unsubscribe mantigi ile daha saglikli bir akis kurar.
- Varsayilan akista mesajlar once uretilir, sonra onaylanir, sonra gonderilir.

## Ornek Akis

1. Migrationlari calistir:

```bash
php artisan migrate
```

2. Kampanya olustur:

```bash
php artisan outreach:create-campaign "Lusha Demo" --company-name="Sirketiniz" --product-name="Urununuz" --offer-summary="Ekipler icin kisa demo ve teklif gorusmesi" --audience-summary="Dil egitimi veya kurumsal gelisim tarafinda karar vericiler" --language=tr
```

3. Lusha search payload JSON hazirla. Bu payload'i Lusha OpenAPI dokumanindaki arama body yapisina gore gonder:

```bash
php artisan outreach:import-lusha 1 --file=storage/app/lusha-search.json
```

4. Email bilgilerini enrich et:

```bash
php artisan outreach:enrich-lusha 1 --limit=25
```

5. GPT ile taslaklari uret:

```bash
php artisan outreach:generate 1 --limit=20
```

6. Taslaklari onayla:

```bash
php artisan outreach:approve 1 --limit=20
```

7. Dry run ile gonderilecekleri kontrol et:

```bash
php artisan outreach:send 1 --limit=10 --dry-run
```

8. Gonder:

```bash
php artisan outreach:send 1 --limit=10
```

9. Cevaplari IMAP ile cek:

```bash
php artisan outreach:sync-replies 1
```

## Tavsiye Edilen Sonraki Adimlar

- Bounce webhook veya mailbox parser eklemek
- DKIM/SPF/DMARC dogrulamasini tamamlamak
- Reply sync cron veya scheduler'a baglamak

# 📦 Koyabu Telegram API Client (PHP)

`koyabu/telegramapi` adalah library PHP sederhana dan fleksibel untuk berkomunikasi dengan **Telegram Bot API**, mendukung pengiriman pesan, media, webhook, update, dan berbagai fitur Telegram lainnya.

Library ini dibangun menggunakan **GuzzleHttp** sebagai HTTP client.

---

## 🚀 Fitur Utama

- Mengirim pesan text
- Mengirim photo, video, audio, document, voice
- Mengirim album (media group)
- Edit message
- Delete message
- Mendapatkan update (long polling)
- Mengatur webhook (setWebhook / deleteWebhook)
- Mendapatkan file & membuat URL unduhan
- Mendapatkan informasi bot (`getMe`)
- Mengirim _typing action_ (sendChatAction)

---

## 📥 Instalasi

Install via Composer:

```bash
composer require koyabu/telegramapi
```

Atau tambahkan pada `composer.json`:

```json
{
  "require": {
    "koyabu/telegramapi": "dev-master"
  }
}
```

---

## 🛠 Cara Menggunakan

### 1. Inisiasi Class

```php
use Koyabu\TelegramAPI\Telegram;

$bot = new Telegram([
    'token'   => 'YOUR_TELEGRAM_BOT_TOKEN',
    'botname' => 'MyAwesomeBot'
]);
```

---

## 📤 Mengirim Pesan

### 1. Kirim Pesan Text

```php
$bot->sendMessage(123456789, "Halo dunia!");
```

### 2. Kirim Photo

```php
$bot->sendPhoto(123456789, './image.jpg', 'Ini contoh foto');
```

### 3. Kirim Document

```php
$bot->sendDocument(123456789, './test.pdf', 'Berikut file PDF');
```

### 4. Kirim Video

```php
$bot->sendVideo(123456789, './video.mp4', 'Video contoh');
```

### 5. Kirim Audio

```php
$bot->sendAudio(123456789, './audio.mp3');
```

### 6. Kirim Voice Message

```php
$bot->sendVoice(123456789, './voice.ogg');
```

### 7. Kirim Media Group (Album)

```php
$bot->sendMediaGroup(123456789, [
    ['type' => 'photo', 'file' => './1.jpg', 'caption' => 'Foto 1'],
    ['type' => 'photo', 'file' => './2.jpg'],
    ['type' => 'document', 'file' => './file.pdf']
]);
```

---

## ✏ Edit & Delete Message

### Edit Message

```php
$bot->editMessageText(123456789, 45, "Teks diganti!");
```

### Hapus Message

```php
$bot->deleteMessage(123456789, 45);
```

---

## 🔄 Mendapatkan Update (Long Polling)

```php
$updates = $bot->getUpdates();
print_r($updates);
```

Jika ingin polling dari offset tertentu:

```php
$bot->getUpdates($start = 50);
```

---

## 🌐 Webhook

### Set Webhook

```php
$bot->setWebhook("https://example.com/webhook-handler.php");
```

### Delete Webhook

```php
$bot->deleteWebhook();
```

### Get Webhook Info

```php
$info = $bot->getWebhookInfo();
print_r($info);
```

---

## 📁 Mendapatkan File

### 1. Ambil informasi file

```php
$file = $bot->getFile($file_id);
$file_path = $file['result']['file_path'];
```

### 2. Buat URL download file

```php
$url = $bot->buildFileUrl($file_path);
echo $url;
```

---

## 🧪 Contoh Webhook Handler (PHP)

```php
require 'vendor/autoload.php';

use Koyabu\TelegramAPI\Telegram;

$bot = new Telegram([
    'token' => 'YOUR_BOT_TOKEN',
]);

$data = json_decode(file_get_contents('php://input'), true);

$chat_id = $data['message']['chat']['id'];
$text    = $data['message']['text'];

$bot->sendMessage($chat_id, "Anda berkata: $text");
```

---

## 📂 Struktur Folder Direkomendasikan

```
project/
│── src/
│   └── Telegram.php
│── public/
│   └── webhook.php
│── composer.json
│── README.md
```

---

## 💬 Dukungan & Kontribusi

Pull Request dipersilakan.
Jika ingin menambah fitur Telegram lainnya, tinggalkan issue.

---

## 📄 Lisensi

MIT License — bebas digunakan untuk kebutuhan personal & komersial.

# 🌌 SilentGalaxy Base-Bot (Scalable PHP Telegram Bot)

یک معماری فوق‌العاده تمیز، مقیاس‌پذیر و ماژولار برای ساخت ربات‌های تلگرامی با PHP و MySQL.
این سورس بر اساس الگوی **State Machine (ماشین وضعیت)** و **Front Controller** طراحی شده است.

## ✨ ویژگی‌های برجسته
- **رابط کاربری شبیه اپلیکیشن (App-like UI):** به جای ارسال پیام‌های پی‌درپی و شلوغ کردن چت، ربات از دکمه‌های شیشه‌ای و متد `editMessageText` استفاده می‌کند تا همان یک پیام مدام تغییر شکل دهد.
- **حذف خودکار پیام کاربر:** پیام‌های متنی تایپ شده توسط کاربر فوراً پاک می‌شوند تا ربات تمیز بماند.
- **ماژولار و بی‌نهایت قابل گسترش:** هر منو در یک فایل جداگانه در پوشه `menus` قرار دارد. اضافه کردن ۱۰۰۰ منو هم باعث کندی ربات نمی‌شود!
- **ذخیره وضعیت (Stateful):** وضعیت فعلی کاربر (Step) در دیتابیس ذخیره می‌شود. حتی اگر سرور ریستارت شود، کاربر در همان منویی که بوده باقی می‌ماند.

## 🚀 راهنمای نصب و راه‌اندازی (بسیار آسان)

**پیش‌نیازها:** سرور لینوکس یا هاست دارای PHP 7.4+ ، MySQL و دارای گواهینامه SSL (برای Webhook).

1. **کلون کردن پروژه:**
   فایل‌ها را در پوشه متصل به دامنه خود (مثلاً `/var/www/html/bot`) قرار دهید.

2. **تنظیم دیتابیس:**
   یک دیتابیس بسازید و فایل `database.sql` موجود در این ریپازیتوری را در آن ایمپورت کنید.

3. **تنظیم فایل پیکربندی:**
   فایل `config.example.php` را به `config.php` تغییر نام دهید.
   توکن ربات تلگرام و اطلاعات دیتابیس خود را داخل آن وارد کنید.

4. **تنظیم Webhook (وب‌هوک):**
   لینک زیر را کپی کرده، اطلاعات خود را جایگزین کنید و در مرورگر اجرا کنید تا تلگرام به ربات شما متصل شود:
   ```text
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://<YOUR_DOMAIN>/bot/index.php
🧠 نحوه کارکرد سیستم (برای توسعه‌دهندگان)
تلگرام آپدیت‌ها را به index.php می‌فرستد.
کاربر در دیتابیس ثبت شده و step (وضعیت) او خوانده می‌شود.
درخواست به switcher.php (مغز مسیریاب) پاس داده می‌شود.
در فایل switcher.php وضعیت دیتابیس آپدیت شده و فایل مربوطه از پوشه menus/ فراخوانی می‌شود.
برای اضافه کردن بخش جدید، فقط کافیست یک callback_data جدید در دکمه‌ها تعریف کنید، آن را در switcher.php هندل کنید و یک فایل جدید در پوشه menus بسازید!
Created with ❤️ by [Your Name/SilentGalaxy]
code
Code
---

### ۲. ساختار فایل‌هایی که باید در گیت‌هاب آپلود کنید:

برای اینکه کاربران سردرگم نشوند، اطلاعات حساس را به شکل `example` قرار می‌دهیم.

#### فایل `database.sql` (جدید - برای راحتی کاربران):
```sql
CREATE TABLE `users` (
  `chat_id` bigint(20) NOT NULL PRIMARY KEY,
  `step` varchar(50) DEFAULT '0',
  `joined_at` timestamp DEFAULT CURRENT_TIMESTAMP
);
فایل config.example.php (کاربر باید نام این را به config.php تغییر دهد):
code
PHP
<?php
// توکن ربات خود را اینجا قرار دهید
$botToken = "YOUR_BOT_TOKEN_HERE";
$apiUrl = "https://api.telegram.org/bot" . $botToken . "/";

// اطلاعات دیتابیس
$host = '127.0.0.1';
$db   = 'YOUR_DATABASE_NAME';
$user = 'YOUR_DATABASE_USER';
$pass = 'YOUR_DATABASE_PASSWORD';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (\PDOException $e) {
    error_log($e->getMessage());
    exit;
}
?>
فایل functions.php:
code
PHP
<?php
function bot($method, $datas = []) {
    global $apiUrl;
    $url = $apiUrl . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){ error_log(curl_error($ch)); }
    curl_close($ch);
    return json_decode($res, true);
}

function setStep($chat_id, $step) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET step = ? WHERE chat_id = ?");
    $stmt->execute([$step, $chat_id]);
}
?>
فایل index.php:
code
PHP
<?php
require_once 'config.php';
require_once 'functions.php';

$update = json_decode(file_get_contents('php://input'), true);

if(isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = $update['message']['text'] ?? '';
    $message_id = $update['message']['message_id'];
    $is_callback = false;
} elseif(isset($update['callback_query'])) {
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $data = $update['callback_query']['data'];
    $message_id = $update['callback_query']['message']['message_id'];
    $is_callback = true;
} else {
    exit();
}

$stmt = $pdo->prepare("SELECT step FROM users WHERE chat_id = ?");
$stmt->execute([$chat_id]);
$user = $stmt->fetch();

if(!$user) {
    $stmt = $pdo->prepare("INSERT INTO users (chat_id, step) VALUES (?, '0')");
    $stmt->execute([$chat_id]);
    $user_step = '0';
} else {
    $user_step = $user['step'];
}

// Auto-delete user text messages for a cleaner App-like UI
if(!$is_callback && $text != '/start') {
    bot('deleteMessage', ['chat_id' => $chat_id, 'message_id' => $message_id]);
}

require_once 'switcher.php';
?>
فایل switcher.php:
code
PHP
<?php
if(isset($text) && $text == '/start') {
    if($user_step == '0') {
        require_once 'menus/step_0.php';
    } else {
        require_once 'menus/step_1.php';
    }
    exit();
}

if($is_callback) {
    // Handling button clicks
    if($data == 'accept_rules') {
        setStep($chat_id, '1');
        $user_step = '1';
        require_once 'menus/step_1.php';
    }
    elseif($data == 'back_to_rules') {
        setStep($chat_id, '0');
        $user_step = '0';
        require_once 'menus/step_0.php';
    }
}
?>
فایل menus/step_0.php:
code
PHP
<?php
$text_msg = "👋 Welcome to SilentGalaxy Base Bot!\n\nPlease read the rules. Click the button below to accept.";

$keyboard = json_encode([
    'inline_keyboard' => [
        [['text' => '✅ I Accept the Rules', 'callback_data' => 'accept_rules']]
    ]
]);

if($is_callback) {
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text_msg,
        'reply_markup' => $keyboard
    ]);
} else {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $text_msg,
        'reply_markup' => $keyboard
    ]);
}
?>
فایل menus/step_1.php:
code
PHP
<?php
$text_msg = "🎉 Registration complete.\nWelcome to the Main Menu!\n\nPlease select an option:";

$keyboard = json_encode([
    'inline_keyboard' => [
        [['text' => '👤 My Profile', 'callback_data' => 'profile'], ['text' => '⚙️ Settings', 'callback_data' => 'settings']],
        [['text' => '🔙 Back to Rules', 'callback_data' => 'back_to_rules']]
    ]
]);

if($is_callback) {
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text_msg,
        'reply_markup' => $keyboard
    ]);
} else {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $text_msg,
        'reply_markup' => $keyboard
    ]);
}
?>
(نکته: در منوها متون را به انگلیسی نوشتم تا در گیت‌هابِ بین‌المللی استانداردتر باشد. در صورت تمایل می‌توانید همان فارسیِ کدهای خودتان را جایگزین کنید).

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

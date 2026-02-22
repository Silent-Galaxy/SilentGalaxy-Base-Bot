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

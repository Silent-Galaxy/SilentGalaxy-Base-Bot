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

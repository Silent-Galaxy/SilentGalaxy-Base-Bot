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

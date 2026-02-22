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

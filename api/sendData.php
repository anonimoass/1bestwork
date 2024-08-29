<?php
require 'config.php';

$token = $telegramBotToken;
$chat_id = $telegramChatId;
$message = "";

// Составляем сообщение из данных формы
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message_text = $_POST['message'];

$message .= "Имя: " . htmlspecialchars($name) . "\n";
$message .= "Email: " . htmlspecialchars($email) . "\n";
$message .= "Телефон: " . htmlspecialchars($phone) . "\n";
$message .= "Сообщение: " . htmlspecialchars($message_text) . "\n";

// Добавляем информацию о странице, с которой была отправлена форма
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    $message .= "Отправлено с: " . htmlspecialchars($_SERVER['HTTP_REFERER']) . "\n";
} else {
    $message .= "Источник страницы не доступен.\n";
}

// URL для отправки сообщения в Telegram
$url = "https://api.telegram.org/bot$token/sendMessage";

$data = [
    'chat_id' => $chat_id,
    'text' => $message,
    'parse_mode' => 'HTML',
];

// Опции для контекста HTTP-запроса
$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ]
];

$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

// Проверяем успешность выполнения запроса
if ($result === FALSE) {
    error_log('Ошибка при отправке сообщения в Telegram: ' . error_get_last()['message']);
}

// Редирект после отправки сообщения
$redirectUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
header('Location: ' . $redirectUrl);
exit();
?>


<?php
// НАСТРОЙКИ TELEGRAM БОТА
$TELEGRAM_TOKEN = "7435968319:AAHA49wddTQtbHQX9tJhlj0epNLVKv96iwM";
$TELEGRAM_CHAT_ID = "-1002334341556";

// Устанавливаем заголовок JSON ответа
header('Content-Type: application/json; charset=utf-8');

// Проверяем метод запроса
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
    exit;
}

// Получаем и очищаем номер телефона
$phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : '';

if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Номер телефона не указан']);
    exit;
}

// Формируем красивое сообщение
$message = "🆕 НОВАЯ ЗАЯВКА!\n📱 Номер телефона: " . $phone . "\n⏰ Время: " . date('d.m.Y H:i:s');

// URL для отправки в Telegram
$api_url = "https://api.telegram.org/bot" . $TELEGRAM_TOKEN . "/sendMessage";

// Инициализируем cURL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'chat_id' => $TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Проверяем результат
if ($http_code === 200 && !$curl_error) {
    $telegram_response = json_decode($response, true);
    if (isset($telegram_response['ok']) && $telegram_response['ok'] === true) {
        echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка Telegram API: ' . ($telegram_response['description'] ?? 'Неизвестная ошибка')]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка соединения: ' . $curl_error]);
}
?>
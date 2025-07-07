<?php
// Включаем логирование ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Чтение JSON-запроса
$data = json_decode(file_get_contents('php://input'), true);

// Логируем входящие данные (для отладки)
file_put_contents('debug.json', json_encode($data, JSON_PRETTY_PRINT));

// Получение user ID из запроса
$userId = $data['telegram_id'] ?? null;

// Проверка наличия ID
if (!$userId) {
    echo json_encode([
        'subscribed' => false,
        'error' => 'User ID not found'
    ]);
    exit;
}

// Получаем токен и имя канала из переменных окружения
$botToken = getenv('BOT_TOKEN');
$channelUsername = getenv('CHANNEL_USERNAME');

// Формируем запрос к Telegram API
$url = "https://api.telegram.org/bot$botToken/getChatMember?chat_id=$channelUsername&user_id=$userId";
$response = file_get_contents($url);
$result = json_decode($response, true);

// Обработка ответа Telegram
if (!$result['ok']) {
    echo json_encode([
        'subscribed' => false,
        'error' => $result['description'] ?? 'Telegram API error'
    ]);
    exit;
}

$status = $result['result']['status'] ?? null;
$isSubscribed = in_array($status, ['member', 'administrator', 'creator']);

// Возврат результата
echo json_encode([
    'subscribed' => $isSubscribed,
    'status' => $status
]);

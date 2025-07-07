<?php
$botToken = getenv('BOT_TOKEN');
$channelUsername = getenv('CHANNEL_USERNAME');

// Получаем входящие данные
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['contact']['telegram']['id'] ?? null;

if (!$userId) {
    echo json_encode(['subscribed' => false, 'error' => 'User ID not found']);
    exit;
}

// Отправляем запрос к Telegram API
$url = "https://api.telegram.org/bot$botToken/getChatMember?chat_id=$channelUsername&user_id=$userId";
$response = file_get_contents($url);

// Проверка, что Telegram ответил корректно
$result = json_decode($response, true);

if (!$result['ok']) {
    echo json_encode([
        'subscribed' => false,
        'error' => $result['description'] ?? 'Unknown Telegram API error'
    ]);
    exit;
}

$status = $result['result']['status'] ?? null;
$isSubscribed = in_array($status, ['member', 'administrator', 'creator']);

// Возвращаем результат
echo json_encode([
    'subscribed' => $isSubscribed,
    'status' => $status
]);

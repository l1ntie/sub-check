<?php
// Получаем тело запроса
$data = json_decode(file_get_contents('php://input'), true);

$userId = $data['telegram_id'] ?? null;
if (!$userId) {
    echo json_encode(['subscribed' => false, 'error' => 'User ID not found']);
    exit;
}

$botToken = getenv('BOT_TOKEN');
$channelUsername = getenv('CHANNEL_USERNAME');

// Формируем URL для запроса к Telegram API
$url = "https://api.telegram.org/bot$botToken/getChatMember?chat_id=$channelUsername&user_id=$userId";

$response = file_get_contents($url);
$result = json_decode($response, true);

// Проверяем успешность ответа Telegram API
if (!$result['ok']) {
    echo json_encode(['subscribed' => false, 'error' => $result['description'] ?? 'Telegram API error']);
    exit;
}

// Смотрим статус пользователя в чате
$status = $result['result']['status'] ?? null;

// Считаем, что подписан, если статус — это:
// 'creator', 'administrator', 'member'
// Все остальные (left, kicked, banned и т.п.) — НЕ подписаны
$isSubscribed = in_array($status, ['creator', 'administrator', 'member']);

echo json_encode([
    'subscribed' => $isSubscribed,
    'status' => $status
]);

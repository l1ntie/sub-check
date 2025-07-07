<?php
$botToken = getenv('BOT_TOKEN');
$channelUsername = getenv('CHANNEL_USERNAME');

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['contact']['telegram']['id'] ?? null;

if (!$userId) {
    echo json_encode(['subscribed' => false, 'error' => 'User ID not found']);
    exit;
}

$url = "https://api.telegram.org/bot$botToken/getChatMember?chat_id=$channelUsername&user_id=$userId";
$response = file_get_contents($url);
$result = json_decode($response, true);

$status = $result['result']['status'] ?? null;
$isSubscribed = in_array($status, ['member', 'administrator', 'creator']);

echo json_encode(['subscribed' => $isSubscribed]);
?>

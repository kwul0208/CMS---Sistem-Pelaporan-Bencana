<?php
use Google\Client;

function getAccessToken() {
    $serviceAccountPath = base_path('public/key/serviceAccountKey.json'); // tempatkan file serviceAccountKey.json di folder publict.key
    $client = new Client();
    $client->setAuthConfig($serviceAccountPath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $client->useApplicationDefaultCredentials();
    $token = $client->fetchAccessTokenWithAssertion();
    return $token['access_token'];
}

function sendMessage($accessToken, $projectId, $message) {
    $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    $response = curl_exec($ch);
     if ($response === false) {
     throw new Exception('Curl error: ' . curl_error($ch));
     }
    curl_close($ch);
    return json_decode($response, true);
}
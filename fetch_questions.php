<?php
header('Content-Type: application/json');
require 'db.php';

$n = intval($_GET['n'] ?? 10);
if($n < 1) $n = 10;

$apiBase = 'https://marcconrad.com/uob/banana/api.php?out=json';

$questions = [];
$ch = curl_init();

for($i=0; $i<$n; $i++){
    curl_setopt($ch, CURLOPT_URL, $apiBase);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $resp = curl_exec($ch);

    // Debug if cURL fails
    if($resp === false) {
        echo json_encode([
            'success' => false,
            'error' => 'CURL failed: ' . curl_error($ch)
        ]);
        exit;
    }

    $json = json_decode($resp, true);

    if(!$json || !isset($json['question'])) continue;

    $questions[] = [
        'question' => $json['question'],
        'solution' => $json['solution']
    ];
}

curl_close($ch);

echo json_encode(['success'=>true,'questions'=>$questions]);

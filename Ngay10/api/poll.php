<?php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['option'])) {
    $votes = isset($_SESSION['polls']) ? $_SESSION['polls'] : [
        'interface' => 0,
        'speed' => 0,
        'service' => 0
    ];
    
    $votes[$input['option']]++;
    $_SESSION['polls'] = $votes;
    
    $total = array_sum($votes);
    $percentages = array_map(function($v) use ($total) {
        return round(($v / $total) * 100);
    }, $votes);
    
    echo json_encode($percentages);
}
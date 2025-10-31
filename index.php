<?php
declare(strict_types=1);

const CARD_ARCANA_VALUES = ['Major', 'Minor'];
const CARD_ELEMENT_VALUES = ['Air', 'Fire', 'Water', 'Earth'];

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

$pdo = require __DIR__ . '/config.php';

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(static function (Throwable $throwable): void {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-API-Key');
    }
    if (http_response_code() < 400) {
        http_response_code(500);
    }
    echo json_encode([
        'error' => 'server',
        'message' => $throwable->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
});

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$segments = extractPathSegments();
if ($segments === []) {
    $segments = ['cards'];
}

$resource = strtolower((string)array_shift($segments));

if ($resource === 'readings') {
    ensureReadingsTable($pdo);
}

switch ($resource) {
    case '':
    case 'cards':
        handleCards($method, $segments, $pdo);
        break;
    case 'readings':
        handleReadings($method, $segments, $pdo);
        break;
    default:
        sendJson(404, [
            'error' => 'not_found',
            'message' => 'Ressourcen findes ikke.',
        ]);
}

function handleCards(string $method, array $segments, PDO $pdo): void
{
    switch ($method) {
        case 'GET':
            if ($segments === []) {
                listCards($pdo);
                return;
            }
            $id = filterId($segments[0]);
            getCard($pdo, $id);
            return;

        case 'POST':
            if ($segments !== []) {
                methodNotAllowed(['GET', 'POST']);
            }
            requireApiKey();
            createCard($pdo);
            return;

        case 'PUT':
            if (count($segments) !== 1) {
                methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
            }
            requireApiKey();
            $id = filterId($segments[0]);
            updateCard($pdo, $id);
            return;

        case 'DELETE':
            if (count($segments) !== 1) {
                methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
            }
            requireApiKey();
            $id = filterId($segments[0]);
            deleteCard($pdo, $id);
            return;

        default:
            methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
    }
}

function handleReadings(string $method, array $segments, PDO $pdo): void
{
    switch ($method) {
        case 'GET':
            if ($segments === []) {
                listReadings($pdo);
                return;
            }
            $id = filterId($segments[0]);
            getReading($pdo, $id);
            return;

        case 'POST':
            if ($segments !== []) {
                methodNotAllowed(['GET', 'POST']);
            }
            requireApiKey();
            createReading($pdo);
            return;

        case 'PUT':
            if (count($segments) !== 1) {
                methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
            }
            requireApiKey();
            $id = filterId($segments[0]);
            updateReading($pdo, $id);
            return;

        case 'DELETE':
            if (count($segments) !== 1) {
                methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
            }
            requireApiKey();
            $id = filterId($segments[0]);
            deleteReading($pdo, $id);
            return;

        default:
            methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
    }
}

function listCards(PDO $pdo): void
{
    if (isset($_GET['random'])) {
        $stmt = $pdo->query(
            "SELECT id,name,arcana,suit,number,description,
                    upright_meaning,reversed_meaning,love_meaning,
                    career_meaning,spiritual_meaning,image_url,keywords,element
             FROM cards
             ORDER BY RAND()
             LIMIT 1"
        );
        $card = $stmt->fetch();
        if (!$card) {
            sendJson(404, ['error' => 'not_found', 'message' => 'Ingen kort tilgængelige.']);
        }
        sendJson(200, attachCardLinks($card));
    }

    $where = [];
    $params = [];

    if (isset($_GET['arcana']) && $_GET['arcana'] !== '') {
        $where[] = 'arcana = :arcana';
        $params['arcana'] = $_GET['arcana'];
    }
    if (isset($_GET['element']) && $_GET['element'] !== '') {
        $where[] = 'element = :element';
        $params['element'] = $_GET['element'];
    }
    if (isset($_GET['q']) && $_GET['q'] !== '') {
        $where[] = 'name LIKE :q';
        $params['q'] = '%' . $_GET['q'] . '%';
    }

    $sql = "SELECT id,name,arcana,suit,number,description,
                   upright_meaning,reversed_meaning,love_meaning,
                   career_meaning,spiritual_meaning,image_url,keywords,element
            FROM cards";

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY number, name';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cards = $stmt->fetchAll();

    sendJson(200, array_map('attachCardLinks', $cards));
}

function getCard(PDO $pdo, int $id): void
{
    $card = findCard($pdo, $id);
    if (!$card) {
        sendJson(404, ['error' => 'not_found', 'message' => 'Kortet blev ikke fundet.']);
    }
    sendJson(200, attachCardLinks($card));
}

function createCard(PDO $pdo): void
{
    $payload = readJsonBody();
    [$data, $errors] = validateCardPayload($payload, false);
    if ($errors) {
        sendJson(422, ['error' => 'validation', 'fields' => $errors]);
    }

    $sql = "INSERT INTO cards
      (name, arcana, suit, number, description, upright_meaning, reversed_meaning,
       love_meaning, career_meaning, spiritual_meaning, image_url, keywords, element)
      VALUES
      (:name,:arcana,:suit,:number,:description,:upright,:reversed,
       :love,:career,:spiritual,:image,:keywords,:element)";

    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            'name'       => $data['name'],
            'arcana'     => $data['arcana'],
            'suit'       => $data['suit'],
            'number'     => $data['number'],
            'description'=> $data['description'],
            'upright'    => $data['upright_meaning'],
            'reversed'   => $data['reversed_meaning'],
            'love'       => $data['love_meaning'],
            'career'     => $data['career_meaning'],
            'spiritual'  => $data['spiritual_meaning'],
            'image'      => $data['image_url'],
            'keywords'   => $data['keywords'],
            'element'    => $data['element'],
        ]);
    } catch (PDOException $e) {
        sendJson(500, ['error' => 'database', 'message' => $e->getMessage()]);
    }

    $id = (int)$pdo->lastInsertId();
    $card = findCard($pdo, $id);
    if (!$card) {
        sendJson(500, ['error' => 'database', 'message' => 'Kortet blev oprettet, men kunne ikke læses.']);
    }

    sendJson(
        201,
        attachCardLinks($card),
        ['Location' => relativeResourcePath('cards/' . $id)]
    );
}

function updateCard(PDO $pdo, int $id): void
{
    $existing = findCard($pdo, $id);
    if (!$existing) {
        sendJson(404, ['error' => 'not_found', 'message' => 'Kortet blev ikke fundet.']);
    }

    $payload = readJsonBody();
    [$data, $errors] = validateCardPayload($payload, true, $existing);
    if ($errors) {
        sendJson(422, ['error' => 'validation', 'fields' => $errors]);
    }

    $sql = "UPDATE cards SET
                name = :name,
                arcana = :arcana,
                suit = :suit,
                number = :number,
                description = :description,
                upright_meaning = :upright,
                reversed_meaning = :reversed,
                love_meaning = :love,
                career_meaning = :career,
                spiritual_meaning = :spiritual,
                image_url = :image,
                keywords = :keywords,
                element = :element
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            'id'         => $id,
            'name'       => $data['name'],
            'arcana'     => $data['arcana'],
            'suit'       => $data['suit'],
            'number'     => $data['number'],
            'description'=> $data['description'],
            'upright'    => $data['upright_meaning'],
            'reversed'   => $data['reversed_meaning'],
            'love'       => $data['love_meaning'],
            'career'     => $data['career_meaning'],
            'spiritual'  => $data['spiritual_meaning'],
            'image'      => $data['image_url'],
            'keywords'   => $data['keywords'],
            'element'    => $data['element'],
        ]);
    } catch (PDOException $e) {
        sendJson(500, ['error' => 'database', 'message' => $e->getMessage()]);
    }

    $card = findCard($pdo, $id);
    sendJson(200, attachCardLinks($card));
}

function deleteCard(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM cards WHERE id = :id');
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() === 0) {
        sendJson(404, ['error' => 'not_found', 'message' => 'Kortet blev ikke fundet.']);
    }
    sendJson(204);
}

function listReadings(PDO $pdo): void
{
    $where = [];
    $params = [];

    if (isset($_GET['spread_type']) && $_GET['spread_type'] !== '') {
        $where[] = 'spread_type = :spread_type';
        $params['spread_type'] = $_GET['spread_type'];
    }

    $sql = "SELECT id, title, question, spread_type, cards, notes, created_at, updated_at
            FROM readings";

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $readings = array_map('normaliseReadingRow', $rows);
    sendJson(200, array_map('attachReadingLinks', $readings));
}

function getReading(PDO $pdo, int $id): void
{
    $reading = findReading($pdo, $id);
    if (!$reading) {
        sendJson(404, ['error' => 'not_found', 'message' => 'Reading blev ikke fundet.']);
    }
    sendJson(200, attachReadingLinks($reading));
}

function createReading(PDO $pdo): void
{
    $payload = readJsonBody();
    [$data, $errors] = validateReadingPayload($payload, false);
    if ($errors) {
        sendJson(422, ['error' => 'validation', 'fields' => $errors]);
    }

    $sql = "INSERT INTO readings (title, question, spread_type, cards, notes)
            VALUES (:title, :question, :spread_type, :cards, :notes)";

    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            'title'       => $data['title'],
            'question'    => $data['question'],
            'spread_type' => $data['spread_type'],
            'cards'       => $data['cards_json'],
            'notes'       => $data['notes'],
        ]);
    } catch (PDOException $e) {
        sendJson(500, ['error' => 'database', 'message' => $e->getMessage()]);
    }

    $id = (int)$pdo->lastInsertId();
    $reading = findReading($pdo, $id);
    if (!$reading) {
        sendJson(500, ['error' => 'database', 'message' => 'Reading blev oprettet, men kunne ikke læses.']);
    }

    sendJson(
        201,
        attachReadingLinks($reading),
        ['Location' => relativeResourcePath('readings/' . $id)]
    );
}

function updateReading(PDO $pdo, int $id): void
{
    $existing = findReading($pdo, $id);
    if (!$existing) {
        sendJson(404, ['error' => 'not_found', 'message' => 'Reading blev ikke fundet.']);
    }

    $payload = readJsonBody();
    [$data, $errors] = validateReadingPayload($payload, true, $existing);
    if ($errors) {
        sendJson(422, ['error' => 'validation', 'fields' => $errors]);
    }

    $sql = "UPDATE readings SET
                title = :title,
                question = :question,
                spread_type = :spread_type,
                cards = :cards,
                notes = :notes
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            'id'          => $id,
            'title'       => $data['title'],
            'question'    => $data['question'],
            'spread_type' => $data['spread_type'],
            'cards'       => $data['cards_json'],
            'notes'       => $data['notes'],
        ]);
    } catch (PDOException $e) {
        sendJson(500, ['error' => 'database', 'message' => $e->getMessage()]);
    }

    $reading = findReading($pdo, $id);
    sendJson(200, attachReadingLinks($reading));
}

function deleteReading(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM readings WHERE id = :id');
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() === 0) {
        sendJson(404, ['error' => 'not_found', 'message' => 'Reading blev ikke fundet.']);
    }
    sendJson(204);
}

function findCard(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        "SELECT id,name,arcana,suit,number,description,
                upright_meaning,reversed_meaning,love_meaning,
                career_meaning,spiritual_meaning,image_url,keywords,element
         FROM cards
         WHERE id = :id"
    );
    $stmt->execute(['id' => $id]);
    $card = $stmt->fetch();
    return $card ?: null;
}

function validateCardPayload(array $input, bool $isUpdate, array $current = []): array
{
    $errors = [];
    $data = [];

    $name = array_key_exists('name', $input)
        ? trim((string)$input['name'])
        : trim((string)($current['name'] ?? ''));
    if ($name === '') {
        $errors['name'] = 'Navn er påkrævet.';
    }
    $data['name'] = $name;

    $arcana = array_key_exists('arcana', $input)
        ? trim((string)$input['arcana'])
        : trim((string)($current['arcana'] ?? ''));
    if ($arcana === '' || !in_array($arcana, CARD_ARCANA_VALUES, true)) {
        $errors['arcana'] = 'Arcana skal være Major eller Minor.';
    }
    $data['arcana'] = $arcana;

    $element = array_key_exists('element', $input)
        ? trim((string)$input['element'])
        : trim((string)($current['element'] ?? ''));
    if ($element === '' || !in_array($element, CARD_ELEMENT_VALUES, true)) {
        $errors['element'] = 'Element skal være Air, Fire, Water eller Earth.';
    }
    $data['element'] = $element;

    if (array_key_exists('number', $input)) {
        $numberRaw = $input['number'];
        if ($numberRaw === '' || $numberRaw === null) {
            $data['number'] = null;
        } elseif (is_numeric($numberRaw)) {
            $data['number'] = (int)$numberRaw;
        } else {
            $errors['number'] = 'Number skal være et tal.';
            $data['number'] = null;
        }
    } else {
        $data['number'] = isset($current['number']) ? (int)$current['number'] : null;
    }

    $optional = [
        'suit',
        'description',
        'upright_meaning',
        'reversed_meaning',
        'love_meaning',
        'career_meaning',
        'spiritual_meaning',
        'image_url',
        'keywords',
    ];

    foreach ($optional as $field) {
        if (array_key_exists($field, $input)) {
            $value = trim((string)$input[$field]);
        } elseif ($isUpdate) {
            $value = trim((string)($current[$field] ?? ''));
        } else {
            $value = '';
        }
        $data[$field] = $value;
    }

    return [$data, $errors];
}

function attachCardLinks(array $card): array
{
    if (!isset($card['id'])) {
        return $card;
    }
    $id = (int)$card['id'];
    $self = relativeResourcePath('cards/' . $id);
    $card['links'] = [
        ['rel' => 'self', 'href' => $self, 'method' => 'GET'],
        ['rel' => 'update', 'href' => $self, 'method' => 'PUT'],
        ['rel' => 'delete', 'href' => $self, 'method' => 'DELETE'],
    ];
    return $card;
}

function normaliseReadingRow(array $row): array
{
    $row['cards'] = decodeCardsField($row['cards'] ?? '[]');
    return $row;
}

function findReading(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        "SELECT id, title, question, spread_type, cards, notes, created_at, updated_at
         FROM readings
         WHERE id = :id"
    );
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    return normaliseReadingRow($row);
}

function validateReadingPayload(array $input, bool $isUpdate, array $current = []): array
{
    $errors = [];
    $data = [];

    $title = array_key_exists('title', $input)
        ? trim((string)$input['title'])
        : trim((string)($current['title'] ?? ''));
    if ($title === '') {
        $errors['title'] = 'Titel er påkrævet.';
    }
    $data['title'] = $title;

    if (array_key_exists('cards', $input)) {
        $cardsValue = $input['cards'];
    } else {
        $cardsValue = $isUpdate ? ($current['cards'] ?? []) : null;
    }

    if (is_string($cardsValue)) {
        $decoded = json_decode($cardsValue, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $cardsValue = $decoded;
        }
    }

    $cards = [];
    if (is_array($cardsValue)) {
        foreach ($cardsValue as $cardId) {
            if (is_numeric($cardId)) {
                $cards[] = (int)$cardId;
            }
        }
    }

    if (!$cards) {
        $errors['cards'] = 'Angiv mindst ét kort.';
    }

    $data['cards'] = $cards;
    $data['cards_json'] = json_encode($cards, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $question = array_key_exists('question', $input)
        ? trim((string)$input['question'])
        : trim((string)($current['question'] ?? ''));
    $data['question'] = $question === '' ? null : $question;

    $spreadType = array_key_exists('spread_type', $input)
        ? trim((string)$input['spread_type'])
        : trim((string)($current['spread_type'] ?? ''));
    $data['spread_type'] = $spreadType === '' ? null : $spreadType;

    $notes = array_key_exists('notes', $input)
        ? trim((string)$input['notes'])
        : trim((string)($current['notes'] ?? ''));
    $data['notes'] = $notes;

    return [$data, $errors];
}

function attachReadingLinks(array $reading): array
{
    if (!isset($reading['id'])) {
        return $reading;
    }
    $id = (int)$reading['id'];
    $self = relativeResourcePath('readings/' . $id);
    $reading['links'] = [
        ['rel' => 'self', 'href' => $self, 'method' => 'GET'],
        ['rel' => 'update', 'href' => $self, 'method' => 'PUT'],
        ['rel' => 'delete', 'href' => $self, 'method' => 'DELETE'],
    ];
    return $reading;
}

function ensureReadingsTable(PDO $pdo): void
{
    static $ensured = false;
    if ($ensured) {
        return;
    }

    $sql = "CREATE TABLE IF NOT EXISTS readings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        question VARCHAR(255) DEFAULT NULL,
        spread_type VARCHAR(100) DEFAULT NULL,
        cards TEXT NOT NULL,
        notes TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql);
    $ensured = true;
}

function decodeCardsField(string $json): array
{
    $decoded = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        return [];
    }
    return array_values(array_filter(array_map(static function ($value) {
        return is_numeric($value) ? (int)$value : null;
    }, $decoded), static fn($value) => $value !== null));
}

function sendJson(int $status, $data = null, array $headers = []): void
{
    http_response_code($status);
    foreach ($headers as $key => $value) {
        header($key . ': ' . $value);
    }

    if ($status === 204) {
        exit;
    }

    if ($data === null) {
        $data = new stdClass();
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function readJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        sendJson(400, ['error' => 'invalid_json', 'message' => 'Kroppen skal være gyldigt JSON.']);
    }
    return $decoded;
}

function requireApiKey(): void
{
    $key = $_SERVER['HTTP_X_API_KEY'] ?? '';
    if ($key !== 'HEMMELIGT_TOKEN') {
        sendJson(401, ['error' => 'unauthorized', 'message' => 'Gyldig API-nøgle er påkrævet.']);
    }
}

function methodNotAllowed(array $allowed): void
{
    header('Allow: ' . implode(', ', $allowed));
    sendJson(405, ['error' => 'method_not_allowed', 'allowed' => $allowed]);
}

function filterId(string $value): int
{
    if (!ctype_digit($value)) {
        sendJson(400, ['error' => 'invalid_id', 'message' => 'ID skal være et positivt heltal.']);
    }
    return (int)$value;
}

function extractPathSegments(): array
{
    $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = $scriptName;

    if ($requestUri === $scriptName) {
        $path = '';
    } elseif ($scriptName !== '' && strpos($requestUri, $scriptName . '/') === 0) {
        $path = substr($requestUri, strlen($scriptName));
    } else {
        $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($dir !== '' && strpos($requestUri, $dir) === 0) {
            $path = substr($requestUri, strlen($dir));
        } else {
            $path = $requestUri;
        }
    }

    $path = trim($path, '/');
    if ($path === '') {
        return [];
    }
    return explode('/', $path);
}

function relativeResourcePath(string $resource): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? 'index.php';
    $script = rtrim($script, '/');
    return $script . '/' . ltrim($resource, '/');
}

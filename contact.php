<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true]); 
    exit;
}

$name    = trim(strip_tags($_POST['name']    ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

$name  = str_replace(["\r", "\n"], '', $name);
$email = str_replace(["\r", "\n"], '', $email);

$errors = [];

if (mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 100) {
    $errors[] = 'Nom invalide (2–100 caractères).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254) {
    $errors[] = 'Adresse email invalide.';
}
if (mb_strlen($message, 'UTF-8') < 10 || mb_strlen($message, 'UTF-8') > 5000) {
    $errors[] = 'Le message doit faire entre 10 et 5000 caractères.';
}

if ($errors) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'errors' => $errors]);
    exit;
}

$to      = 'hugo.geay2@gmail.com';
$subject = '=?UTF-8?B?' . base64_encode('[Portfolio] Message de ' . $name) . '?=';

$body  = "Nouveau message reçu via le portfolio.\n";
$body .= str_repeat('─', 48) . "\n\n";
$body .= "Nom    : {$name}\n";
$body .= "Email  : {$email}\n\n";
$body .= "Message :\n{$message}\n";

$host    = $_SERVER['HTTP_HOST'] ?? 'portfolio';
$headers = implode("\r\n", [
    "From: Portfolio Hugo Geay <no-reply@{$host}>",
    "Reply-To: {$name} <{$email}>",
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . PHP_VERSION,
    'MIME-Version: 1.0',
]);

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'errors' => ["Erreur d'envoi. Réessaie plus tard ou écris-moi directement."]]);
}

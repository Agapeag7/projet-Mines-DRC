<?php
// simple CLI helper to exercise login and 2FA flow

if (php_sapi_name() !== 'cli') {
    die("Run from command line only\n");
}

$email = $argv[1] ?? null;
$password = $argv[2] ?? null;
$action = $argv[3] ?? null; // optionally 'register'
if (!$email || !$password) {
    echo "Usage: php test_auth.php email password [register]\n";
    exit(1);
}

// use https to avoid automatic redirect; self-signed certs are ignored
$base = 'https://localhost/api/auth.php';

function post($url, $data) {
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($data),
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];
    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
}

if ($action === 'register') {
    echo "Registering $email...\n";
    $payload = [
        'email' => $email,
        'password' => $password,
        'password_confirm' => $password,
        'display_name' => 'CLI Tester ' . rand(1000,9999)
    ];
    $res = json_decode(post("$base?action=register", $payload), true);
    print_r($res);
    if (!($res && !empty($res['ok']))) {
        exit(0);
    }
    echo "Login after registration...\n";
}

// helper to perform request and show raw text + decoded value
function doReq($url, $data) {
    $raw = post($url, $data);
    echo "REQUEST to $url\n";
    echo "raw response (", strlen($raw), " bytes):\n";
    echo $raw . "\n";
    $decoded = json_decode($raw, true);
    echo "decoded: ";
    var_export($decoded);
    echo "\n";
    return $decoded;
}

// login with debug flag to retrieve code
$res = doReq("$base?action=login&debug=1", ['email'=>$email,'password'=>$password]);
if ($res && !empty($res['need_2fa']) && !empty($res['debug_code'])) {
    echo "Attempting verify2fa with code {$res['debug_code']}\n";
    $res2 = doReq("$base?action=verify_2fa", ['code'=>$res['debug_code']]);
}

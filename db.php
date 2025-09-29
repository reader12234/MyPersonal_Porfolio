<?php
// very simple DB connection (student-style)
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$name = 'portfolio_db';

$conn = mysqli_connect($host, $user, $pass, $name);
if (!$conn) {
    die('DB connection error: ' . mysqli_connect_error());
}

function h($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}



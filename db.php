<?php
$host = 'localhost';
$user = 'urnrgaote95vf';
$pass = 'tgk9ztof7xb1';
$dbname = 'db93wmfkttgfds';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

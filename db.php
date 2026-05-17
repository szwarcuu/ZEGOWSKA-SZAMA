<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'zegowska_szama';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
$conn->set_charset('utf8mb4');
?>

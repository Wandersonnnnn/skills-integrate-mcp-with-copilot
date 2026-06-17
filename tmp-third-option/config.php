<?php

// Do not expose stack traces in production.
$appEnv = getenv('APP_ENV') ?: 'production';
if ($appEnv !== 'production') {
	ini_set('display_errors', '1');
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', '0');
	error_reporting(0);
}

$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASSWORD');
$dbName = getenv('DB_NAME');
$dbPort = getenv('DB_PORT') ?: '3306';

if (!$dbHost || !$dbUser || !$dbName) {
	die('Database is not configured. Set DB_HOST, DB_USER, DB_PASSWORD, DB_NAME and DB_PORT.');
}

$conn = mysqli_init();
if (!$conn) {
	die('Failed to initialize database client.');
}

if (!mysqli_real_connect($conn, $dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort)) {
	die('Connection to database failed.');
}

mysqli_set_charset($conn, 'utf8mb4');

function db_conn()
{
	global $conn;
	return $conn;
}

function db_prepared_select_one($sql, $types = '', $params = array())
{
	$stmt = mysqli_prepare(db_conn(), $sql);
	if (!$stmt) {
		return null;
	}

	if ($types !== '' && !empty($params)) {
		$refs = array();
		foreach ($params as $k => $v) { $refs[$k] = &$params[$k]; }
		mysqli_stmt_bind_param($stmt, $types, ...$refs);
	}

	if (!mysqli_stmt_execute($stmt)) {
		mysqli_stmt_close($stmt);
		return null;
	}

	$result = mysqli_stmt_get_result($stmt);
	$row = $result ? mysqli_fetch_assoc($result) : null;
	mysqli_stmt_close($stmt);
	return $row ?: null;
}

function db_prepared_execute($sql, $types = '', $params = array())
{
	$stmt = mysqli_prepare(db_conn(), $sql);
	if (!$stmt) {
		return false;
	}

	if ($types !== '' && !empty($params)) {
		mysqli_stmt_bind_param($stmt, $types, ...$params);
	}

	$ok = mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	return $ok;
}

?>
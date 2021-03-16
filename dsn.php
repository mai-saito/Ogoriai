<?php 
require 'config.php';
try {
	$pdo = new PDO($dsn, $id, $pw);
	$pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	exit('データベースに接続できません。'.$e -> getMessage());
}
?>

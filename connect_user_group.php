<?php 
session_start();
session_regenerate_id(true);
var_dump($_SESSION);

if (isset($_SESSION['group_name'])) {
	require_once 'dsn.php';
	
	$sql = 'SELECT `user_id` FROM `users` WHERE `email` = :email';
	$stmt = $pdo -> prepare($sql);
	$stmt -> bindParam(':email', $_SESSION['email']);
	$stmt -> execute();
	$result = $stmt -> fetch();
	if ($result) {
		$stmt = null;
		$sql = 'INSERT INTO `user_group` (`user_id`, `group_id`) VALUES (:user_id, :group_id)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':user_id', $result['user_id']);
		$stmt -> bindValue(':group_id', $_SESSION['group_id']);
		$stmt -> execute();
	}
	$stmt = null;
} else {
	echo 'ログインし直してください。';
	echo '<input type="submit" value="戻る" onclick="history.go(-1)">';
}
$pdo = null;
header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/choose_member.php');
?>
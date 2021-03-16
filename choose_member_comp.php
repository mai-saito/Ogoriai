<?php 
	session_start();
	session_regenerate_id(true);
	require_once 'dsn.php';
	$sql = 'SELECT * FROM `users`';
	$stmt = $pdo -> prepare($sql);
	$stmt -> execute();
	$result = $stmt -> fetchAll();
	for ($i = 0, $end = count($result); $i < $end; $i++) {
		$user_id = $result[$i]['user_id'];
		//$_COOKIE[$user_id] = $result[$i]['name'];
		setcookie($user_id, '', time()-1000);
	}
	var_dump($_COOKIE);

	if (isset($_SESSION['group_name'])) {
		require_once 'dsn.php';
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			var_dump($_POST);
		}
	}
?>

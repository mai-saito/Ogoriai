<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// セッション開始
	session_start();
	check_session('user_id');	
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ユーザー名変更</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
		// DB接続情報
		$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

		// ユーザーの名前をアップデートする
		if ($_SERVER['REQUEST_METHOD'] === 'POST'):
			$sql = 'UPDATE `users` SET `name` = :new_name WHERE `name` = :current_name';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':new_name', $_POST['name']);
			$stmt -> bindParam(':current_name', $_SESSION['name']);
			$stmt -> execute();
			$_SESSION['name'] = $_POST['name'];
			$stmt = null;
			$pdo = null;
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/account.php');
		else:
			$pdo = null;
			exit('直接アクセス禁止です。');
		endif;
?>
</body>
</html>
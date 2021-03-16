<?php 
	session_start();
	session_regenerate_id(true);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>退会</title>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
	if (isset($_SESSION['email'])): 
		require_once 'dsn.php';
		if ($_SERVER['REQUEST_METHOD'] === 'POST'):
			if (!isset($_POST['password']) || !strlen($_POST['password'])):
?>
	<p>パスワードを入力してください。</p>
	<input type="submit" value="戻る" onclick="history.go(-1)">
<?php
			endif;

			$sql = 'SELECT `password` FROM `users` WHERE `email` = :email';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':email', $_SESSION['email']);
			$stmt -> execute();
			$result = $stmt -> fetch();
			if ($result): 
				if ($_POST['password'] === $result['password']):
					$stmt = null;
					$sql = 'DELETE FROM `users` WHERE `email` = :email';
					$stmt = $pdo -> prepare($sql);
					$stmt -> bindParam(':email', $_SESSION['email']);
					$stmt -> execute();
?>
	<p>退会処理が完了いたしました。</p>
	<p><a href="http://localhost/ogoriai/index.html">トップへ戻る</a></p>
<?php
				else:
?>
	<p>パスワードが異なります。</p>
	<input type="submit" value="戻る" onclick="history.go(-1)">
<?php	
				endif;	
			endif;
		$stmt = null;	
		else:
			$pdo = null;
			exit('直接アクセス禁止です。');
		endif;
		$pdo = null;
		//header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/index.html');
?>	
<?php 
else:?>
		<p>
			ログインしなおしてください。<br>
			（自動的にログイン画面に切り替わります。）
		</p>
		<script>
			setTimeout(function() {
				window.location.href = 'login.html';
			}, 2000);
		</script>
<?php endif; ?>
</body>
</html>
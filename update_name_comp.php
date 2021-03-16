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
	<title>ユーザー名変更</title>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
	if (isset($_SESSION['email'])):
		require 'dsn.php';
		if ($_SERVER['REQUEST_METHOD'] === 'POST'):
			$sql = 'UPDATE `users` SET `name` = :new_name WHERE `name` = :current_name';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':new_name', $_POST['name']);
			$stmt -> bindParam(':current_name', $_SESSION['name']);
			$stmt -> execute();
			$_SESSION['name'] = $_POST['name'];
			$stmt = null;
			$pdo = null;
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/account.php');
		else:
			$pdo = null;
			exit('直接アクセス禁止です。');
		endif;
	else : 
?>
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
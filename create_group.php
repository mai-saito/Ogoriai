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
	<title>メンバー選択</title>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
	if (isset($_SESSION['email'])): 
		require_once 'dsn.php';
		$errors = array();
		if ($_SERVER['REQUEST_METHOD'] === 'POST'):
			if (!isset($_POST['group_name']) || !strlen($_POST['group_name'])):
				$errors['group_name'] = 'グループ名を入力してください。';
			else:
				if (strlen($_POST['group_name']) > 20):
					$errors['group_name'] = 'グループ名は20文字以下で入力してください。';
				else:
					$group_name = $_POST['group_name'];
				endif;
			endif;
			
			if (count($errors) === 0):
				$sql = 'INSERT INTO `groups` (`group_name`) VALUES (:group_name)';
				$stmt = $pdo -> prepare($sql);
				$stmt -> bindValue(':group_name', $group_name);
				$stmt -> execute();
				$stmt = null;
				$sql = 'SELECT LAST_INSERT_ID()';
				$stmt = $pdo -> prepare($sql);
				$stmt -> execute();
				$group_id = $stmt -> fetch();
				$_SESSION['group_id'] = $group_id['LAST_INSERT_ID()'];
				$_SESSION['group_name'] = $group_name;
				$stmt = null;
			else: 
?>
	<ul>
<?php foreach ($errors as $error): ?>
		<li><?php echo $error ?></li>
<?php endforeach; ?>
	</ul>
	<input type="submit" value="戻る" onclick="history.go(-1)">
<?php
			endif;
		endif;
		$pdo = null;
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/connect_user_group.php');
	else : ?>
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
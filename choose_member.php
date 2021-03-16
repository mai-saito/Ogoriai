<?php 
	session_start();
	session_regenerate_id(true);
	var_dump($_SESSION);
	require_once 'dsn.php';
	$sql = 'SELECT * FROM `users`';
	$stmt = $pdo -> prepare($sql);
	$stmt -> execute();
	$result = $stmt -> fetchAll();
	for ($i = 0, $end = count($result); $i < $end; $i++) {
		$user_id = $result[$i]['user_id'];
		//$_COOKIE[$user_id] = $result[$i]['name'];
		setcookie($user_id, $result[$i]['name'], time()+1000);
	}
	var_dump($_COOKIE);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>メンバー選択</title>
	<link rel="stylesheet" href="css/styles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
<?php if (isset($_SESSION['group_name'])): ?>
	<main class="choose_member">
		<p>『<?php echo $_SESSION['group_name'] ?>』のメンバーを選んでください。</p>
		<form action="choose_member.php" method="POST" onsubmit="handleSubmit(event)">
			<table>
				<tr>
					<th><label for="user">名前もしくはメールアドレス：</label></th>
					<td><input type="text" name="user" id="user"></td>
					<td><input type="submit" name="search" value="検索する"></td>
				</tr>
			</table>
		</form>
<?php
	$members = array();
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if (isset($_POST['user'])):
			$user = $_POST['user'];
			$user = '%'.$user.'%';
			// Need to exclude current user from user search.
			//$sql = 'SELECT `user_id`, `name`, `email` FROM `users` WHERE `name` NOT IN (:name)';
			$sql = 'SELECT `user_id`, `name`, `email` FROM `users` WHERE `name` LIKE :name OR `email` LIKE :email';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':name', $user);
			$stmt -> bindParam(':email', $user);
			$stmt -> execute();
			$result = $stmt -> fetchAll();
			if ($result):
			var_dump($_COOKIE);
?>
		<table>
			<tr>
				<th>名前</th>
				<th>メールアドレス</th>
			</tr>
<?php foreach ($result as $user): ?>
			<tr>
				<td><?php echo $user['name'] ?></td>
				<td><?php echo $user['email'] ?></td>
				<td><button type="submit" name="add" value="<?php echo $user['user_id'] ?>" onclick="handleClick(event)">追加</button></td>
			</tr>
<?php endforeach; ?>
		</table>
		<form action="choose_member_comp.php" method="post">
			<section id="member-inputs">
<?php 
	$users = array_map('intval', explode(',', $_COOKIE['user_id']));
	var_dump($_COOKIE['user_id']);
	var_dump($users); 
?>
<?php foreach ($users as $user): ?>
				<input type="text" name="name" value="<?php echo $_COOKIE[$user] ?>">
<?php endforeach; ?>
			</section>
			<input type="submit" value="メンバー確定">
		</form>
<?php
			endif;
		endif;
	endif;
?>
	</main>
<?php else: ?>
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
<script src="script.js"></script>
</body>
</html>


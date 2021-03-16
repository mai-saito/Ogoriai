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
<?php if(isset($_SESSION['email'])): ?>
	<header>
		<h1>おごりあい</h1>
		<nav>
			<ul>
				<li><a href="mypage.php">マイページ</a></li>
				<li><a href="account.php">アカウント</a></li>
				<li><a href="logout.php">ログアウト</a></li>
			</ul>
		</nav>
	</header>
	<main class="update-name">
		<h1>ユーザー名</h1>
		<p>現在のユーザー名：<?php echo $_SESSION['name'] ?> さん</p>
		<form action="update_name_comp.php" method="post">
			<table>
				<tr>
					<th><label for="name">新しいユーザー名：</label></th>
					<td><input type="text" name="name" id="name"></td>
				</tr>
				<tr>
					<th><input type="submit" value="変更する"></th>
				</tr>
			</table>
		</form>
	</main>
<?php else : ?>
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
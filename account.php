<?php
session_start();
session_regenerate_id(true);
var_dump($_SESSION);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ユーザーアカウント情報</title>
	<link rel="stylesheet" href="css/styles.css">
</head>

<body>
<?php if (isset($_SESSION['email'])) : ?>
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
	<main class="account">
		<h1><?php echo $_SESSION['name'] ?>さんのアカウントページ</h1>
		<ul>
			<li><a href="update_name.php">ユーザー名を変更する</a></li>
			<li><a href="update_password.php">パスワードを変更する</a></li>
			<li><a href="resign.php">退会する</a></li>
		</ul>
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
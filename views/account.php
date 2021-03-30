<?php
	require_once '../models/function.php';
	
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
	<title>ユーザーアカウント情報</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/styles.css">
</head>

<body>
	<header>
		<h1>おごりあい</h1>
		<nav>
			<ul>
				<li><a href="mypage.php">マイページ</a></li>
				<li><a href="account.php">アカウント</a></li>
				<li><a href="../logout.php">ログアウト</a></li>
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
</body>

</html>
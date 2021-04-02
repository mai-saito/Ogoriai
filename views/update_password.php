<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	
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
	<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
	<header>
		<h1><a href="../index.html">おごりあい</a></h1>
		<nav>
			<ul>
				<li><a href="mypage.php">マイページ</a></li>
				<li><a href="account.php">アカウント</a></li>
				<li><a href="../logout.php">ログアウト</a></li>
			</ul>
		</nav>
	</header>
	<main class="update-password">
		<form action="../update_password_comp.php" method="post">
			<table>
				<tr>
					<th><label for="current_password">現在のパスワード：</label></th>
					<td><input type="text" name="current_password" id="current_password"></td>
				</tr>
				<tr>
					<th><label for="new_password">新しいパスワード：</label></th>
					<td><input type="text" name="new_password" id="new_password"></td>
				</tr>
				<tr>
					<th><label for="new_password2">新しいパスワード（再入力）：</label></th>
					<td><input type="text" name="new_password2" id="new_password2"></td>
				</tr>
				<tr>
					<th><input type="submit" value="変更する"></th>
				</tr>
			</table>
		</form>
	</main>
</body>
</html>
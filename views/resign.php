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
	<title>退会</title>
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
	<main class="resign">
		<p><?php echo $_SESSION['name'] ?>さん、本当に退会されますか？</p>
		<form action="../resign_comp.php" method="POST">
			<table>
				<tr>
					<th><label for="password">パスワード：</label></th>
					<td><input type="password" name="password" id="password"></td>
				</tr>
				<!-- <tr>
					<th colspan="2"><input type="checkbox" name="check" id="0">　退会によって起こる影響</th>
				</tr>
				<tr>
					<th colspan="2"><input type="checkbox" name="check" id="0">　退会によって起こる影響その２</th>
				</tr> -->
				<tr>
					<th><input type="submit" value="退会する"></th>
				</tr>
			</table>
		</form>
	</main>
</body>
</html>
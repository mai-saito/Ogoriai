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
<?php if (isset($_SESSION['email'])): ?>
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
	<main class="resign">
		<p><?php echo $_SESSION['name'] ?>さん、本当に退会されますか？</p>
		<form action="resign_comp.php" method="POST">
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
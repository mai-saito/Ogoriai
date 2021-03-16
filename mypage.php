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
	<title>マイページ</title>
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
	<main class="mypage">
		<h1><?php echo $_SESSION['name'] ?>さんのページ</h1>
		<p>支払い金額を入力する</p>
		<p>グループを作る</p>
		<!-- 支払い金額入力フォーム -->
		<!-- <form action="#">

		</form> -->
		<!-- グループ作成フォーム -->
		<form action="create_group.php" method="POST">
			<p>新しいグループをつくる</p>
			<table>
				<tr>
					<th><label for="group_name">グループ名：</label></th>
					<td><input type="text" name="group_name" id="group_name"></td>
				</tr>
				<tr>
					<th><input type="submit" value="メンバーを決める"></th>
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
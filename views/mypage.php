<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';

	// セッション開始
	session_start();
	check_session('email');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>マイページ</title>
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
				<li><a href="mypage.php" class="mr-3">マイページ</a></li>
				<li><a href="account.php" class="mr-3">アカウント</a></li>
				<li><a href="../logout.php">ログアウト</a></li>
			</ul>
		</nav>
	</header>
	<main class="mypage">
		<h1 class="mb-5"><span><?php echo $_SESSION['name'] ?></span>さんのページ</h1>
		<section>
			<div id="group-list">
<?php include VIEWS_PATH.'/group_list.php' ?>
			</div>
			<form action="../create_group.php" method="POST" id="group-form" class="p-3">
				<p>新しいグループをつくる</p>
				<table>
					<tr class="form-group">
						<th><label for="group_name">グループ名：</label></th>
						<td><input type="text" name="group_name" id="group_name" class="form-control"></td>
					</tr>
					<tr>
						<th colspan="2" class="text-right"><input type="submit" value="メンバーを決める" class="btn btn-lg btn-primary mt-2"></th>
					</tr>
				</table>
			</form>
		</section>
	</main>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
	<script src="../script.js"></script>
</body>

</html>
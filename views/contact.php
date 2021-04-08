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
		<title>お問合せ</title>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/styles.css">
	</head>
	<body>
		<div id="wrapper">
			<header>
				<h1><a href="../index.html"><img src="../images/logo-sm.png" alt="ロゴ画像"></a></h1>
				<nav>
					<ul>
						<li><a href="mypage.php" class="mr-3">マイページ</a></li>
						<li><a href="account.php" class="mr-3">アカウント</a></li>
						<li><a href="../logout.php">ログアウト</a></li>
					</ul>
				</nav>
			</header>
			<main class="contact">
				<h1 class="mb-4">お問合せフォーム</h1>
				<form action="#" method="POST">
					<div class="p-3">
						<table>
							<tr>
								<th><label for="name">お名前：</label></th>
								<td><input type="text" name="name" id="name" value="<?php echo $_SESSION['name'] ?>" class="mb-3" readonly></td>
							</tr>					
							<tr class="form-group">
								<th><label for="title">件名：</label></th>
								<td><input type="text" name="title" id="title" class="form-control mb-3"></td>
							</tr>					
							<tr class="form-group">
								<th><label for="content">内容：</label></th>
								<td><textarea name="content" id="content" rows="10" class="form-control mb-4"></textarea></td>
							</tr>					
							<tr>
								<th colspan="2"><input type="submit" value="送信する" class="btn btn-lg btn-primary"></th>
							</tr>
						</table>
					</div>
				</form>
			</main>
			<footer>
				<div>
					<small>© 2021 Mai Saito.</small>
				</div>
				<div>
					<p><a href="faq.html" class="btn btn-lg btn-contact mr-3">よくあるご質問</a></p>
					<p><a href="contact.php"  class="btn btn-lg btn-contact mr-3">お問合せ</a></p>
					<p><a href="#wrapper"><img src="../images/page-top-nude.png" alt="ページトップへ移動"></a></p>
				</div>
			</footer>
		</div>
	</body>
	</html>	
<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once MODELS_PATH.'/notice.php';

	// セッション開始
	session_start();
	check_session('user_id');

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		// group_idの確認
		if (isset($_POST['group_id'])):
			$group_id = $_POST['group_id'];
		endif;
?>
<!DOCTYPE html>
	<html lang="ja">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>CSV出力</title>
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
			<main class="csv">
				<!-- お知らせセクション -->
				<div class="notice-container">
					<ul class="notice-list">
<?php foreach($notices as $notice): ?>
						<li>
							<form action="display_notice.php" method="POST">
								<input type="hidden" name="title" value="<?php echo $notice['notice_id'] ?>">
								<input type="submit" class="notice-title" value="<?php echo $notice['title'] ?>">
							</form>
						</li>
						<li class="notice-date"><?php echo $notice['date'] ?></li>
<?php endforeach; ?>
					</ul>
				</div>
				<!-- メインセクション -->
				<h1 class="mb-4">出力するCSVの種類を選んでください</h1>
				<form action="../csv_comp.php" method="POST">
					<div class="p-3">
						<table>
							<tr>
								<th><input type="radio" name="radio" id="radio" value="0" class="mr-3"></th>
								<td><label for="radio">このグループの支出</label></td>
							</tr>
							<tr>
								<th><input type="radio" name="radio" id="radio" value="1" class="mr-3"></th>
								<td><label for="radio">このグループでのあなたの支出</label></td>
							</tr>
							<tr>
								<th><input type="radio" name="radio" id="radio" value="2" class="mr-3 mb-3"></th>
								<td><label for="radio" class="mb-3">全てのグループにおけるあなたの支出</label></td>
							</tr>
							<tr>
								<input type="hidden" name="group_id" value="<?php echo $group_id ?>">
								<th colspan="2"><input type="submit" value="CSVを出力する" class="btn btn-lg btn-primary"></th>
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
		<script src="../js/script.js"></script>
	</body>
	</html>	
<?php		
	else:
		$pdo = null;
		exit('直接アクセス禁止です。');
	endif;
	$pdo = null;
?>
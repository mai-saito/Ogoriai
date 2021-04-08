<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';
	
	// セッション確認
	session_start();
	check_session('user_id');
	var_dump($_SESSION);

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		// group_idを取得する
		$group_id = null;
		if (isset($_POST['group_id'])):
			$group_id = $_POST['group_id'];
		endif;
			
			// グループメンバーの名前を取得する
			$members = get_member_names($pdo, $group_id);

			// グループ全体の合計金額を出す
			$group_total = get_group_total($pdo, $group_id);

			// user_groupテーブルから各メンバーの現在の繰越額を取得する
			$carryovers = get_carryover($pdo, $group_id);

			// グループの情報を取得する
			$group =  get_group_info($pdo, $group_id);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>清算</title>
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
					<li><img src="../images/bell-brown.png" alt="お知らせボタン" id="notice"></li>
					<li><a href="mypage.php" class="mr-3">マイページ</a></li>
					<li><a href="account.php" class="mr-3">アカウント</a></li>
					<li><a href="../logout.php" class="mr-3">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="settle-up">
			<section>
				<h1 class="mb-4">
					<img src="../images/avatars/group_avatars/<?php echo $group['group_avatar'] ?>" alt="グループアバター画像" class="rounded-circle">
					<span><?php echo $group['group_name'] ?></span>を清算する</span>
				</h1>
				<!-- <p><a href="#" class="btn btn-lg btn-block btn-primary">グループのメンバーに清算のお知らせをする</a></p> -->
				<form action="alert_settle_up.php" method="POST" class="settle-up-btn">
					<input type="hidden" name="group_id" value="<?php echo $group_id ?>">
					<input type="submit" value="グループのメンバーに清算のお知らせをする" class="btn btn-lg btn-block btn-primary">
					<span class="balloon">このボタンを押すとグループメンバーに清算のお願いがお知らせされます。</span>
				</form>
			</section>
			<section class="mb-5">
				<table id="member-table">			
					<tr><th>名前</th><th>支払った金額</th><th>繰越している金額</th><th>清算</th></tr>
<?php 
	foreach ($members as $member):
		foreach ($carryovers as $carryover):
			if ($member['user_id'] === $carryover['user_id']):
				$member_subtotal = is_null(get_user_total($pdo, $group_id, $member['user_id'])) ? $member_subtotal['member_subtotal'] : 0;
?>
					<tr>
						<td><?php echo $member['name'] ?></td>
						<td><?php echo $member_subtotal?>円</td>
						<td><?php echo $carryover['carryover'] ?>円</td>
						<td>未済</td>
					</tr>
<?php 
			endif;
		endforeach;
	endforeach; 
?>
				</table>
			</section>
			<section id="confirmation-section">
				<h1>清算完了のご確認</h1>
				<div class="mb-3">
							<p>※注意事項</p>
							<p>
								1. 清算ボタンを押す前にきっちりと清算をお願いします（アプリ上は現金による清算処理はできません）。<br>
								2. 清算後はグループの各メンバーの繰越金額が0円になります。<br>
							</p>
				</div>
				<form action="#" method="POST" id="settle-up-form">
					<table>
						<tr class="form-group">
							<td><input type="checkbox" name="checkbox" id="checkbox" value="2"> 他のグループメンバーは清算済みですか？</td>
						</tr>
						<tr class="form-group">
							<th colspan="2"><input type="submit" value="清算終了" class="btn btn-lg btn-primary"></th>
						</tr>
					</table>
				</form>
			</section>
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
<?php endif; ?>

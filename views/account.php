<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/notice.php';
	
	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// ユーザーのアバターを取得する
	$user = get_user_info($_SESSION['user_id'], $pdo);
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
	<div id="wrapper">
		<header>
			<h1><a href="../index.html"><img src="../images/logo-sm.png" alt="ロゴ画像"></a></h1>
			<nav>
				<ul>
<?php 
	// お知らせがあるか確認する
	$notices = get_notice($pdo, $_SESSION['user_id']);
	if (!$notices):
?>
						<li class="notice-icon"><img src="../images/bell-brown.png" alt="お知らせ" id="notice"></li>
<?php else: ?>
						<li class="notice-icon"><img src="../images/notice-bell-brown.png" alt="お知らせ" id="notice"></li>
<?php endif; ?>
					<li><a href="mypage.php" class="mr-3">マイページ</a></li>
					<li><a href="account.php" class="mr-3">アカウント</a></li>
					<li><a href="../logout.php">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="account">
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
			<h1 class="mb-4">
			<img src="../images/avatars/user_avatars/<?php echo $user['user_avatar'] ?>" alt="グループアバター画像" class="rounded-circle">
				<span><?php echo $_SESSION['name'] ?></span>さんのアカウントページ
			</h1>
			<section>
				<ul class="tab-list">
					<li class="btn btn-lg btn-secondary btn-form-list is-active" data-id="tab1">ユーザー名を変更</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab2">パスワードを変更</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab3">アバターを登録</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab4">退会する</li>
				</ul>
				<!-- 切り替え表示用タブ -->
				<div class="tab-content">
					<!-- ユーザー名変更タブ -->
					<div id="tab1" class="tab-panel name-form is-active">
						<h1>ユーザー名を変更する</h1>
						<p>現在のユーザー名：<span><?php echo $_SESSION['name'] ?></span> さん</p>
						<form action="../update_name_comp.php" method="post">
							<table>
								<tr class="form-group">
									<th><label for="name">新しいユーザー名：</label></th>
									<td><input type="text" name="name" id="name" class="form-control"></td>
									<td><input type="submit" value="変更する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
					</div>
					<!-- パスワード変更タブ -->
					<div id="tab2" class="tab-panel password-form">
						<h1>パスワードを変更する</h1>
						<form action="../update_password_comp.php" method="post">
							<table>
								<tr class="form-group">
									<th><label for="current_password">現在のパスワード：</label></th>
									<td><input type="password" name="current_password" id="current_password" class="form-control"></td>
								</tr>
								<tr class="form-group">
									<th><label for="new_password">新しいパスワード：</label></th>
									<td><input type="password" name="new_password" id="new_password" class="form-control"></td>
								</tr>
								<tr class="form-group">
									<th><label for="new_password2">新しいパスワード（再入力）：</label></th>
									<td><input type="password" name="new_password2" id="new_password2" class="form-control"></td>
									<td colspan="2"><input type="submit" value="変更する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
					</div>
					<!-- アバター変更タブ -->
					<div id="tab3" class="tab-panel user-avatar-form">
						<h1>アバターを変更する</h1>
						<form action="../update_avatar.php" method="post" enctype="multipart/form-data">
							<table>
								<tr>
									<th><label class="form-label" for="avatar">ファイルを選択：</label></th>
									<td><input type="file" name="avatar" class="form-control form-control-lg mb-3 p-1" id="avatar"></td>
								</tr>
								<tr><td><input type="submit" name="user_avatar" value="登録する" class="btn btn-lg btn-primary ml-3"></td></tr>
							</table>
						</form>
					</div>
					<!-- 退会タブ -->
					<div id="tab4" class="tab-panel resign-form">
						<h1>退会する</h1>
						<p><span><?php echo $_SESSION['name'] ?></span>さん、本当に退会されますか？</p>
						<div class="mb-3">
							<p>※注意事項</p>
							<p>
								1. 退会すると全てのデータが消去されます。<br>
								2. グループ内でのあなたの繰越金額は相殺されますので、清算してから退会することをおすすめします。<br>
							</p>
						</div>
						<form action="../resign_comp.php" method="POST">
							<table>
								<tr class="form-group">
									<th colspan="2"><input type="checkbox" name="checkbox" value="1" class="mb-3" onclick="handleCheck(event)">&nbsp;上記の注意事項を確認しました。</th>
								</tr>
								<tr class="form-group">
									<th><label for="password">パスワード：</label></th>
									<td><input type="password" name="password" id="password" class="form-control"></td>
									<td><input type="submit" value="退会する" id="resign-button" class="btn btn-lg btn-danger ml-3" disabled></td>
								</tr>
							</table>
						</form>
					</div>
				</div>
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
	<script src="../js/script.js"></script>
</body>

</html>
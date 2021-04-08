<?php 	
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// group_idを取得する
	if (isset($_SESSION['group_id'])) {
		$group_id = $_SESSION['group_id'];
	} else {
		$group_id = $_POST['group_id'];
	}

	// グループのgroup_nameを取得する
	$group = get_group_info($pdo, $group_id);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>メンバー選択</title>
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
		<main class="choose-member">
			<h1><span><?php echo $group['group_name'] ?></span> のメンバーを選んでください。</h1>
			<form action="choose_member.php" method="POST" id="search-form">
				<table class="mb-4">
					<tr class="form-group">
						<th><label for="user">名前もしくはメールアドレス：</label></th>
						<td><input type="text" name="user" id="user" class="form-control" size="50"></td>
						<td><button type="submit" name="selected_method" value="search" class="btn btn-block btn-lg btn-primary ml-3">検索する</button></td>
					</tr>
				</table>
			</form>
<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):

		// 検索フォームに文字が入力されているか確認する
		if (isset($_POST['user'])) {
			$user = $_POST['user'];
		} else {
			$user = '';
		}

		// usersテーブルにおいて名前かメールアドレスの一部であいまい検索を実行する
		$user = '%'.$user.'%';
		$sql = 'SELECT `user_id`, `name`, `email` FROM `users` WHERE `name` LIKE :name OR `email` LIKE :email';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':name', $user);
		$stmt -> bindParam(':email', $user);
		$stmt -> execute();
		$result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		if ($result):
?>
			<table class="member-table">
				<!-- <tr>
					<th>名前</th>
					<th>メールアドレス</th>
				</tr> -->
<?php 
	// あいまい検索結果で合致したユーザーの一覧を表示する
	foreach ($result as $user): 
?>
				<tr>
					<form action="../choose_member_comp.php" method="post">
						<td><input type="text" name="name" value="<?php echo $user['name'] ?>"></td>
						<td><input type="text" name="email" value="<?php echo $user['email'] ?>"></td>
						<input type="hidden" name="user_id" value="<?php echo $user['user_id'] ?>">
						<td class="pr-2"><input type="submit" name="add_member" value="追加" class="btn btn-primary"></td>
					</form>
				</tr>
<?php endforeach; ?>
			</table>
<?php	else: ?>		
		<p>
			ユーザーが存在しません。<br>
			もう一度検索してください。
		</p>
<?php endif; ?>
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
<?php
	$stmt =null;
	endif;
	$pdo = null;
?>	
</body>
</html>
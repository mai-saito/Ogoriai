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
	if (isset($_POST['group_id'])) {
		$group_id = $_POST['group_id'];
	} else {
		$group_id = $_SESSION['group_id'];
	}

	// セッションのgroup_idとgroup_nameを変更する
	$group = get_group_info($pdo, $group_id);
	set_session('group_id', $group_id);
	set_session('group_name', $group['group_name']);
	var_dump($_SESSION);

	// グループメンバーの名前を取得する
	$members = get_member_names($pdo, $group_id);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>グループ管理</title>
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
		<main class="group-account">
			<h1 class="mb-4">
				<img src="../images/avatars/group_avatars/<?php echo $group['group_avatar'] ?>" alt="グループアバター画像" class="rounded-circle">
				<span><?php echo $group['group_name'] ?></span>の管理ページ
			</h1>
			<section>
				<ul class="tab-list">
					<li class="btn btn-lg btn-secondary btn-form-list is-active" data-id="tab1">グループ基本情報</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab2">グループ名の変更</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab3">アバターの登録</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab4">メンバーの追加</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab5">メンバーの削除</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab6">グループの削除</li>
				</ul>
				<!-- 切り替え表示用タブ -->
				<div class="tab-content">
					<!-- グループ名変更タブ -->
					<div id="tab1" class="tab-panel group-info is-active">
						<h1><span><?php echo $group['group_name'] ?></span>の基本情報</h1>
						<table>
							<tr>
								<th>メンバー：</th>
								<td>
								<?php 
	if ($members){
		foreach ($members as $member) {
			echo $member['name'],'<br>';
		}
	} else {
		echo 'メンバーがいません。';
	}
?>
							</td>	
						</tr>
							<tr>
								<th>端数処理：</th>
								<td>四捨五入</td>
							</tr>
							<tr>
								<th>わりかん方法：</th>
								<td>等分</td>
							</tr>
						</table>
					</div>
					<!-- グループ名変更タブ -->
					<div id="tab2" class="tab-panel group-name-form">
						<h1>グループ名を変更する</h1>
						<p>現在のグループ名：<span><?php echo $group['group_name'] ?></span></p>
						<form action="../update_group_name.php" method="post">
							<table>
								<tr class="form-group">
									<th><label for="name">新しいグループ名：</label></th>
									<td><input type="text" name="group_name" id="name" class="form-control"></td>
									<input type="hidden" name="group_id" value="<?php echo $group_id ?>">
									<td><input type="submit" value="変更する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
					</div>
					<!-- グループアバター変更タブ -->
					<div id="tab3" class="tab-panel group-avatar-form">
						<h1>グループのアバターを変更する</h1>
						<form action="../update_avatar.php" method="post" enctype="multipart/form-data">
							<table>
								<tr>
									<th><label class="form-label" for="avatar">ファイルを選択：</label></th>
									<td><input type="file" name="avatar" class="form-control form-control-lg mb-3 p-1" id="avatar"></td>
									<input type="hidden" name="group_id" value="<?php echo $group_id ?>">
								</tr>
								<tr><td><input type="submit" value="登録する" name="group_avatar" class="btn btn-lg btn-primary ml-3"></td></tr>
							</table>
						</form>
					</div>
					<!-- メンバー追加タブ -->
					<div id="tab4" class="tab-panel add-member-form">
						<h1>メンバーの追加をする</h1>
						<form action="group_account.php" method="POST" id="search-form">
							<table class="mb-4">
								<tr class="form-group">
									<th><label for="user">名前もしくはメールアドレス：</label></th>
									<td><input type="text" name="user" id="user" class="form-control" size="50"></td>
									<td><button type="submit" name="select_member" value="search" class="btn btn-block btn-lg btn-primary ml-3">検索する</button></td>
								</tr>
							</table>
						</form>
<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		// 検索フォームに文字が入力されているか確認する
		if (isset($_POST['user']) && strlen($_POST['user'])):
			$user = $_POST['user'];
		
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
<?php 
	// あいまい検索結果で合致したユーザーの一覧を表示する
	foreach ($result as $user): 
?>
								<tr>
									<form action="../choose_member_comp.php" method="post">
										<td><input type="text" name="name" value="<?php echo $user['name'] ?>" class="form-control"></td>
										<td><input type="text" name="email" value="<?php echo $user['email'] ?>" class="form-control"></td>
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
<?php else: ?>
		<p>検索するための入力が必要です。</p>
<?php endif; ?>
<?php endif; ?>
					</div>
					<!-- メンバー削除タブ -->
					<div id="tab5" class="tab-panel remove-member-form">
						<h1>メンバーを削除する</h1>
						<table class="member-table mb-3">
<?php
	// グループのgroup_nameを取得する
	$members = get_member_names($pdo, $group_id);
	if ($members):
		foreach ($members as $member):
?>
				<tr class="form-group">
					<form action="../remove_member_comp.php" method="POST">
						<td><input type="text" name="name" value="<?php echo $member['name'] ?>" class="form-control"></td>
						<input type="hidden" name="user_id" value="<?php echo $member['user_id'] ?>">
						<input type="hidden" name="group_id" value="<?php echo $group_id?>">
						<td class="pr-2"><input type="submit" name="delete" value="削除" class="btn btn-danger btn-expense"></td>
					</form>
				</tr>
<?php 
		endforeach; 
	else:  
		delete_group($pdo, $group_id);
		$pdo = null;
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/mypage.php');
	endif; ?>
			</table>
					</div>
					<!-- グループ削除タブ -->
					<div id="tab6" class="tab-panel delete-group-form">
						<h1>グループを削除する</h1>
						<p>本当に<span><?php echo $group['group_name'] ?></span>を消去しますか？</p>
						<form action="../delete_group_comp.php" method="post">
							<table>
								<tr>
									<td colspan="2"><input type="checkbox" name="checkbox" value="0" class="mb-4" onclick="handleCheck(event)">グループの削除後は支出データの取得ができなくりますが、よろしいですか？</td>
								</tr>
								<tr>
									<input type="hidden" name="group_id" value="<?php echo $group_id ?>">
									<input type="hidden" name="group_name" value="<?php echo $group['group_name'] ?>">
									<td><input type="submit" id="delete-button" value="グループを削除する" class="btn btn-lg btn-danger" disabled></td>
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
	<script src="../script.js"></script>
</body>

</html>
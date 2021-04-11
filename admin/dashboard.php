<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/notice.php';
	
	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// ユーザの情報を取得する
	$admin_user = get_user_info($_SESSION['user_id'], $pdo);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>管理画面</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>

<body>
	<div id="wrapper">
		<header>
			<h1><a href="index.html"><img src="../images/logo-sm.png" alt="ロゴ画像"></a></h1>
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
					<li class="mr-1"><a href="../views/account.php"><img src="../images/avatars/user_avatars/<?php echo $admin_user['user_avatar'] ?>" alt="管理者のアバター" class="avatar rounded-circle"></a></li>
					<li class="mr-3"><a href="../views/account.php"><span><?php echo $admin_user['name'] ?></span>さん</a></li>
					<li><a href="admin_logout.php">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="dashboard">
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
			<h1 class="mb-4">おごりあい管理画面</h1>
			<section>
				<ul class="tab-list">
					<li class="btn btn-lg btn-secondary btn-form-list is-active" data-id="tab1">支出データの取得</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab2">お知らせの発行</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab3">ユーザーの検索</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab4">グループの検索</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab5">管理者の追加</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab6">パスワード再発行</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab7">グループ強制削除</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab8">ユーザー強制削除</li>
				</ul>
				<!-- 切り替え表示用タブ -->
				<div class="tab-content">
					<!-- 支出データ取得タブ -->
					<div id="tab1" class="tab-panel expense-table is-active">
						<h1 class="mb-4">支出データを取得する</h1>
						<!-- 支出項目を選択 -->
						<form action="dashboard.php" method="POST" class="mb-4">
							<table id="table-filter">
								<tr class="form-group">
									<th><label for="group_id">グループID：</label></th>
									<td><input type="text" name="group_id" id="group_id" class="form-control"></td>
									<th><label for="user_id" class="ml-4">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control"></td>
									<td><input type="submit" value="テーブルを出力する" class="btn btn-lg btn-primary ml-4"></td>
								</tr>
							</table>
						</form>
<?php 
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if ((isset($_POST['group_id']) && strlen($_POST['group_id'])) && (isset($_POST['user_id']) && strlen($_POST['user_id']))) {
			// グループIDとユーザーID両方での検索
			$result = get_expense_list($pdo, $_POST['group_id'], $_POST['user_id']);
		} else if ((isset($_POST['group_id']) && strlen($_POST['group_id'])) && !(isset($_POST['user_id']) && strlen($_POST['user_id']))) {
			// グループIDのみでの検索
			$result = get_expense_list_by_group($pdo, $_POST['group_id']);
		} else if ((isset($_POST['user_id']) && strlen($_POST['user_id'])) && !(isset($_POST['group_id']) && strlen($_POST['group_id']))) {
			// ユーザーIDのみでの検索
			$result = get_expense_list_by_user($pdo, $_POST['user_id']);
		} else {
			exit('グループIDおよびユーザーIDを入力して検索してください。');
		}
?>
						<!-- 支出テーブル一覧 -->
						<table class="dashboard-table">
							<tr><th>支出ID</th><th>アイテム</th><th>金額（円）</th><th>支払ったユーザー</th><th>ユーザーID</th><th>グループ名</th><th>グループID</th><th>日付</th></tr>
<?php
		if ($result):
			foreach($result as $value):
				// 退会済みのユーザーは「退会済みユーザー」、そのIDは「ー」と表示する
				$member = !is_null($value['name']) ? $value['name'] : '退会済みユーザー';
				$member_id = !is_null($value['user_id']) ? $value['user_id'] : 'ー';
?>
<!-- テーブル入力 -->
							<tr>
								<td><?php echo $value['expense_id'] ?></td>
								<td><?php echo $value['item'] ?></td>
								<td><?php echo $value['amount'] ?></td>
								<td><?php echo $member ?></td>
								<td><?php echo $member_id ?></td>
								<td><?php echo $value['group_name'] ?></td>
								<td><?php echo $value['group_id'] ?></td>
								<td><?php echo $value['date'] ?></td>
							</tr>
<?php endforeach; ?>
<?php else: ?>
							<td colspan="8">データが存在しません。</td>
<?php endif; ?>
						</table>
<?php endif; ?>
					</div>

					<!-- お知らせの発行タブ -->
					<div id="tab2" class="tab-panel notice-form">
						<h1 class="mb-4">お知らせを発行する</h1>
						<form action="post.php" method="POST">
							<table>				
								<tr class="form-group">
									<th><label for="title">件名：</label></th>
									<td colspan="2"><input type="text" name="title" id="title" class="form-control mb-3"></td>
								</tr>					
								<tr class="form-group">
									<th><label for="content">内容：</label></th>
									<td colspan="2"><textarea name="content" id="content" rows="10" class="form-control mb-4"></textarea></td>
								</tr>		
								<tr class="form-group">
									<th class="form-group form-row"><label for="recipient">送信先：</label></th>
									<td class="col">
										<select name="recipient" id="recipient" class="form-control">
											<option>選択してください</option>
											<option value="0">全てのユーザー</option>
											<option value="1">指定のグループ</option>
											<option value="2">指定のユーザー</option>
										</select>
									</td>
									<td class="col">
										<input type="text" name="selected_recipient" class="select-recipient form-control" placeholder="グループIDもしくはユーザーIDを入力">
									</td>
								</tr>			
								<tr>
									<input type="hidden" name="name" id="name" value="<?php echo $_SESSION['name'] ?>">
									<th colspan="2"><input type="submit" value="送信する" class="btn btn-lg btn-primary"></th>
								</tr>
							</table>
						</form>
					</div>

					<!-- ユーザー検索タブ -->
					<div id="tab3" class="tab-panel user-search-form">
						<h1 class="mb-4">ユーザーを検索する</h1>
						<form action="dashboard.php" method="POST">
							<table class="user-filter">
								<tr class="form-group">
									<th><label for="user_id">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control"></td>
									<th><label for="keyword" class="ml-3">キーワード：</label></th>
									<td><input type="text" name="keyword" id="keyword" class="form-control" size="40" placeholder="名前やメールアドレス"></td>
									<td><input type="checkbox" name="admin" id="admin" class="ml-3 mr-1"></td>
									<th><label for="admin">管理者のみ</label></th>
									<td><input type="submit"value="検索する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
<?php 
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
			// user_idがPOSTされている場合、user_idからユーザー情報を取得する
			if (isset($_POST['user_id']) && strlen($_POST['user_id'])):
				$user_id = $_POST['user_id'];
				$users = null;
				$users[] = get_user_info($user_id, $pdo);
				var_dump($users);
			else:
				// 名前もしくはメールアドレスからユーザー情報をあいまい検索する
				if (isset($_POST['keyword']) && strlen($_POST['keyword'])):
					$keyword = $_POST['keyword'];
					$users = null;
					$users = get_user_info_with_keyword($pdo, $keyword); 
					var_dump($users);
				else:
?>
	<p>IDもしくはキーワードの入力が必要です。</p>
<?php
				endif;
			endif;				
			if (isset($users)):
?>
							<table class="dashboard-table">
								<tr><th>ユーザーID</th><th>名前</th><th>メールアドレス</th></tr>
<?php 
		foreach ($users as $user): 
			// 管理者のみの指定がある場合
			if (isset($_POST['admin'])):
				if ($user['admin'] == 1):
?>

								<tr>
									<td><?php echo $user['user_id'] ?></td>
									<td><?php echo $user['name'] ?></td>　
									<td><?php echo $user['email'] ?></td>　
								</tr>
<?php endif; ?>
<?php else: ?>
								<tr>
									<td><?php echo $user['user_id'] ?></td>
									<td><?php echo $user['name'] ?></td>　
									<td><?php echo $user['email'] ?></td>　
								</tr>
<?php endif; ?>
<?php endforeach; ?>
							</table>
<?php else: ?>
		<p>
			ユーザーが存在しません。<br>
			もう一度検索してください。
		</p>
<?php endif; ?>
<?php endif; ?>
					</div>

					<!-- グループ検索タブ -->
					<div id="tab4" class="tab-panel group-search-form">
						<h1 class="mb-4">グループからユーザーを検索する</h1>
						<form action="dashboard.php" method="POST">
							<table class="group-filter">
								<tr class="form-group">
									<th><label for="group_id">グループID：</label></th>
									<td><input type="text" name="group_id" id="group_id" class="form-control"></td>
									<th><label for="group＿name" class="ml-3">グループ名：</label></th>
									<td><input type="text" name="group_name" id="group_name" class="form-control" size="40" placeholder="グループ名"></td>
									<td><input type="submit"value="検索する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
<?php 
	// グループ情報で検索する
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		// グループのメンバーの名前とuser_idを取得する
		if (isset($_POST['group_id']) && strlen($_POST['group_id'])):
			$group_id = $_POST['group_id'];
			$users = null;
			$users = get_member_names($pdo, $group_id);
		else:
			// グループ名からあいまい検索をかける
			if (isset($_POST['group_name']) && strlen($_POST['group_name'])):
				$group_name = $_POST['group_name'];
				$users = null;
				$users = get_member_names_with_keyword($pdo, $group_name);
			else:
?>
		<p>IDもしくはグループ名の入力が必要です。</p>
<?php
			endif;
		endif;

		if (isset($users)):
?>
						<table class="dashboard-table">
							<tr><th>ユーザーID</th><th>名前</th><th>メールアドレス</th></tr>
<?php foreach ($users as $user): ?>
								<tr>
									<td><?php echo $user['user_id'] ?></td>
									<td><?php echo $user['name'] ?></td>　
									<td><?php echo $user['email'] ?></td>　
								</tr>
<?php endforeach; ?>
						</table>
<?php else: ?>
		<p>
			ユーザーが存在しません。<br>
			もう一度検索してください。
		</p>
<?php endif; ?>
<?php endif; ?>
					</div>

					<!-- 管理者の追加タブ -->
					<div id="tab5" class="tab-panel add-admin-form">
						<h1 class="mb-4">管理者を追加する</h1>
						<!-- 管理者権限を付与するユーザーを選択 -->
						<form action="dashboard.php" method="POST">
							<table class="user-filter">
								<tr class="form-group">
									<th><label for="user_id">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control"></td>
									<td><input type="submit" value="検索する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
<?php 
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if (isset($_POST['user_id']) && strlen($_POST['user_id'])):
			$user_id = $_POST['user_id'];
			// user_idからユーザーの名前とメールアドレスを取得する
			$user = null;
			$user = get_user_info($user_id, $pdo);
			if ($user):
?>
						<form action="add_admin.php" method="POST">
							<table class="dashboard-table">
								<tr><th>ユーザーID</th><th>ユーザー名</th><th colspan="2">メールアドレス</th></tr>
								<tr>
									<td><?php echo $user_id ?></td>　
									<td><?php echo $user['name'] ?></td>　
									<td><?php echo $user['email'] ?></td>　
									<input type="hidden" name="name" value="<?php echo $user['name'] ?>">
									<input type="hidden" name="user_id" value="<?php echo $user_id ?>">
									<td class="btn-cell"><input type="submit" value="管理者に追加する" class="btn btn-lg btn-primary m-1"></td>
								</tr>
							</table>
						</form>
<?php else: ?>
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

					<!-- パスワード再発行タブ -->
					<div id="tab6" class="tab-panel password-form">
						<h1 class="mb-4">パスワードを再発行する</h1>
					</div>
					
					<!-- グループ強制削除タブ -->
					<div id="tab7" class="tab-panel delete-group-form">
						<h1 class="mb-4">グループを強制削除する</h1>
							<!-- 削除するグループを選択 -->
							<form action="dashboard.php" method="POST">
							<table class="group-filter">
								<tr class="form-group">
									<th><label for="group_id">グループID：</label></th>
									<td><input type="text" name="group_id" id="group_id" class="form-control"></td>
									<td><input type="submit" value="検索する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
<?php 
	// group_idからユーザーの名前とメールアドレスを取得する
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if (isset($_POST['group_id']) && strlen($_POST['group_id'])):
			$group_id = $_POST['group_id'];
			// group_idからグループ情報を取得する
			$group = null;
			// $group = get_group_info($pdo, $group_id);
			$stmt = null;
			$sql ='SELECT * FROM groups WHERE group_id = :group_id';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':group_id', $group_id);
			$stmt -> execute();
			$group = $stmt -> fetch(PDO::FETCH_ASSOC);
			if (isset($group)):
?>
						<form action="delete_group.php" method="POST">
							<table class="dashboard-table">
							<tr><th>グループID</th><th colspan="2">グループ名</th></tr>
								<tr>
									<td><?php echo $group['group_id'] ?></td>　
									<td><?php echo $group['group_name'] ?></td>　
									<input type="hidden" name="group_name" value="<?php echo $group['group_name']  ?>">
									<input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>">
									<td class="btn-cell"><input type="submit" value="グループを削除する" class="btn btn-lg btn-danger m-1"></td>
								</tr>
							</table>
						</form>
<?php else: ?>
		<p>
			グループが存在しません。<br>
			もう一度検索してください。
		</p>
<?php endif; ?>		
<?php else: ?>
		<p>検索するための入力が必要です。</p>
<?php endif; ?>
<?php endif; ?>
					</div>

					<!-- ユーザー強制削除タブ -->
					<div id="tab8" class="tab-panel delete-user-form">
						<h1 class="mb-4">ユーザーを強制削除する</h1>
						<!-- 削除するユーザーを選択 -->
						<form action="dashboard.php" method="POST">
							<table class="user-filter">
								<tr class="form-group">
									<th><label for="user_id">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control"></td>
									<td><input type="submit" value="検索する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
<?php 
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if (isset($_POST['user_id']) && strlen($_POST['user_id'])):
			$user_id = $_POST['user_id'];
			// user_idからユーザーの名前とメールアドレスを取得する
			$user = null;
			$user = get_user_info($user_id, $pdo);
			if ($user):
?>
						<form action="delete_user.php" method="POST">
							<table class="dashboard-table">
								<tr><th>ユーザーID</th><th>ユーザー名</th><th colspan="2">メールアドレス</th></tr>
								<tr>
									<td><?php echo $user_id ?></td>　
									<td><?php echo $user['name'] ?></td>　
									<td><?php echo $user['email'] ?></td>　
									<input type="hidden" name="name" value="<?php echo $user['name'] ?>">
									<input type="hidden" name="user_id" value="<?php echo $user_id ?>">
									<td class="btn-cell"><input type="submit" value="ユーザーを削除する" class="btn btn-lg btn-danger m-1"></td>
								</tr>
							</table>
						</form>
<?php else: ?>
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
				</div><!-- タブコンテンツ終了 -->
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
	<script src="script.js"></script>
</body>

</html>
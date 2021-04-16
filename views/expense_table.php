<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/notice.php';
	
	// セッション確認
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

	// セッションにgroup_nameを設定する
	$group = get_group_info($pdo, $group_id);
	set_session('group_name', $group['group_name']);

	// 端数処理用配列
	$payment_methods = array('人数で等分', '％で分ける');

	// 端数処理用配列
	$rounding = array('四捨五入', '切り捨て', '切り上げ');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>グループ支出一覧</title>
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
	<main class="expense-table">
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
<?php
	// グループメンバーの名前を取得する
	$members = get_member_names($pdo, $group_id);

	// グループ全体の合計金額を出す
	$group_total = get_group_total($pdo, $group_id);

	// ログインしているユーザーの支払額を算出する
	$subtotal = get_user_total($pdo, $group_id, $_SESSION['user_id']);
	if (!$subtotal) {
		$subtotal = 0;
	} else {
		$subtotal = $subtotal['subtotal'];
	}
	
	// 各メンバーの支払額を算出する
	$member_subtotals = get_user_totals($pdo, $group_id);

	// expenseテーブルのデータを取得する
	$result = get_expense_list_by_group($pdo, $group_id);

	// 一覧結果を使ってテーブルを作成する
	if ($result):
?>
		<!-- メインセクション -->
		<section>
			<h1>		
				<img src="../images/avatars/group_avatars/<?php echo $group['group_avatar'] ?>" alt="グループアバター画像" class="rounded-circle avatar">
				<span><?php echo $group['group_name'] ?></span> 支出一覧
			</h1>
			<ul>
				<li><p id="modal-button" class="btn btn-lg btn-primary mr-3">グループの詳細</p></li>
				<li>
					<form action="settle_up.php" method="POST" class="m-0">
						<input type="hidden" name="group_id" value="<?php echo $group_id ?>">
						<input type="submit" value="清算する" class="btn btn-lg btn-primary mr-3">
					</form>
				</li>
				<li>
					<form action="csv.php" method="POST" class="m-0">
						<input type="hidden" name="group_id" value="<?php echo $group_id ?>">
						<input type="submit" value="CSVを出力する" class="btn btn-lg btn-primary mr-3">
					</form>
				</li>
				<li><a href="mypage.php" class="btn btn-lg btn-primary">マイページに戻る</a></li>
			</ul>
		</section>
			<!-- 支出テーブル一覧 -->
		<table class="mb-3">
<?php foreach ($result as $value): ?>
			<tr>
				<form action="../edit_expense.php" method="POST" class="expense-form">
					<td class="bigger-input"><input type="text" name="item" value="<?php echo $value['item'] ?>"></td>
					<td class="smaller-input"><input type="text" name="amount" value="<?php echo $value['amount'] ?>"></td>
					<td class="smaller-input">
						<select name="name">
<?php $name = (is_null($value['name'])) ? '退会済みユーザー' : $value['name']; ?>
							<option value="<?php echo $name ?>"><?php echo $name ?></option>
<?php 
	foreach ($members as $member):
		if ($value['user_id'] !== $member['user_id']):
?>
							<option value="<?php echo $member['name'] ?>"><?php echo $member['name'] ?></option>
<?php endif; ?>
<?php endforeach; ?>
						</select>
					</td>
					<td class="smaller-input"><input type="text" name="date" value="<?php echo $value['date'] ?>" readonly></td>
					<input type="hidden" name="group_id" value="<?php echo $value['group_id'] ?>">
					<input type="hidden" name="expense_id" value="<?php echo $value['expense_id'] ?>">
					<td><input type="submit" name="update" value="変更" class="btn btn-primary btn-expense"></td>
					<td><input type="submit" name="delete" value="削除" class="btn btn-danger btn-expense"></td>
				</form>
			</tr>
<?php endforeach; ?>
		</table>
		<!-- 合計金額テーブル -->
		<table>
			<tr>
				<th>グループ合計：</th>
				<td><?php echo $group_total['group_total'] ?>円</td>
			</tr>
			<tr>
				<th>あなたの支払い合計：</th>
				<td><?php echo $subtotal ?>円</td>
			</tr>
		</table>
<?php else: ?>
		<section>
			<h1><span><?php echo $group['group_name'] ?></span> 支出一覧</h1>
			<ul>
				<li><p id="modal-button" class="btn btn-lg btn-primary mr-3">グループ管理</p></li>
				<li><a href="mypage.php" class="btn btn-lg btn-primary">マイページに戻る</a></li>
			</ul>
		</section>
		<p class="no-expense">支出データが存在しません。</p>
<?php endif; ?>
			<!-- グループ詳細モーダル -->
			<div class="modal" id="setting-modal">
				<div class="modal-content">
					<span class="close" id="close-setting">&times;</span>
					<div>
						<table>
							<tr>
								<th>グループ名：</th>
								<td><?php echo $group['group_name'] ?></td>
							</tr>
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
								<th>わりかん方法：</th>
								<td><?php echo $payment_methods[$group['payment_method']] ?></td>
							</tr>						
							<tr>
								<th>端数の処理方法：</th>
								<td><?php echo $rounding[$group['rounding']] ?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
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
// 管理画面のタブ切り替え処理
document.addEventListener('DOMContentLoaded', handleTabs());
	
function handleTabs() {
	const tabButtons = document.querySelectorAll('.btn-form-list');
	const tabContents = document.querySelectorAll('.tab-panel');

	// クッキーのハンドリング処理
	let tab = handleCookie(document.cookie);

	// クッキーにタブの保存情報がある場合の処理
	if (tab['tab']) {
		let tabNumber = tab['tab'];

		// タブボタンとタブのis-activeクラスを削除する
		for (let i = 0; i < tabButtons.length; i++) {
			tabButtons[i].classList.remove('is-active');
			tabContents[i].classList.remove('is-active');
		}

		// クッキーからタブボタンを取得する
		let selectedTabButton =　document.querySelector('[data-id='+tabNumber+']');
		if (selectedTabButton) {
			// クッキーから取得したタブボタンをアクティブにする
			selectedTabButton.classList.add('is-active');
		
			// クッキーから取得したタブをアクティブにする
			let selectedTabContent = document.getElementById(tabNumber);
			selectedTabContent.classList.add('is-active');
		} else {
			// クッキーから取得したタブが存在しない場合は、最初のタブをアクティブにする
			tabButtons[0].classList.add('is-active');
			tabContents[0].classList.add('is-active');
		}
	} 

	// クリックイベントの処理
	for (let i = 0; i < tabButtons.length; i++) {
		tabButtons[i].addEventListener('click', function(event) {
			let tabButton = event.currentTarget;
			let tabContent = document.getElementById(tabButton.dataset.id);

			// すべてのタブボタンのis-activeクラスを削除する
			for (let i = 0; i < tabButtons.length; i++) {
					tabButtons[i].classList.remove('is-active');
					tabContents[i].classList.remove('is-active');
			}
			// クリックしたタブボタンにis-activeクラスを追加する
			tabButton.classList.add('is-active');

			// タブの保存情報を保存しておく
			setCookie('tab', tabButton.dataset.id, 300, '/ogoriai/views');

			// タブを表示する
			if(tabContent !== null) {
					tabContent.classList.add('is-active');
			}
		});				
	}
}

// クッキーを設定する
function setCookie(key, value, time, path) {
	let expiresAt = "max-age=" + time;
	document.cookie = key + "=" + value + ";" + expiresAt + ";" + path + ";";
	return document.cookie;
}

// クッキーを連想配列に変換する
function handleCookie(cookie) {
	let array = new Array();
	if (cookie != '') {
		let cookies = cookie.split( '; ' );
		for(let i = 0; i < cookies.length; i++ ) {
			let value = cookies[i].split('=');
		
			// クッキーの名前をキーとして 配列に追加する
			array[value[0]] = decodeURIComponent(value[1]);
		}
	}
	return array;
}

// マイページのグループタブ切り替え
const groupTabButtons = document.querySelectorAll('.group-list-tab .tab');
const groupTabContents = document.querySelectorAll('.group-tab-panel');

function handleLoad() {
	groupTabButtons[0].classList.add('is-active');
	groupTabContents[0].classList.add('is-active');
}

for (let i = 0; i < groupTabButtons.length; i++) {
	// 画面ロード時のタブ表示処理（先頭のグループを表示する）
	groupTabButtons[i].addEventListener('click', handleLoad());

	// クリックされた時のタブ切り替え処理
	groupTabButtons[i].addEventListener('click', function(event) {
			let groupTabButton = event.currentTarget;
			let groupTabContent = document.getElementById(groupTabButton.dataset.id);

			// すべてのタブボタンのis-activeクラスを削除する
			for (let i = 0; i < groupTabButtons.length; i++) {
				groupTabButtons[i].classList.remove('is-active');
				groupTabContents[i].classList.remove('is-active');
			}
			// クリックしたタブボタンにis-activeクラスを追加する
			groupTabButton.classList.add('is-active');

			// タブを表示する
			if(groupTabContent !== null) {
				groupTabContent.classList.add('is-active');
			}
	});
}

// 確認事項のチェック処理
function handleCheck(event) {
	let button;
	// どのチェックボックスがクリックされたかの判定をする
	if (event.currentTarget.value == 0) {
		button = document.querySelector('#delete-button');
	} else if (event.currentTarget.value == 1) {
		button = document.querySelector('#resign-button');
	} else { 
		console.log(event.currentTarget);
	}

	// クリック後の処理
	if (event.currentTarget.checked) {
		button.removeAttribute('disabled');
	} else {
		button.setAttribute('disabled', true);
	}
}

// expense_tableのモーダル操作
const modalButtons = document.querySelectorAll('.expense-table #modal-button');
const modals = document.querySelectorAll('.expense-table #setting-modal');
const close = document.querySelectorAll('.expense-table .modal .close');

// モーダルを開く
modalButtons.forEach(function (modalButton) {
	modals.forEach(function (modal) {
		modalButton.onclick = function () {
			if (modal.style.display === 'block') {
				modal.style.display = 'none';
			} else {
				modal.style.display = 'block';
			}
		}
	});
});

// クローズボタンでモーダルを閉じる
close.forEach(function (button) {
	modals.forEach(function (modal) {
		button.onclick = function () {
			modal.style.display = 'none';
		}
	});
});

// ウィンドウをクリックしてモーダルを閉じる
modals.forEach(function (modal) {
	modal.onclick = function () {
		if (modal.style.display === 'block') {
			modal.style.display = 'none';
		}
	}
});

// お知らせハンドリング処理
const noticeIcon = document.querySelector('.notice-icon');
const noticeContainer = document.querySelector('.notice-container');

noticeIcon.addEventListener('click', function() {
	if (noticeContainer.style.display == 'none') {
		noticeContainer.style.display = 'block';
	} else {
		noticeContainer.style.display = 'none';
	}
});



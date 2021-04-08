// クリックイベント処理（マイページタブ切り替え）
const groupTabs = document.querySelectorAll('.group-list-tab .group-tab');

// クッキーにgroup_idを設定してリロードする
function handleClick(event) {
	let cookie = setCookie('group_id', event.target.id, '500', '/');
	window.location.reload();
}

// リロード（読み込み）ごとにタブのスタイルを切り替える
window.onload = function() {
	// クッキーを配列に変換する
	let cookies = document.cookie.split('; ', 1);
	cookies.forEach(function(cookie) {
		let groupId = cookie.split('=')[1];

		// クッキーのgroup_idとidが一致するタブを探す
		groupTabs.forEach(function (groupTab) {
			groupTab.classList.remove('selected-group-tab');
			if (groupTab.id == groupId) {
				groupTab.classList.remove('tab');
				groupTab.classList.add('selected-group-tab');
			} 
		});	
	});
}

// クッキーを設定する
function setCookie(key, value, time, path) {
	let expiresAt = "max-age=" + time;
	document.cookie = key + "=" + value + ";" + expiresAt + ";" + path + ";";
	return document.cookie;
}

// マイページ、アカウント、グループアカウントページのタブ切り替え処理
document.addEventListener('DOMContentLoaded', handleTabs());
	
function handleTabs() {
	const tabButtons = document.querySelectorAll('.btn-form-list');
	const tabContents = document.querySelectorAll('.tab-panel');
	const searchForm = document.querySelector('#search-form');

	// 要素の数の分だけループ処理をして値を取り出す
	for (let i = 0; i < tabButtons.length; i++) {
		tabButtons[i].addEventListener('click', function(event) {
			if (event.target.dataset.id === 'tab4') {
				// すべてのタブボタンのis-activeクラスを削除する
				for (let i = 0; i < tabButtons.length; i++) {
					tabButtons[i].classList.remove('is-active');
					tabContents[i].classList.remove('is-active');
				}

				// クリックしたタブボタンにis-activeクラスを追加する
				tabButtons[3].classList.add('is-active');

				// タブを表示する
				if(tabContents[3] !== null) {
						tabContents[3].classList.add('is-active');
				}

				// ここにリロード後も同じタブが表示される処理を書く
				
			} else {
				let tabButton = event.currentTarget;
				let tabContent = document.getElementById(tabButton.dataset.id);

				// すべてのタブボタンのis-activeクラスを削除する
				for (let i = 0; i < tabButtons.length; i++) {
						tabButtons[i].classList.remove('is-active');
						tabContents[i].classList.remove('is-active');
				}
				// クリックしたタブボタンにis-activeクラスを追加する
				tabButton.classList.add('is-active');

				// タブを表示する
				if(tabContent !== null) {
						tabContent.classList.add('is-active');
				}
			}
		});				
	}
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

// console.log(localStorage);
function setCurrentTab(selectedTab) {
	localStorage.setItem('tab', selectedTab);
	console.log(localStorage.getItem('tab'));
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
			console.log('hey')
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





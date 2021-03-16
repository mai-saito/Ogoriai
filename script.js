// Handle click events
let cookieArray = [];
function handleCookie(cookies) {
	cookies.split('; ').forEach(function(cookiesArray) {
		let cookie = cookiesArray.split('=');
		cookieArray.push(cookie);
	});
};
window.onload = handleCookie(document.cookie);
console.log(cookieArray);

// Create a form of group member
cookies = [];
function handleClick(event) {
	const memberInputs = document.querySelector('#member-inputs');
	const input = document.createElement('input');
	const hiddenInput = document.createElement('input');

	if (event.target.name === 'add'){
		cookieArray.forEach(function(cookie) {
			if (event.target.value === cookie[0]) {
				cookies.push(cookie[0]);
				console.log(cookies)
				// console.log(JSON.stringify(cookies));
				document.cookie = 'user_id='+[cookies];
				console.log(document.cookie);
				input.setAttribute('value', decodeURIComponent(cookie[1]));
				input.setAttribute('name', 'name');
				input.style.display = 'block';
				hiddenInput.setAttribute('type', 'hidden')
				hiddenInput.setAttribute('value', cookie[0]);
				hiddenInput.setAttribute('name', 'user_id');
			}
		});
		memberInputs.appendChild(input);
		memberInputs.appendChild(hiddenInput);
	}
}

// function handleSubmit(event) {
// 	// event.preventDefault();
// 	$.ajax({
// 		type: 'POST',
// 		url: 'choose_member.php',
// 		// success: function () {
// 		// 	alert('Success');
// 		// },
// 		error: function (error) {
// 			console.log(error);
// 		}
// 	});
// }

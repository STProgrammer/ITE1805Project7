
// Denne koden er tatt (og litt modifisert) fra https://codewithawa.com/posts/check-if-user-already-exists-without-submitting-form
$('document').ready(function(){
	var username_state = false;
	var email_state = false;


	$('#username').on('blur', function(){
		var username = $('#username').val();
		if (username == '') {
			username_state = false;
			return;
		}
		$.ajax({
			url: 'process.php',
			type: 'post',
			data: {
				'username_check' : 1,
				'username' : username,
			},
			success: function(response){
				if (response == 'taken' ) {
					username_state = false;
					$('#username').parent().removeClass();
					$('#username').parent().addClass("form_error");
					$('#username').siblings("span").text('Sorry... Username already taken');
				}else if (response == 'not_taken') {
					username_state = true;
					$('#username').parent().removeClass();
					$('#username').parent().addClass("form_success");
					$('#username').siblings("span").text('Username available');
				}
			}
		});
	});
	$('#change-email-input').on('blur', function(){
		var email = $('#change-email-input').val();
		if (email == '') {
			email_state = false;
			return;
		}
		$.ajax({
			url: 'process.php',
			type: 'post',
			data: {
				'email_check' : 1,
				'email' : email,
			},
			success: function(response){
				if (response == 'taken' ) {
					email_state = false;
					$('#change-email-input').parent().removeClass();
					$('#change-email-input').parent().addClass("form_error");
					$('#change-email-input').siblings("span").text('Sorry... Email already taken');
				}else if (response == 'not_taken') {
					email_state = true;
					$('#change-email-input').parent().removeClass();
					$('#change-email-input').parent().addClass("form_success");
					$('#change-email-input').siblings("span").text('Email available');
				}
			}
		});
	});
	$("#edit-user").submit(function(e){
		$('#username').blur();
		if (!username_state){
			alert("Check if user data is correct");
			e.preventDefault();
		}
	});
	$("#change-email").submit(function(f){
		$('#change-email-input').blur();
		if (!email_state){
			alert("Check if email is correct");
			f.preventDefault();
		}
	});
});
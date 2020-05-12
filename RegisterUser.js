
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
	$('#register-email').on('blur', function(){
		var email = $('#register-email').val();
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
					$('#register-email').parent().removeClass();
					$('#register-email').parent().addClass("form_error");
					$('#register-email').siblings("span").text('Sorry... Email already taken');
				}else if (response == 'not_taken') {
					email_state = true;
					$('#register-email').parent().removeClass();
					$('#register-email').parent().addClass("form_success");
					$('#register-email').siblings("span").text('Email available');
				}
			}
		});
	});
	$("#register-form").submit(function(e){
		if (!email_state || !username_state){
			alert("Please correct the information!")
			e.preventDefault();
		}
	});
});
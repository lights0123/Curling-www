CSC Curling | Sign Up
<?php
if (isset($_POST['password'])) {
	ob_end_clean();
	exit();
}
?>
<script src="/js/jquery.validate.min.js"></script>
<div>
	<p>Please enter your information below:</p>

	<form id="signup_form" action="<?php echo getSelf(); ?>"
		  method="POST" enctype="multipart/form-data">
		<fieldset>
			<div>
				<input type="text" name="username" size="20" placeholder="Username"/>
			</div>

			<div>
				<input type="text" name="email" size="30" placeholder="Email Address"/>
			</div>

			<div>
				<input type="password" id="password" name="password" size="20" placeholder="Password"/>
			</div>
		</fieldset>
		<br/>
		<fieldset class="center">
			<input type="submit" value="Sign Up"/>
			<br/>
			<small>By signing up, you agree to the CSC Curling's <a href="/tos">Terms of Service</a> and <a
					href="/privacy">Privacy Policy</a>.
			</small>
		</fieldset>
	</form>
	<script>
		$("#signup_form").validate({
			rules: {
				username: {
					required: true,
					minlength: 4,
					maxlength: 64
				},
				password: {
					required: true,
					minlength: 6
				},
				email: {
					required: true,
					email: true,
					maxlength: 64
				}
			},
			messages: {
				password: {
					minlength: "Passwords must be at least 6 characters",
					required: "Please enter a password"
				},
				username: {
					minlength: "Please enter a username between 4 and 64 characters",
					required: "Please select a username",
					maxlength: "Please enter a username between 4 and 64 characters"
				},
				email: {
					email: "Please enter a valid email address",
					required: "Please enter an email address",
					maxlength: "Your email address must be less than 65 characters"
				}
			},
			submitHandler: function (form) {
				$.post($(form).attr('action'), $(form).serialize(), function (data, test, test2) {
					console.log(data);
					console.log(test);
					console.log(test2);
				});
			}
		});
	</script>
</div>
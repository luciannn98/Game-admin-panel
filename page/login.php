<?php
if ($general->logged_in() === true) {
	header('Location: home');
}
?>
<form method="post">
	<fieldset>
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" class="form-control" id="username" placeholder="Enter your username" name="login_username">
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" class="form-control" id="password" placeholder="Enter your password" name="login_password">
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-success" name="login_btn">Sign in</button>
		</div>
		<p class="forgot"><a href="signup">Don't have account</a> or <a href="forgot_password">Forgot password?</a></p>
	</fieldset>
</form>
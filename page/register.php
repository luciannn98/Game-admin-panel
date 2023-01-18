<?php
if ($general->logged_in() === true) {
	header('Location: home');
}
?>
<form method="post">
	<fieldset>
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" class="form-control" id="username" placeholder="Enter your username" name="reg_user">
		</div>
		<div class="form-group">
			<label for="email">Email</label>
			<input type="text" class="form-control" id="email" placeholder="Enter your email" name="reg_email">
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" class="form-control" id="password" placeholder="Enter your password" name="reg_pass">
		</div>
		<div class="form-group">
			<label for="re-password">Confirm password</label>
			<input type="password" class="form-control" id="re-password" placeholder="Confirm your password" name="reg_pass_2">
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-success" name="reg_btn">Sign up</button>
		</div>
	</fieldset>
</form>
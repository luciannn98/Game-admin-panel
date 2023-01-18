<?php
if ($general->logged_in() === true) {
	header('Location: home');
}
?>
<form method="post">
	<fieldset>
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" class="form-control" id="username" placeholder="Enter your username/SteamID" name="f_p_user">
		</div>
		<div class="form-group">
			<label for="email">Email</label>
			<input type="email" class="form-control" id="email" placeholder="Enter your email" name="f_p_email">
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-success" name="f_p_btn">Recover password</button>
		</div>
	</fieldset>
</form>
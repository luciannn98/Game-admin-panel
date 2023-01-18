<?php
$name = clear_string($general->get_username_from_id($_GET['user_id']));

if ($name) {
	if ($general->exist_user($name) == true) {
		$search_results = array_search($name, array_column($data, 'name'));
		if (!$_GET['change_avatar']) {
?>
<ul class="nav nav-tabs" style="margin-bottom: 10px;margin-left: 70px;">
	<li class="nav-item active">
		<a class="nav-link" data-toggle="tab" href="#profile">PROFILE</a>
	</li>
	<?php if ($general->my_account($name) === true): ?>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#tools">TOOLS</a>
	</li>
	<?php endif; ?>
	<?php if ($general->is_adm_and_mod() === true || $general->my_account($name) === true): ?>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#logs">LAST 10 ACTION</a>
	</li>
	<?php endif; ?>
</ul>

<div class="tab-content">
	<div id="profile" class="tab-pane in active">
		<div class="row col-sm-8 profile">
			<div class="col-sm-5 avatar">
				<center>
					<?php if ($_SESSION['id'] == $general->get_user_id_from_name($name) || $general->get_user_type_from_id($_SESSION['id']) == 'admin') { ?>
					<a href="profile&user_id=<?php echo $_GET['user_id']; ?>&change_avatar=<?php echo $_SESSION['id']; ?>"><img src="icons/avatars/<?php echo $general->get_user_avatar_from_name($name); ?>" width="150" height="250"></a><br>
					<?php } else { ?>
					<img src="icons/avatars/<?php echo $general->get_user_avatar_from_name($name); ?>"><br>
					<?php } ?>
					<?php if (JUMBO === true) { if (empty($search_results)) { ?>
					<span class="badge badge-pill badge-danger">Offline</span><br>
					<?php } else { ?>
					<span class="badge badge-pill badge-success">Online</span><br>
					<?php } } ?>
				</center>
			</div>
			<div class="col-sm-6 info">
				<?php if ($general->check_buy_something($name) === true) { ?>
					<span class="badge badge-pill badge-info">
						<i class="fa fa-diamond"></i>
						Premium user
					</span><br>
				<div class="dropdown-divider"></div>
					<?php } ?>
				<p>Username: <?php echo get_steam_name($_GET['user_id'], $name); ?></p>
				<div class="dropdown-divider"></div>
				<p>Access: <?php echo $general->get_user_access($general->get_user_access_from_name($name)); ?></p>
				<div class="dropdown-divider"></div>
				<?php if ($general->server_admin_2($name) === true): ?>
				<p>Ban-uri acordate: <?php echo $general->get_user_bans_from_name($name); ?></p>
				<div class="dropdown-divider"></div>
				<?php endif; ?>
				<p>Account type: <?php echo user_type($general->get_user_type_from_name($name)); ?></p>
				<div class="dropdown-divider"></div>
				<?php if ($general->logged_in() === true) { ?>
				<p>Email: <?php echo $general->get_user_email_from_name($name); ?></p>
				<div class="dropdown-divider"></div>
				<?php } ?>
				<p>Warns: <?php if ($general->get_user_warns_from_name($name) >= 2) {
								echo '<span class="red">'.$general->get_user_warns_from_name($name).'</span>';
							} else {
								echo $general->get_user_warns_from_name($name);
							} ?></p>
				<div class="dropdown-divider"></div>
			</div>
		</div>
	</div>

	<?php if ($general->my_account($name) === true): ?>
	<div id="tools" class="tab-pane">
		<div class="row col-sm-12" style="margin-left: 70px;">
			<div class="col-sm-6">
				<h3>Change Password</h3>
				<form method="post">
					<fieldset>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter old password..." name="change_old_pass">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter new password..." name="change_new_pass">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Confirm new password..." name="change_new_pass_2">
						</div>
						<button class="btn btn-danger btn-manage" name="change_password_btn">Change password</button>
					</fieldset>
				</form>
			</div>
			<div class="col-sm-6">
				<h3>Change Email</h3>
				<form method="post">
					<fieldset>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter old email..." name="c_o_e">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter new email..." name="c_n_e">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Confirm new email..." name="c_n_e_2">
						</div>
						<button class="btn btn-danger btn-manage" name="c_e_btn">Change email</button>
					</fieldset>
				</form>
			</div>
		</div>
		<div class="dropdown-divider" style="margin-left: 70px;"></div>
		<div class="row col-sm-12" style="margin-left: 70px;">
			<div class="col-sm-6">
				<h3>Change Username</h3>
				<form method="post">
					<fieldset>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter new name..." name="change_new_name">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Confirm new name..." name="change_new_name_2">
						</div>
						<button class="btn btn-danger btn-manage" name="change_name_btn">Change name</button>
					</fieldset>
				</form>
			</div>
			<!-- <div class="col-sm-6">
				<form method="post">
					<fieldset>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter old email..." name="c_o_e">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter new email..." name="c_n_e">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Confirm new email..." name="c_n_e_2">
						</div>
						<button class="btn btn-danger btn-manage" name="c_e_btn">Change email</button>
					</fieldset>
				</form>
			</div> -->
		</div>
	</div>
	<?php endif; ?>
	<?php if ($general->is_adm_and_mod() === true || $general->my_account($name) === true): ?>
	<div id="logs" class="tab-pane">
		<div class="row col-sm-12" style="margin-left: 70px;">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Username</th>
						<th>Actiune</th>
						<th>
							<i class="fa fa-clock-o"></i>
							Time
						</th>
						<th>User ip</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($select->select_last_10_action($name) as $r) { ?>
					<tr>
						<td><?php echo $r['user']; ?></td>
						<td><?php echo $r['actiune']; ?></td>
						<td><?php echo $r['time']; ?></td>
						<td><?php echo get_flag_from_ip($r['user_ip']); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php } else { ?>
	<form method="post" enctype="multipart/form-data" accept="image/*">
		<fieldset>
			<div class="form-group">
				<label for="change_avatar">Change avatar</label>
				<input type="hidden" name="name" value="<?php echo $name; ?>">
				<input type="file" class="form-control-file" id="change_avatar" aria-describedby="fileHelp" name="change_avatar">
				<small id="fileHelp" class="form-text text-muted">Max dimension 150x250 (pixel) and 2MB (megabyte).</small>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success" name="change_avatar_btn">Change</button>
			</div>
		</fieldset>
	</form>
<?php } } else { echo '<h3 class="red">Acest user nu a putut fi gasit in baza de date.</h3>'; } } else { header('Location: home'); } ?>
<?php if ($general->is_admin() === true || $general->is_mod() === true) { ?>
<div class="acp">
	<ul class="nav nav-tabs" style="margin-bottom: 10px;">
		<li class="nav-item active">
			<a class="nav-link" data-toggle="tab" href="#bans">Ban</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#suspend">Suspend</a>
		</li>

		<?php if ($general->is_admin() === true) { ?>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#admins">Admins</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#access">Access</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#shop">Item shop</a>
		</li>
		<?php } ?>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#news">News</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#suspends">Suspends</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#logs">Logs</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#buy_logs">Shop log</a>
		</li>
	</ul>

	<div class="tab-content">
		<div id="bans" class="tab-pane in active">
			<form method="post">
				<fieldset>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Enter username..." name="add_ban_username">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Enter IP / SteamID" name="add_ban_steamid">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Enter reason" name="add_ban_reason">
					</div>
					<button class="btn btn-danger btn-manage" name="add_ban_btn">Baneaza</button>
				</fieldset>
			</form>
		</div>

		<div id="suspend" class="tab-pane">
			<form method="post">
				<div class="form-group">
					<input type="text" class="form-control input-m" placeholder="Enter username..." name="add_suspend_user">
				</div>
				<div class="form-group">
					<select class="form-control input-m" name="add_suspend_time">
						<option value="86400">24h</option>
						<option value="259200">3 zile</option>
						<option value="432000">5 zile</option>
						<option value="604800">7 zile</option>
						<option value="1209600">14 zile</option>
						<option value="2592000">1 luna</option>
						<option value="999">Permanent</option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control input-m" placeholder="Enter reason..." name="add_suspend_reason">
				</div>
				<button class="btn btn-danger btn-manage" name="add_suspend_btn">Adauga</button>
			</form>
		</div>

		<?php if ($general->is_admin() === true) { ?>
		<div id="admins" class="tab-pane">
			<form method="post">
				<div class="form-group">
					<input class="form-control input-m" type="text" placeholder="Enter username" name="add_admin_user">
				</div>
				<div class="form-group">
					<input class="form-control input-m" type="text" placeholder="Enter password" name="add_admin_pass">
				</div>
				<div class="form-group">
					<input class="form-control input-m" type="text" placeholder="Enter email" name="add_admin_email">
				</div>
				<div class="form-group">
					<select class="form-control input-m" name="add_admin_access">
						<?php
							foreach ($general->get_user_access_for_option() as $a) {
								echo '<option value="'.$a['access'].'">'.$a['name'].'</option>';
							}
						?>
					</select>
				</div>
				<div class="form-group">
					<select class="form-control input-m" name="add_admin_flag">
						<option value="a">Nume</option>
						<option value="ce">Steam</option>
						<option value="de">IP</option>
					</select>
				</div>
				<div class="form-group">
					<select class="form-control input-m" name="add_admin_type">
						<option value="user">Member</option>
						<option value="mod">Moderator</option>
						<option value="admin">Admin</option>
					</select>
				</div>
				<button class="btn btn-danger btn-manage" name="add_admin_btn">Adauga</button>
			</form>
		</div>
		<div id="access" class="tab-pane">
			<form method="post">
				<div class="form-group">
					<input class="form-control input-m" type="text" placeholder="Enter access" name="add_access_access">
				</div>
				<div class="form-group">
					<input class="form-control input-m" type="text" placeholder="Enter name access" name="add_access_name">
				</div>
				<button class="btn btn-danger btn-manage" name="add_access_btn">Adauga</button>
			</form>
			<hr>
			<table class="table table-hover" style="margin-top: 20px;">
				<thead>
					<tr>
						<th>Access</th>
						<th>Access name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($select->select_access_from_db() as $r) { ?>
					<tr>
						<td><?php echo $r['access']; ?></td>
						<td><?php echo $r['name']; ?></td>
						<td>
							<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#<?php echo $r['access']; ?>">
								<i class="fa fa-pencil"></i>
							</button>
							<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&delete_access=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm">
								<i class="fa fa-trash"></i>
							</a>
						</td>
					</tr>
					<div class="modal" id="<?php echo $r['access']; ?>" role="dialog">
						<div class="modal-dialog" role="document">
							<!-- Modal content-->
								<div class="modal-content">
									<form method="post">
										<div class="modal-header">
											<h5 class="modal-title">
												Editeaza access.
											</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
										</div>
										<div class="modal-body">
											<fieldset>
												<div class="form-group">
													<label for="access">Access</label>
													<input type="text" class="form-control" id="access" placeholder="Enter access" name="access_access" value="<?php echo $r['access']; ?>">
												</div>
												<div class="form-group">
													<label for="access_name">Access name</label>
													<input type="text" class="form-control" id="access_name" placeholder="Enter access" name="access_name" value="<?php echo $r['name']; ?>">
												</div>
												<input type="hidden" name="access_id" value="<?php echo $r['id']; ?>">
											</fieldset>
										</div>
										<div class="modal-footer">
											<button class="btn btn-success" name="update_access_btn">Save</button>
											<button class="btn btn-danger" data-dismiss="modal">Close</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<?php } ?>

		<div id="shop" class="tab-pane">
			<form method="post">
				<div class="form-group">
					<input type="text" class="form-control input-m" placeholder="Enter item title..." name="add_shopitem_title">
				</div>
				<div class="form-group">
					<textarea class="form-control input-m" placeholder="Enter item description..." name="add_shopitem_description"></textarea>
				</div>
				<div class="form-group">
					<input type="text" class="form-control input-m" placeholder="Enter item price..." name="add_shopitem_price" value="5">
				</div>
				<div class="form-group">
					<select class="form-control input-m" name="add_shopitem_table">
						<option>Select a table</option>
						<option>-------------</option>
						<?php
							$tb = 'Tables_in_'.DB_DB;
							foreach ($select->get_tabels_from_db() as $a) {
								echo '<option value="'.$a[$tb].'">'.$a[$tb].'</option>';
							}
						?>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control input-m" placeholder="Enter column..." name="add_shopitem_column" value="access">
				</div>
				<div class="form-group">
					<input type="text" class="form-control input-m" placeholder="Enter column 2 (auth)..." name="add_shopitem_column_2" value="auth">
				</div>
				<div class="form-group">
					<input type="text" class="form-control input-m" placeholder="Enter column value..." name="add_shopitem_value" value="abcdefghijklmn">
				</div>
				<button class="btn btn-danger btn-manage" name="add_shopitem_btn">Adauga</button>
			</form>
		</div>

		<div id="news" class="tab-pane">
			<form method="post">
				<div class="form-group">
					<input type="text" class="form-control input-m" placeholder="Enter title..." name="add_news_title">
				</div>
				<div class="form-group">
					<textarea class="form-control input-m" placeholder="Enter message..." name="add_news_msg"></textarea>
				</div>
				<button class="btn btn-danger btn-manage" name="add_news_btn">Adauga</button>
			</form>
		</div>

		<div id="suspends" class="tab-pane">
			<table class="table table-hover" style="margin-top: 10px;">
				<thead>
					<tr>
						<th>Username</th>
						<th>
							<i class="fa fa-clock-o"></i>
							Time
						</th>
						<th>Reason</th>
						<th>Admin name</th>
						<?php if ($general->is_admin() == true) { ?>
							<th>Action</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($select->select_suspends() as $r) { ?>
					<tr>
						<td><?php echo get_steam_name(0, $r['username']); ?></td>
						<td><?php suspend_time($r['suspenddown']); ?></td>
						<td><?php echo $r['reason']; ?></td>
						<td><?php echo $r['by_auth']; ?></td>
						<?php if ($general->is_admin() == true) { ?>
							<td>
								<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&delete_suspend=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm">
									<i class="fa fa-trash"></i>
								</a>
							</td>
						<?php } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<div id="logs" class="tab-pane">
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
					<?php foreach ($select->select_logs() as $r) { ?>
					<tr>
						<td><?php echo get_steam_name(0, $r['user']); ?></td>
						<td><?php echo $r['actiune']; ?></td>
						<td><?php echo $r['time']; ?></td>
						<td><?php echo $r['user_ip']; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<div id="buy_logs" class="tab-pane">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Username</th>
						<th>Status</th>
						<th>
							<i class="fa fa-euro"></i>
							Price
						</th>
						<th>PaymentID</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($select->select_buy_logs() as $r) { ?>
					<tr>
						<td><?php echo get_steam_name(0, $r['username']); ?></td>
						<td><?php echo $r['status']; ?></td>
						<td><?php echo $r['pret']; ?></td>
						<td><?php echo $r['paymentid']; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php } else { header('Location: 404.php'); } ?>
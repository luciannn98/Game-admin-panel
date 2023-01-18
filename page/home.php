<?php if ($general->check_is_banned() === true) { ?>
	<?php foreach ($general->get_user_reason_from_ban() as $r) { ?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>

		<p class="black">Contul tau a fost banat pentru <b class="red"><?php echo $r['unbantime']; ?></b> de <b class="red"><?php echo $r['admin_name']; ?></b> motiv <b class="red"><?php echo $r['reason']; ?></b>!</p>

		<a href="home&unban_request=true" class="btn btn-success btn-md">UNBAN</a>

	</div>
<?php } } ?>

<?php if ($select->check_news() === true) { foreach ($select->select_news() as $r) { ?>
		<div class="border-secondary" style="width: 100%;border-bottom-right-radius: 30px;border-top-left-radius: 30px; margin: 10px;">
			<div class="card-header" style="border-top-left-radius: 30px;">
				<?php echo $r['title']; ?>
				<?php if ($general->is_admin() === true || $general->is_mod() === true) { ?>
				<a href="home&delete_news=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" style="float: right;">
					<i class="fa fa-trash-o"></i>
				</a>
				<?php } ?>
			</div>
			<div class="card-body">
				<?php echo $r['msg']; ?>
			</div>
		</div>
		<div class="dropdown-divider"></div>
<?php } } else { echo "<p>I can't found news in database.</p>"; }?>
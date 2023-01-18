<?php foreach ($general->user_suspend_panel($general->get_username_from_id($_SESSION['id'])) as $suspend) { ?>

	<div class="alert alert-danger">

		<button type="button" class="close" data-dismiss="alert">

			<i class="ace-icon fa fa-times"></i>

		</button>

		<p>Contul tau a fost suspendat pentru <b><?php suspend_time($suspend['suspenddown']); ?></b> de <b><?php echo get_steam_name(0, $suspend['by_auth']); ?></b> motiv <b><?php echo $suspend['reason']; ?></b>!</p>

	</div>

<?php } ?>
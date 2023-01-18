<?php if (JUMBO === true) { ?>
<table class="table table-hover">

	<thead>

		<tr>

			<th>Nickname</th>

			<th>Frags</th>

			<th>Time</th>

		</tr>

	</thead>
	<tbody>

		<?php foreach ($data as $r) { ?>

		<tr>

			<td><?php
			if ($general->exist_user($r['name'])) {
				echo '<a href="profile&user_id='.$general->get_user_id_from_name($r['name']).'" class="red"><b>'.htmlspecialchars($r['name']).'</b></a> - '.$general->get_user_access($general->get_user_access_from_name($r['name']));
			} else {
				echo htmlspecialchars($r['name']);
			}

			?></td>

			<td><?php echo $r['score']; ?></td>

			<td><?php echo gmdate("H:i:s", $r['time']); ?></td>

		</tr>

		<?php } ?>

	</tbody>

</table>
<?php } else { header('Location: home'); } ?>
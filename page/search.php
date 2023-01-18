<?php

$nick = $_GET['nickname'];

echo '
<center>
	<form method="post">
		<input type="text" class="form-control input-m" placeholder="Enter nickname" name="search_name">
	</form>
</center><br>';

if ($select->select_profile_users_search($nick) == false) {
	echo '<h3 class="red">Acest user nu a putut fi gasit in baza de date.</h3>';
} else {
	if ($nick) {
		foreach ($select->select_profile_users_search($nick) as $r) {
			echo "(<a href='profile&user_id=".$r['id']."'>".$r['id']."</a>) ".get_steam_name($r['id'], $r['auth'])." - [".$general->get_user_access($r['access'])."]<br>";
		}
	}
} ?>
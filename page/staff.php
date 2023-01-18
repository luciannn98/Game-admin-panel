<?php
if (!$_GET['edit_user']) {
//Extragerea datelor
$targetpage = 'staff';
$limit = 50;

$total_pages = $count->count_users();
$total_pages = $total_pages['total_results'];

$stages = 3;
$page = $_GET['pagina'];
if($page) {
	$start = ($page - 1) * $limit;
} else {
	$start = 0;
}

// Get page data
if ($general->is_admin() === true || $general->is_mod() === true) {
	$result = $select->select_users($start, $limit);
} else {
	$result = $select->select_users_2($start, $limit);
}
// Initial page num setup
if ($page == 0) {
	$page = 1;
}
$prev = $page - 1;
$next = $page + 1;
$lastpage = ceil($total_pages/$limit);
$LastPagem1 = $lastpage - 1;

$paginate = '';
if($lastpage > 1) {
	$paginate .= "<ul class='pagination'>";
	// Previous
	if ($page > 1) {
		$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$prev'><i class='fa fa-angle-double-left' aria-hidden='true'></i></a></li>";
	} else {
		$paginate.= "<li class='page-item disabled'><a class='page-link' href='#'><i class='fa fa-angle-double-left' aria-hidden='true'></i></a></li>";
	}

	// Pages
	if ($lastpage < 7 + ($stages * 2)) {
		for ($counter = 1; $counter <= $lastpage; $counter++) {
			if ($counter == $page) {
				$paginate.= "<li class='page-item active'><a class='page-link' href='#'>$counter</a></li>";
			} else {
				$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$counter'>$counter</a></li>";
			}
		}
	} else if ($lastpage > 5 + ($stages * 2)) {
		if($page < 1 + ($stages * 2)) {
			for ($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
				if ($counter == $page) {
					$paginate.= "<li class='page-item active'><a class='page-link' href='#'>$counter</a></li>";
				} else {
					$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$counter'>$counter</a></li>";
				}
			}
			$paginate.= "<li class='page-item disabled'><a class='page-link' href='#'>...</a></li>";
			$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$LastPagem1'>$LastPagem1</a></li>";
			$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$lastpage'>$lastpage</a></li>";
		} elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
			$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=1'>1</a></li>";
			$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=2'>2</a></li>";
			$paginate.= "<li class='page-item disabled'><a class='page-link' href='#'>...</a></li>";
			for ($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
				if ($counter == $page) {
					$paginate.= "<li class='page-item active'><a class='page-link' href='#'>$counter</a></li>";
				} else {
					$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$counter'>$counter</a></li>";}
				}
				$paginate.= "<li class='page-item disabled'><a class='page-link' href='#'>...</a></li>";
				$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$LastPagem1'>$LastPagem1</a></li>";
				$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$lastpage'>$lastpage</a></li>";
			} else {
		$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=1'>1</a></li>";
		$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=2'>2</a></li>";
		$paginate.= "<li class='page-item disabled'><a class='page-link' href='#'>...</a></li>";
		for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
			if ($counter == $page){
				$paginate.= "<li class='page-item active'><a class='page-link' href='#'>$counter</a></li>";
			}else{
				$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$counter'>$counter</a></li>";}
			}
		}
	}
	if ($page < $counter - 1) {
		$paginate.= "<li class='page-item'><a class='page-link' href='$targetpage&pagina=$next'><i class='fa fa-angle-double-right' aria-hidden='true'></i></a></li>";
	} else {
		$paginate.= "<li class='page-item disabled'><a class='page-link' href='#'><i class='fa fa-angle-double-right' aria-hidden='true'></i></a></li>";
	}
	$paginate.= "</ul>";
}?>
<?php echo $paginate; ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>
				Name
			</th>

			<th>
				Access
			</th>
			<th>
				<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
				Last online
			</th>

			<th>
				Avertismente
			</th>

			<?php if ($general->is_admin() === true || $general->is_mod() === true) { ?>
			<th>
				Type
			</th>
			<th>
				Action
			</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($result as $r) { ?>
		<tr>
			<td>
				<?php echo get_steam_name($r['id'], $r['auth']); ?>
			</td>
			<td><?php echo $general->get_user_access($r['access']); ?></td>
			<td><?php echo date("d/m/Y - H:i:s", $r['lastonline']); ?></td>
			<td><span class="badge badge-pill badge-danger"><?php echo $r['warns']; ?></span></td>
			<?php if ($general->is_mod() === true) { ?>
			<td><?php user_type($r['type']); ?></td>
			<td>
				<a href="staff&warn_user=<?php echo $r['id']; ?>">
					<button class="btn btn-sm btn-danger"><i class="fa fa-plus"></i></button>
				</a>
				<a href="staff&unwarn_user=<?php echo $r['id']; ?>">
					<button class="btn btn-sm btn-success"><i class="fa fa-minus"></i></button>
				</a>
			</td>
			<?php } else if ($general->is_admin() === true) { ?>
			<td><?php user_type($r['type']); ?></td>
			<td>
				<a href="staff&warn_user=<?php echo $r['id']; ?>">
					<button class="btn btn-sm btn-danger"><i class="fa fa-plus"></i></button>
				</a>
				<a href="staff&unwarn_user=<?php echo $r['id']; ?>">
					<button class="btn btn-sm btn-success"><i class="fa fa-minus"></i></button>
				</a>
				<a href="staff&delete_user=<?php echo $r['id']; ?>">
					<button class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></button>
				</a>
				<a href="staff&edit_user=<?php echo $r['id']; ?>">
					<button class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></button>
				</a>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php
echo $paginate;
} else { ?>
	<?php foreach ($select->select_users_info($_GET['edit_user']) as $r) { ?>
	<form method="post">
		<fieldset>
			<input type="hidden" name="staff_user_id" value="<?php echo $r['id']; ?>">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" class="form-control" id="username" placeholder="Enter username" name="staff_user" value="<?php echo $r['auth']; ?>">
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="text" class="form-control" id="password" placeholder="Enter password" name="staff_pass" value="<?php echo $r['password']; ?>">
			</div>
			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" class="form-control" id="email" placeholder="Enter email" name="staff_email" value="<?php echo $r['email']; ?>">
			</div>
			<div class="form-group">
				<label for="access">Access</label>
				<select class="form-control" id="access" name="staff_access">
					<?php 
					foreach ($general->get_user_access_for_option() as $a) {
						echo '<option value="'.$a['access'].'">'.$a['name'].'</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="flag">Flag</label>
				<select class="form-control" id="flag" name="staff_flags">
					<option value="a">Nume</option>
					<option value="ce">Steam</option>
					<option value="de">IP</option>
				</select>
			</div>
			<div class="form-group">
				<label for="type">Type account</label>
				<select class="form-control" id="type" name="staff_type">
					<option value="user">Member</option>
					<option value="mod">Moderator</option>
					<option value="admin">Admin</option>
				</select>
			</div>
			<button class="btn btn-success" name="update_staff_btn">Update</button>
		</fieldset>
	</form>
<?php } } ?>
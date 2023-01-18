<?php
if (!$_GET['info']) {
if ($_GET['search']) {
	$search = $_GET['search'];
	$result = $select->select_bans_search($search);
} else {
	//Extragerea datelor
	$targetpage = 'bans';
	$limit = 50;

	$total_pages = $count->count_bans();
	$total_pages = $total_pages['total_results'];

	$stages = 3;
	$page = $_GET['pagina'];
	if($page) {
		$start = ($page - 1) * $limit;
	} else {
		$start = 0;
	}

	// Get page data
	$result = $select->select_bans($start, $limit);

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
	}
}

?>
<form method="post">
	<input type="text" class="form-control input-xl" placeholder="Search by username, ip or steamid..." name="b_search" autocomplete="off" />
</form>
<div class="dropdown-divider"></div>
<?php echo $paginate; ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Name</th>
			<th>
				<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
				Time
			</th>
			<th>
				Reason
			</th>

			<th>
				Banned by
			</th>
			<?php if ($general->is_admin() === true || $general->is_mod() === true) { ?>
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
				<?php echo $r['victim_name']; ?>
			</td>	
			<td><?php echo $r['unbantime']; ?></td>			
			<td><?php echo $r['reason']; ?></td>
			<td>
				<?php echo get_steam_name($general->get_user_id_from_name($r['admin_name']), $r['admin_name']); ?>
			</td>
			<?php if ($general->is_admin() === true || $general->is_mod() === true) { ?>
			<td>
				<a href="bans&delete_ban=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm">
					<i class="fa fa-trash"></i>
				</a>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php
echo $paginate;
} else {

}
?>
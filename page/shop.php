<?php $general->protect_login(); ?>
<center><small>ATENTIE! Se pot cumpara atat accese mai mari cat si accese mai mici, cel actual nu poate fi cumparat.</small></center>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Item</th>
			<th>
				Price
			</th>
			<th>
				Action
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($select->select_all_shop_items() as $r) { ?>
		<tr>
			<td>
				<p><?php echo $r['produs']; ?></p>
				<small><?php echo $r['descriere']; ?></small>
			</td>	
			<td>
				<?php echo $r['pret']; ?>
				<i class="ace-icon fa fa-euro hidden-480"></i>
			<td>
				<a href="shop&buy=<?php echo $r['id']; ?>" class="btn btn-success btn-md">
					<i class="fa fa-shopping-cart"></i> Buy
				</a>
				<a href="shop&delete_item=<?php echo $r['id']; ?>" class="btn btn-danger btn-md" title="Delete this item.">
					<i class="fa fa-trash"></i>
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
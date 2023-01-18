<?php  if (count($errors) > 0) : ?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<?php foreach ($errors as $error) : ?>
			<p><?php echo $error ?></p>
		<?php unset($error); endforeach ?>
	</div>
<?php  endif ?>

<?php  if (count($succes) > 0) : ?>
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<?php foreach ($succes as $succes) : ?>
			<p><?php echo $succes ?></p>
		<?php unset($succes); endforeach ?>
	</div>
<?php  endif ?>

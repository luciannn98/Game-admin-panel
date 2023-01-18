<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<a class="navbar-brand" href="home">
		<img src="icons/UPA.png" width="100" height="30" title="Go to home page.">
	</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarColor01">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item dropdown">
				<a class="nav-link" href="staff">
					<i class="fa fa-android"></i> 
					Staff
				</a>
			</li>
			<?php if (SHOP): ?>
			<li class="nav-item">
				<a class="nav-link" href="shop">
					<i class="fa fa-diamond"></i> 
					Shop
				</a>
			</li>
			<?php endif; ?>
			<li class="nav-item dropdown">
				<a class="nav-link" href="bans">
					<i class="fa fa-frown-o"></i> 
					Ban-uri
				</a>
			</li>
			<?php if (JUMBO): ?>
			<li class="nav-item">
				<a class="nav-link" href="jucatori-online">
					<i class="fa fa-users"></i> 
					Jucatori Online
				</a>
			</li>
			<?php endif; ?>
		</ul>

		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link" href="search">
					<i class="fa fa-search"></i> 
					Search player
				</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-user"></i>
					<?php if ($general->logged_in() === true) { ?>
						Welcome back, <?php echo get_steam_name(0, $general->get_username_from_id($_SESSION['id']), 0); ?>
					<?php } else { ?>
						User account
					<?php } ?>
				</a>
				<div class="dropdown-menu">
					<?php if ($general->logged_in() === true) { ?>
						<a class="dropdown-item" href="profile&user_id=<?php echo $_SESSION['id']; ?>">Profile</a>
						<?php if ($general->is_admin() === true || $general->is_mod() === true) { ?>
						<a class="dropdown-item" href="manage">Admin CP</a>
						<?php } ?>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="logout">Logout</a>
					<?php } else { ?>
						<a class="dropdown-item" href="signin">Sign In</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="signup">Sign Up</a>
						<?php } ?>
				</div>
			</li>
		</ul>
	</div>
</nav>
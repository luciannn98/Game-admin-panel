<?php
require_once "core/init.php";
require_once "include/overall/header.php";
require "include/errors.php";
$general->suspend_time_down();
$page = $_GET['page'];
if (!$page) {
	header('Location: shop');
} else {
	if ($general->user_suspend_panel($general->get_username_from_id($_SESSION['id'])) != false) {
		require 'include/overall/suspend.php';
		if ($page == 'logout') {
			$general->logout();
		}
	} else {
		switch ($page) {
			case 'home':
				require_once 'page/home.php';
				break;
			case 'jucatori-online':
				require_once 'page/jucatori_on.php';
				break;
			case 'logout':
				$general->logout();
				break;
			case 'bans':
				require_once 'page/bans.php';
				break;
			case 'staff':
				require_once 'page/staff.php';
				break;
			case 'manage':
				require_once 'page/admin_acp.php';
				break;
			case 'profile':
				require_once 'page/profile.php';
				break;
			case 'search':
				require_once 'page/search.php';
			break;
			case 'signin':
				require_once 'page/login.php';
			break;
			case 'signup':
				require_once 'page/register.php';
			break;
			case 'forgot_password':
				require_once 'page/forgot_pass.php';
			break;
			case 'shop':
				require_once 'page/shop.php';
			break;
								
			default:
				header('Location: 404.php');
				break;
		}
	}
}

require_once "include/overall/footer.php";
?>
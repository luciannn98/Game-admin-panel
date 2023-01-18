<?php
if (file_exists("config.php")):
	header("Location: home");
	die();
endif;

if (isset($_POST['install_panel'])) {
	$data = $_POST['install'];
	$content = "<?php

// Data for conection to database
define(DB_HOST, '$data[0]');
define(DB_USER, '$data[1]');
define(DB_PASS, '$data[2]');
define(DB_DB, '$data[3]');

// Private key si secret key for PayPal API
define(P_KEY, '$data[4]');
define(P_SEC, '$data[5]');
define(P_MODE, '$data[6]'); // 'sandbox' = test store | 'live' = live shopping

// Web settings
define(WEB_TITLE, '$data[7]'); // Titlul paginii
define(WEB_EMAIL, '$data[8]'); // Email pentru functia de contact
define(JUMBO, $data[9]); // 'false' = nu apare statistici server | 'true' = apar statistici despre server
define(FOLDER, $data[11]); // 'false' = Daca nu il ai intr-un director/folder | 'true' = daca este intr-un folder
define(FOL_NAME, '$data[12]');// Denumirea folderului daca variabila de mai sus este TRUE
define(SHOP, $data[10]); // 'false' = shop-ul este dezactivat | 'true' = shop-ul este activat
define(TIMP, time());

// GameQ settings (if JUMBO is true)
define(GQ_MOD, '$data[13]'); // Se mentine 'cs', NU ARE LEGATURA CU MODUL SERVER-lui
define(GQ_PORT, $data[14]); // De regula este 27015 portul, daca ai altul, il poti schimba
define(GQ_IP, '$data[15]'); // IP server
";
	if (!file_exists("config.php")) {
		$fp = fopen("config.php","wb");
		fwrite($fp,$content);
		fclose($fp);
		header("Location: home");
	} else {
		die();
	}
}


?>

<!DOCTYPE html>
<html>
<head>
	<head>
		<title>UPA v3 Instalation</title>
		<link rel="shortcut icon" type="image/x-icon" href="icons/favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/style.css">

		<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://bootswatch.com/4/lux/bootstrap.min.css">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

		<link href='https://fonts.googleapis.com/css?family=Audiowide' rel='stylesheet'>
		<link href='https://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
	</head>
</head>
<body>
	<div class="container">
		<center>
			<form method="post" style="margin: 10%">
				<div align="left"><small>Database details</small></div>
				<div class="dropdown-divider"></div>
				<fieldset>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Database Host" name="install[]">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Database User" name="install[]">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Database Password" name="install[]">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Database Name" name="install[]">
					</div>
				</fieldset>
				<div align="left"><small>PayPal Details</small></div>
				<div class="dropdown-divider"></div>
				<fieldset>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="PayPal Private KEY" name="install[]">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="PayPal Secret KEY" name="install[]">
					</div>
					<div class="form-group">
						<select class="form-control input-m" name="install[]">
							<option value="sandbox">Sandbox</option>
							<option value="live">Live</option>
						</select>
					</div>
				</fieldset>
				<div align="left"><small>Web settings</small></div>
				<div class="dropdown-divider"></div>
				<fieldset>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Page title" name="install[]">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Admin email" name="install[]">
					</div>
					<div class="form-group">
						<select class="form-control input-m" name="install[]">
							<option value="true">Jumbo ON</option>
							<option value="false">Jumbo OFF</option>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control input-m" name="install[]">
							<option value="true">Shop ON</option>
							<option value="false">Shop OFF</option>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control input-m" name="install[]">
							<option value="false">Directory OFF</option>
							<option value="true">Directory ON</option>
						</select>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Folder name" name="install[]">
					</div>
				</fieldset>
				<div align="left"><small>Server Details</small></div>
				<div class="dropdown-divider"></div>
				<fieldset>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Server mode (default: cs)" name="install[]">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Server port" name="install[]">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Server IP" name="install[]">
					</div>
				</fieldset>
				<input type="submit" class="btn btn-success btn-block" name="install_panel">
			</form>
		</center>
	</div>
</body>
</html>
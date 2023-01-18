<?php
class Connection {
	public function __construct() {
		$this->create_table();
	}

	public function dbConnect() {
		return new PDO("mysql:host=".DB_HOST."; dbname=".DB_DB, DB_USER, DB_PASS);
	}

	public function create_table() {
		$st = $this->dbConnect()->prepare("DESCRIBE `admins`");
		if (!$st->execute()) {
			$st = $this->dbConnect()->prepare(file_get_contents("./db.sql"));
			$st->execute();
		}
	}
}

class User {
	protected $db;
	protected $time;
	protected $aply;
	protected $ticket;
	protected $unban;
	protected $pro;
	protected $contra;
	protected $acceptat;
	protected $respins;

	public function __construct() {
		$this->db = new Connection();
		$this->db = $this->db->dbConnect();

		// Other variables
		$this->time = time();
		$this->aply = 'application';
		$this->ticket = 'ticket';
		$this->unban = 'unban';
		$this->pro = 'pro';
		$this->contra = 'contra';
		$this->acceptat = 'acceptat';
		$this->respins = 'respins';
	}

	public function set_log($actiune) {
		$ip = get_user_ip();
		$st = $this->db->prepare("INSERT INTO `logs` (user, actiune, time, user_ip) VALUES (:username, :action, NOW(), :ip)");
		if ($this->logged_in() === true) {
			$st->bindParam(':username', $this->get_username_from_id($_SESSION['id']));
		} else {
			$st->bindParam(':username', $ip);
		}
		$st->bindParam(':action', $actiune);
		$st->bindParam(':ip', $ip);
		$st->execute();
	}

	public function exist_user($username) {
		$username = clear_string($username);
		
		$st = $this->db->prepare("SELECT `id` FROM `admins` WHERE `auth` = :auth");
		$st->bindParam(':auth', $username);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function check_is_banned() {
		$st = $this->db->prepare("SELECT `id` FROM `advanced_bans` WHERE `victim_name` = :name");
		$st->bindParam(':name', $this->get_username_from_id($_SESSION['id']));
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function get_user_reason_from_ban() {
		$st = $this->db->prepare("SELECT * FROM `advanced_bans` WHERE `victim_name` = :name ORDER BY `id` DESC LIMIT 1");
		$st->bindParam(':name', $this->get_username_from_id($_SESSION['id']));
		if ($st->execute()) {
			return $st->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return false;
		}
	}

	public function get_user_access($access) {
		$access = clear_string($access);

		$st = $this->db->prepare("SELECT * FROM `admin_access` WHERE `access` = :access LIMIT 1");
		$st->bindParam(':access', $access);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
					if ($access == $r['access']) {
						return $r['name'];
					} else {
						return "Unknow access.";
					}
				}
			} else {
				return "Unknow access.";
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_user_access_for_option () {
		$st = $this->db->prepare("SELECT * FROM `admin_access`");
		$st->execute();

		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function login($name, $pass) {
		$name = clear_string($name);
		$pass = clear_string($pass);
		$tim = time();
		$log = 'logged in his account';

		$st = $this->db->prepare("SELECT * FROM `admins` WHERE `auth` = :auth AND `password` = :pass LIMIT 1");
		$st->bindParam(':auth', $name);
		$st->bindParam(':pass', $pass);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				$st_log = $this->db->prepare("UPDATE `admins` SET `lastonline` = :timp WHERE `auth` = :auth");
				$st_log->bindParam(':timp', $tim);
				$st_log->bindParam(':auth', $name);
				$st_log->execute();

				foreach ($st->fetchAll() as $row) {
					$_SESSION['id'] = $row['id'];
					$this->set_log($log);
					return true;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function logout() {
		session_destroy();
		unset($_SESSION['id']);
		header('Location: home');
	}

	public function get_user_access_from_name($name) {
		$st = $this->db->prepare("SELECT `access` FROM `admins` WHERE `auth` = :auth LIMIT 1");
		$st->bindParam(':auth', $name);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['access'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_user_email_from_name($name) {
		$st = $this->db->prepare("SELECT `email` FROM `admins` WHERE `auth` = :auth LIMIT 1");
		$st->bindParam(':auth', $name);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['email'];
			}
		}
	}

	public function get_user_warns_from_name($name) {
		$st = $this->db->prepare("SELECT `warns` FROM `admins` WHERE `auth` = :auth LIMIT 1");
		$st->bindParam(':auth', $name);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['warns'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_user_bans_from_name($name) {
		if ($this->server_admin_2($name) === true) {
			$st = $this->db->prepare("SELECT * FROM `advanced_bans` WHERE `admin_name` = :auth");
			$st->bindParam(':auth', $name);
			if ($st->execute()) {
				return $st->rowCount();
			} else {
				return 'Mysql error.';
			}
		} else {
			return 'Not admin.';
		}
	}

	public function get_news_title($id) {
		$st = $this->db->prepare("SELECT `title` FROM `news` WHERE `id` = :id");
		$st->bindParam(':id', $name);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['title'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_user_type_from_name($name) {
		$st = $this->db->prepare("SELECT `type` FROM `admins` WHERE `auth` = :auth LIMIT 1");
		$st->bindParam(':auth', $name);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['type'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_user_avatar_from_name($name) {
		$st = $this->db->prepare("SELECT `avatar` FROM `admins` WHERE `auth` = :auth LIMIT 1");
		$st->bindParam(':auth', $name);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['avatar'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function update_admin_access($access, $access_name, $id) {
		$access = clear_string($access);
		$access_name = clear_string($access_name);
		$id = clear_string($id);
		$log = 'edit access ['.$access.'] with name ['.$access_name.']';

		$st = $this->db->prepare("UPDATE `admin_access` SET `access` = :access, `name` = :name WHERE `id` = :id");
		$st->bindParam(':access', $access);
		$st->bindParam(':name', $access_name);
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function check_access_exist($access) {
		$st = $this->db->prepare("SELECT * FROM `admin_access` WHERE `access` = :access");
		$st->bindParam(':access', $access);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function get_user_id_from_name($name) {
		$st = $this->db->prepare("SELECT `id` FROM `admins` WHERE `auth` = :auth LIMIT 1");
		$st->bindParam(':auth', $name);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['id'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_user_type_from_id($id) {
		$st = $this->db->prepare("SELECT `type` FROM `admins` WHERE `id` = :id LIMIT 1");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['type'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_item_shop_price_with_id($id) {
		$id = clear_string($id);
		$st = $this->db->prepare("SELECT `pret` FROM `shop_items` WHERE `id` = :id LIMIT 1");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['pret'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_item_shop_query_with_id($id) {
		$id = clear_string($id);
		$st = $this->db->prepare("SELECT `query` FROM `shop_items` WHERE `id` = :id LIMIT 1");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['query'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_item_shop_value_with_id($id) {
		$id = clear_string($id);
		$st = $this->db->prepare("SELECT `value` FROM `shop_items` WHERE `id` = :id LIMIT 1");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['value'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_item_shop_produs_with_id($id) {
		$id = clear_string($id);
		$st = $this->db->prepare("SELECT `produs` FROM `shop_items` WHERE `id` = :id LIMIT 1");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['produs'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_shop_log_paymentid($id) {
		$st = $this->db->prepare("SELECT `paymentid` FROM `shop_buy_log` WHERE `paymentid` = :paymentid LIMIT 1");
		$st->bindParam(':paymentid', $id);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function check_buy_something($name) {
		$name = clear_string($name);
		$st = $this->db->prepare("SELECT `username` FROM `shop_buy_log` WHERE `username` = :user LIMIT 1");
		$st->bindParam(':user', $name);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function get_user_qery_from_shop($buy_id, $buy_price) {
		$buy_id = clear_string($buy_id);
		if (substr($buy_price, 0, -3) === $this->get_item_shop_price_with_id($buy_id)) {
			$st = $this->db->prepare($this->get_item_shop_query_with_id($buy_id));
			$st->bindParam(':value', $this->get_item_shop_value_with_id($buy_id));
			$st->bindParam(':auth', $this->get_username_from_id($_SESSION['id']));
			if ($st->execute()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function set_shop_log($price, $status, $paymentID) {
		$st = $this->db->prepare("INSERT INTO `shop_buy_log` (`username`, `pret`, `status`, `paymentid`) VALUES (:user, :price, :status, :paymentid)");
		$st->bindParam(':user', $this->get_username_from_id($_SESSION['id']));
		$st->bindParam(':price', $price);
		$st->bindParam(':status', $status);
		$st->bindParam(':paymentid', $paymentID);
		if ($st->execute()) {
			return true;
		} else {
			return false;
		}
	}

	public function get_access_name_from_id($id) {
		$st = $this->db->prepare("SELECT `name` FROM `admin_access` WHERE `id` = :id LIMIT 1");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['name'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function check_avatar($id) {
		$st = $this->db->prepare("SELECT `avatar` FROM `admins` WHERE `id` = :id");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['avatar'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function check_column($column, $table) {
		$column = clear_string($column);
		$table = clear_string($table);
		$query = "SHOW COLUMNS FROM ".$table." WHERE `Field` = :field";
		$st = $this->db->prepare($query);
		$st->bindParam(':field', $column);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function protect_login() {
		if ($this->logged_in() === false) {
			header('Location: home');
		}
	}

	public function logged_in() {
		return (isset($_SESSION['id'])) ? true : false;
	}

	public function is_admin() {
		if ($this->get_user_type_from_id($_SESSION['id']) === 'admin') {
			return true;
		} else {
			return false;
		}
	}

	public function is_mod() {
		if ($this->get_user_type_from_id($_SESSION['id']) === 'mod') {
			return true;
		} else {
			return false;
		}
	}

	public function is_adm_and_mod() {
		if ($this->get_user_type_from_id($_SESSION['id']) === 'mod' || $this->get_user_type_from_id($_SESSION['id']) === 'admin') {
			return true;
		} else {
			return false;
		}
	}

	public function my_account($name) {
		if ($this->logged_in() === true) {
			if ($this->get_username_from_id($_SESSION['id']) === $name) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function server_admin() {
		$st = $this->db->prepare("SELECT `id` FROM `admins` WHERE `auth` = :auth AND `access` LIKE '%c%' LIMIT 1");
		$st->bindParam(':auth', $this->get_username_from_id($_SESSION['id']));
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function server_admin_2($name) {
		$st = $this->db->prepare("SELECT `id` FROM `admins` WHERE `auth` = :auth AND `access` LIKE '%c%' LIMIT 1");
		$st->bindParam(':auth', $name);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function Register($reg_user, $reg_email, $reg_pass) {
		$reg_user = clear_string($reg_user);
		$reg_pass = clear_string($reg_pass);
		$reg_email = clear_string($reg_email);
		$log = '['.$reg_user.'] is a new user now';

		if ($this->exist_user($reg_user) == false) {
			$st = $this->db->prepare("INSERT INTO `admins` (auth, password, email) VALUES (:auth, :pass, :email)
			");
			$st->bindParam(':auth', $reg_user);
			$st->bindParam(':pass', $reg_pass);
			$st->bindParam(':email', $reg_email);
			if ($st->execute()) {
				$this->set_log($log);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function staff_get_warn($id) {
		$id = clear_string($id);
		$log = 'get warn ['.$this->get_username_from_id($id).']';
		$log2 = 'get warn and remove ['.$this->get_username_from_id($id).']';

		$st = $this->db->prepare("SELECT * FROM `admins` WHERE `id` = :id");
		$st->bindParam(':id', $id);
		$st->execute();
		foreach ($st->fetchAll() as $row) {
			if ($row['warns'] == 2) {
				$st2 = $this->db->prepare("UPDATE `admins` SET `access` = 'z', `warns` = 0 WHERE `id` = :id;");
				$st2->bindParam(':id', $id);
				if ($st2->execute()) {
					$this->set_log($log2);
					return true;
				} else {
					return false;
				}
			} else {
				$st3 = $this->db->prepare("UPDATE `admins` SET `warns` = :warns + 1 WHERE `id` = :id");
				$st3->bindParam(':warns', $row['warns']);
				$st3->bindParam(':id', $id);
				if ($st3->execute()) {
					$this->set_log($log);
					return true;
				} else {
					return false;
				}
			}
		}
	}

	public function staff_get_unwarn($id) {
		$id = clear_string($id);
		$log = 'get unwarn ['.$this->get_username_from_id($id).']';
		$user_ip = get_user_ip();

		$st = $this->db->prepare("SELECT * FROM `admins` WHERE `id` = :id");
		$st->bindParam(':id', $id);
		$st->execute();
		foreach ($st->fetchAll() as $row) {
			if ($row['warns'] <= 0) {
				return false;
			} else {
				$st3 = $this->db->prepare("UPDATE `admins` SET `warns` = :warns - 1 WHERE `id` = :id;");
				$st3->bindParam(':warns', $row['warns']);
				$st3->bindParam(':id', $id);
				if ($st3->execute()) {
					$this->set_log($log);
					return true;
				} else {
					return false;
				}
			}
		}
	}

	public function update_user($user, $pass, $email, $access, $flags, $type, $u_id) {
		$user = clear_string($user);
		$pass = clear_string($pass);
		$email = clear_string($email);
		$access = clear_string($access);
		$flags = clear_string($flags);
		$type = clear_string($type);
		$u_id = clear_string($u_id);
		$log = 'update user ['.$user.'] with access ['.$access.'] and type ['.$type.']';

		$st = $this->db->prepare("UPDATE `admins` SET `auth` = :auth, `password` = :pass, `access` = :access, `flags` = :flag, `email` = :email, `type` = :type WHERE `id` = :id;");
		$st->bindParam(':auth', $user);
		$st->bindParam(':pass', $pass);
		$st->bindParam(':access', $access);
		$st->bindParam(':flag', $flags);
		$st->bindParam(':email', $email);
		$st->bindParam(':type', $type);
		$st->bindParam(':id', $u_id);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function make_admin_application($user, $access) {
		$user = clear_string($user);
		$pass = clear_string($pass);

		$log = 'application admin accepted ['.$user.'] with access ['.$access.']';

		$st = $this->db->prepare("UPDATE `admins` SET `access` = :access WHERE `auth` = :auth;");
		$st->bindParam(':access', $access);
		$st->bindParam(':auth', $user);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function forgot_password($name, $email) {
		$name = clear_string($name);
		$email = clear_string($email);

		$chk = $this->db->prepare("SELECT * FROM `admins` WHERE `auth` = :auth AND `email` = :email LIMIT 1");
		$chk->bindParam(':auth', $name);
		$chk->bindParam(':email', $email);
		$chk->execute();

		if ($chk->rowCount() > 0) {
			foreach ($chk->fetchAll() as $r) {
				$to = $r['email'];
				$subject = 'Recuperare parola.';
				$body = "
					<center><h3>Recuperare parola</h3></center><br><br>
					Nume: <b>".$r['auth']."</b><br>
					Parola: <b>" . $r['password'] . "</b></b></b>
					<h3>Distractie placuta!</h3>
				";
				$headers = 'Content-type: text/html; charset=iso-8859-1' . "\n"; 

				mail ($to, $subject, $body, $headers);
				return true;
			}
		} else {
			return false;
		}
	}

	public function change_avatar($file, $user) {
		$log = '['.$this->get_username_from_id($user).'] change avatar';
		$st = $this->db->prepare("UPDATE `admins` SET `avatar` = :avatar WHERE `id` = :id");
		$st->bindParam(':avatar', $file);
		$st->bindParam(':id', $user);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function change_password($old_pass, $new_pass) {
		$old_pass	= clear_string($old_pass);
		$new_pass	= clear_string($new_pass);
		$log = 'change password from ['.$old_pass.'] in ['.$new_pass.']';

		$chk = $this->db->prepare("SELECT `id` FROM `admins` WHERE `password` = :pass AND `auth` = :auth LIMIT 1");
		$chk->bindParam(':pass', $old_pass);
		$chk->bindParam(':auth', $this->get_username_from_id($_SESSION['id']));
		$chk->execute();

		if ($chk->rowCount() > 0) {
			$st = $this->db->prepare("UPDATE `admins` SET `password` = :pass WHERE `id` = :id");
			$st->bindParam(':pass', $new_pass);
			$st->bindParam(':id', $_SESSION['id']);
			if ($st->execute()) {
				$this->set_log($log);
				$this->logout();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function change_username($new_name) {
		$new_name	= clear_string($new_name);
		$log = 'change username from ['.$this->get_username_from_id($_SESSION['id']).'] in ['.$new_name.']';

		$chk = $this->db->prepare("SELECT `id` FROM `admins` WHERE `auth` = :auth LIMIT 1");
		$chk->bindParam(':auth', $this->get_username_from_id($_SESSION['id']));
		$chk->execute();

		if ($chk->rowCount() > 0) {
			$st = $this->db->prepare("UPDATE `admins` SET `auth` = :auth WHERE `id` = :id");
			$st->bindParam(':auth', $new_name);
			$st->bindParam(':id', $_SESSION['id']);
			if ($st->execute()) {
				$this->set_log($log);
				//$this->logout();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function change_email($old_email, $new_email) {
		$old_email	= clear_string($old_email);
		$new_email	= clear_string($new_email);
		$log = 'change email from ['.$old_email.'] in ['.$new_email.']';

		$chk = $this->db->prepare("SELECT `id` FROM `admins` WHERE `email` = :email AND `auth` = :auth");
		$chk->bindParam(':email', $old_email);
		$chk->bindParam(':auth', $this->get_username_from_id($_SESSION['id']));
		$chk->execute();

		if ($chk->rowCount() > 0) {
			$st = $this->db->prepare("UPDATE `admins` SET `email` = :email WHERE `id` = :id");
			$st->bindParam(':email', $new_email);
			$st->bindParam(':id', $_SESSION['id']);
			if ($st->execute()) {
				$this->set_log($log);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function user_suspend_panel($auth) {
		$auth = clear_string($auth);
		$chk = $this->db->prepare("SELECT `id` FROM `panel_suspend` WHERE `username` = :user");
		$chk->bindParam(':user', $auth);
		$chk->execute();

		if ($chk->rowCount() > 0) {
			$st = $this->db->prepare("SELECT * FROM `panel_suspend` WHERE `username` = :user LIMIT 1");
			$st->bindParam(':user', $auth);
			if ($st->execute()) {
				return $st->fetchAll();
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function suspend_time_down() {
		$chk = $this->db->prepare("SELECT * FROM `panel_suspend`");
		$chk->execute();

		foreach ($chk->fetchAll(PDO::FETCH_ASSOC) as $r) {
			$down 	  = $r['suspenddown'];
			$downtime = time()-$down;

			if ($chk->rowCount() > 0) {
				if ($down != 999) {
					$st = $this->db->prepare("DELETE FROM `panel_suspend` WHERE `suspend` < :dtime");
					$st->bindParam(':dtime', $downtime);
					$st->execute();
				}
			}
		}
	}

	public function get_username_from_id($id) {
		$id = clear_string($id);

		$st = $this->db->prepare("SELECT `auth` FROM `admins` WHERE `id` = :id");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll() as $r) {
				return $r['auth'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public function get_username_from_suspend_with_id($id) {
		$id = clear_string($id);

		$st = $this->db->prepare("SELECT `username` FROM `panel_suspend` WHERE `id` = :id");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			foreach ($st->fetchAll() as $r) {
				return $r['username'];
			}
		} else {
			return 'Mysql error.';
		}
	}

    public function get_username_ban_from_id($id) {
        $id = clear_string($id);
 
        $st = $this->db->prepare("SELECT `victim_name` FROM `advanced_bans` WHERE `id` = :id");
        $st->bindParam(':id', $id);
        if ($st->execute()) {
	        foreach ($st->fetchAll() as $r) {
	            return $r['victim_name'];
	        }
	    } else {
	    	return 'Mysql error.';
	    }
    }

	public function paginate($pages, $limits, $count, $results) {
		//Extragerea datelor
		$targetpage = $pages;
		$limit = $limits;

		$total_pages = $count;
		$total_pages = $total_pages['total_results'];

		$stages = 3;
		$page = $_GET['pagina'];

		// Get page data
		$result = $results;

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
				$paginate.= "<li><a href='$targetpage&pagina=$prev'><i class='fa fa-angle-double-left' aria-hidden='true'></i></a></li>";
			} else {
				$paginate.= "<li class='disabled'><a href='#'><i class='fa fa-angle-double-left' aria-hidden='true'></i></a></li>";
			}

			// Pages
			if ($lastpage < 7 + ($stages * 2)) {
				for ($counter = 1; $counter <= $lastpage; $counter++) {
					if ($counter == $page) {
						$paginate.= "<li class='active'><a href='#'>$counter</a></li>";
					} else {
						$paginate.= "<li><a href='$targetpage&pagina=$counter'>$counter</a></li>";
					}
				}
			} else if ($lastpage > 5 + ($stages * 2)) {
				if($page < 1 + ($stages * 2)) {
					for ($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
						if ($counter == $page) {
							$paginate.= "<li class='active'><a href='#'>$counter</a></li>";
						} else {
							$paginate.= "<li><a href='$targetpage&pagina=$counter'>$counter</a></li>";
						}
					}
					$paginate.= "<li class='disabled'><a href='#'>...</a></li>";
					$paginate.= "<li><a href='$targetpage&pagina=$LastPagem1'>$LastPagem1</a></li>";
					$paginate.= "<li><a href='$targetpage&pagina=$lastpage'>$lastpage</a></li>";
				} elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
					$paginate.= "<li><a href='$targetpage&pagina=1'>1</a></li>";
					$paginate.= "<li><a href='$targetpage&pagina=2'>2</a></li>";
					$paginate.= "<li class='disabled'><a href='#'>...</a></li>";
					for ($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
						if ($counter == $page) {
							$paginate.= "<li class='active'><a href='#'>$counter</a></li>";
						} else {
							$paginate.= "<li><a href='$targetpage&pagina=$counter'>$counter</a></li>";}
						}
						$paginate.= "<li class='disabled'><a href='#'>...</a></li>";
						$paginate.= "<li><a href='$targetpage&pagina=$LastPagem1'>$LastPagem1</a></li>";
						$paginate.= "<li><a href='$targetpage&pagina=$lastpage'>$lastpage</a></li>";
					} else {
				$paginate.= "<li><a href='$targetpage&pagina=1'>1</a></li>";
				$paginate.= "<li><a href='$targetpage&pagina=2'>2</a></li>";
				$paginate.= "<li class='disabled'><a href='#'>...</a></li>";
				for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
					if ($counter == $page){
						$paginate.= "<li class='active'><a href='#'>$counter</a></li>";
					}else{
						$paginate.= "<li><a href='$targetpage&pagina=$counter'>$counter</a></li>";}
					}
				}
			}
			if ($page < $counter - 1) {
				$paginate.= "<li><a href='$targetpage&pagina=$next'><i class='fa fa-angle-double-right' aria-hidden='true'></i></a></li>";
			} else {
				$paginate.= "<li class='disabled'><a href='#'><i class='fa fa-angle-double-right' aria-hidden='true'></i></a></li>";
			}
			$paginate.= "</ul>";
		}
		return $paginate;
	}
}

class Select_count extends User {
	public function count_bans() {
		$st = $this->db->prepare("SELECT COUNT(*) AS `total_results` FROM `advanced_bans`");
		$st->execute();
		return $st->fetch(PDO::FETCH_ASSOC);
	}

	public function count_users() {
		$st = $this->db->prepare("SELECT COUNT(*) AS `total_results` FROM `admins`");
		$st->execute();
		return $st->fetch(PDO::FETCH_ASSOC);
	}
}

class Select extends User {
	public function select_logs() {
		$st = $this->db->prepare("SELECT * FROM `logs` ORDER BY `id` DESC LIMIT 100");
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_last_10_action($name) {
		$st = $this->db->prepare("SELECT * FROM `logs` WHERE `user` = :auth ORDER BY `id` DESC LIMIT 10");
		$st->bindParam(':auth', $name);
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_tabels_from_db() {
		$st = $this->db->prepare("SHOW TABLES");
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_buy_logs() {
		$st = $this->db->prepare("SELECT * FROM `shop_buy_log` ORDER BY `id` DESC LIMIT 100");
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_all_shop_items() {
		$st = $this->db->prepare("SELECT * FROM `shop_items`");
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_suspends() {
		$st = $this->db->prepare("SELECT * FROM `panel_suspend` ORDER BY `id` DESC LIMIT 100");
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_access_from_db() {
		$st = $this->db->prepare("SELECT * FROM `admin_access`");
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_news() {
		$st = $this->db->prepare("SELECT * FROM `news` ORDER BY `id` DESC LIMIT 5");
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function check_news() {
		$st = $this->db->prepare("SELECT * FROM `news`");
		$st->execute();
		if ($st->rowCount() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function select_profile_users_search($search) {
		$search = clear_string($search);
		$search = '%'.$search.'%';
		$st = $this->db->prepare("SELECT * FROM `admins` WHERE `auth` LIKE :name");
		$st->bindParam(':name', $search);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return $st->fetchAll(PDO::FETCH_ASSOC);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function select_bans($start, $limit) {
		$st = $this->db->prepare("SELECT * FROM `advanced_bans` ORDER BY `id` DESC LIMIT :start, :limit");
		$st->bindValue(':start', $start, PDO::PARAM_INT);
		$st->bindValue(':limit', $limit, PDO::PARAM_INT);
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_bans_search($search) {
		$search = clear_string($search);
		$search = '%'.$search.'%';
		$st = $this->db->prepare("SELECT * FROM `advanced_bans` WHERE `victim_name` LIKE :name OR `victim_steamid` LIKE :name");
		$st->bindParam(':name', $search);
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_users($start, $limit) {
		$st = $this->db->prepare("SELECT * FROM `admins` ORDER BY LENGTH(access) DESC, access ASC LIMIT :start, :limit");
		$st->bindValue(':start', $start, PDO::PARAM_INT);
		$st->bindValue(':limit', $limit, PDO::PARAM_INT);
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_users_2($start, $limit) {
		$st = $this->db->prepare("SELECT * FROM `admins` WHERE `access` != 'z' ORDER BY LENGTH(access) DESC, access ASC LIMIT :start, :limit");
		$st->bindValue(':start', $start, PDO::PARAM_INT);
		$st->bindValue(':limit', $limit, PDO::PARAM_INT);
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select_users_info($id) {
		$st = $this->db->prepare("SELECT * FROM `admins` WHERE `id` = :id");
		$st->bindValue(':id', $id, PDO::PARAM_INT);
		$st->execute();
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}
}

class Delete extends User {
	public function delete_ban($id) {
		$id 	= clear_string($id);
		$log = 'get unban ['.$this->get_username_ban_from_id($id).']';

		$st = $this->db->prepare("DELETE FROM `advanced_bans` WHERE `id` = :id;");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function delete_access_from_db($id) {
		$id = clear_string($id);
		$log = 'delete access ['.$this->get_access_name_from_id($id).']';

		$st = $this->db->prepare("DELETE FROM `admin_access` WHERE `id` = :id");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function delete_shop_item_from_db($id) {
		$id = clear_string($id);
		$log = 'delete item shop with name ['.$this->get_item_shop_produs_with_id($id).']';

		$st = $this->db->prepare("DELETE FROM `shop_items` WHERE `id` = :id");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function delete_suspend_from_db($id) {
		$id = clear_string($id);
		$log = 'delete access ['.$this->get_username_from_suspend_with_id($id).']';

		$st = $this->db->prepare("DELETE FROM `panel_suspend` WHERE `id` = :id");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function delete_user($id) {
		$id 	= clear_string($id);
		$log = 'delete user ['.$this->get_username_from_id($id).']';

		$st = $this->db->prepare("DELETE FROM `admins` WHERE `id` = :id;");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function delete_news($id) {
		$id 	= clear_string($id);
		$log = 'delete news ['.$this->get_news_title($id).']';

		$st = $this->db->prepare("DELETE FROM `news` WHERE `id` = :id;");
		$st->bindParam(':id', $id);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}
}

class Insert extends User {

	public function insert_ban($username, $steamid, $reason) {
		$username = clear_string($username);
		$steamid = clear_string($steamid);
		$reason = clear_string($reason);
		$banlength = '0';
		$unbantime = 'Permanent Ban';
		$admin_name = $this->get_username_from_id($_SESSION['id']);
		$admin_steamid = get_user_ip();
		$log = 'get ban ['.$username.']';

		$chk = $this->db->prepare("SELECT `victim_name` FROM `advanced_bans` WHERE `victim_steamid` = :name");
		$chk->bindParam(':name', $steamid);
		$chk->execute();

		if ($chk->rowCount() == 0) {
			$st = $this->db->prepare("INSERT INTO `advanced_bans` (victim_name, victim_steamid, banlength, unbantime, reason, admin_name, admin_steamid) VALUES (:name, :steam, :length, :unbantime, :reason, :name_admin, :admin_steam);");
			$st->bindParam(':name', $username);
			$st->bindParam(':steam', $steamid);
			$st->bindParam(':length', $banlength);
			$st->bindParam(':unbantime', $unbantime);
			$st->bindParam(':reason', $reason);
			$st->bindParam(':name_admin', $admin_name);
			$st->bindParam(':admin_steam', $admin_steamid);
			if ($st->execute()) {
				$this->set_log($log);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function insert_new_user($user, $pass, $email, $access, $flag, $type) {
		$username = clear_string($user);
		$password = clear_string($pass);
		$access = clear_string($access);
		$email = clear_string($email);
		$flag = clear_string($flag);
		$type = clear_string($type);
		$log = 'add new user ['.$username.'] with access ['.$access.'] and type ['.$type.']';

		$chk = $this->db->prepare("SELECT `id` FROM `admins` WHERE `auth` = :auth");
		$chk->bindParam(':auth', $username);
		$chk->execute();

		if ($chk->rowCount() == 0) {
			$st = $this->db->prepare("INSERT INTO `admins` (auth, password, access, flags, email, type) VALUES (:auth, :pass, :access, :flag, :email, :type);");
			$st->bindParam(':auth', $username);
			$st->bindParam(':pass', $password);
			$st->bindParam(':access', $access);
			$st->bindParam(':flag', $flag);
			$st->bindParam(':email', $email);
			$st->bindParam(':type', $type);
			if ($st->execute()) {
				$this->set_log($log);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function add_new_access($access, $name) {
		$access = clear_string($access);
		$name = clear_string($name);
		$log = 'add new access ['.$access.'] with name ['.$name.']';

		$st = $this->db->prepare("INSERT INTO `admin_access` (`access`, `name`) VALUES (:access, :name)");
		$st->bindParam(':access', $access);
		$st->bindParam(':name', $name);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function insert_news($title, $msg) {
		$title = clear_string($title);
		$msg = clear_string($msg);
		$msg = nl2br($msg);
		$log = 'add news with title ['.$title.']';

		$st = $this->db->prepare("INSERT INTO `news` (title, msg) VALUES (:title, :msg);");
		$st->bindParam(':title', $title);
		$st->bindParam(':msg', $msg);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function insert_new_item_shop($item_title, $item_desc, $item_price, $item_value, $item_query) {
		$item_title = clear_string($item_title);
		$item_desc = clear_string($item_desc);
		$item_price = clear_string($item_price);
		$item_value = clear_string($item_value);
		$item_query = clear_string($item_query);
		$item_desc = nl2br($item_desc);
		$log = 'add new item in shop with title ['.$item_title.'] and price ['.$item_price.']';

		$st = $this->db->prepare("INSERT INTO `shop_items` (produs, descriere, pret, value, by_auth, query) VALUES (:title, :descriere, :pret, :value, :by_auth, :query);");
		$st->bindParam(':title', $item_title);
		$st->bindParam(':descriere', $item_desc);
		$st->bindParam(':pret', $item_price);
		$st->bindParam(':value', $item_value);
		$st->bindParam(':by_auth', $this->get_username_from_id($_SESSION['id']));
		$st->bindParam(':query', $item_query);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}

	public function insert_suspend($user, $time, $reason) {
		$user = clear_string($user);
		$time1 = time();
		$time2 = clear_string($time);
		$reason = clear_string($reason);
		$auth = $this->get_username_from_id($_SESSION['id']);
		$log = 'add suspend ['.$user.']';

		$st = $this->db->prepare("INSERT INTO `panel_suspend` (username, suspend, suspenddown, reason, by_auth) VALUES (:user, :suspend, :sdown, :reason, :auth);");
		$st->bindParam(':user', $user);
		$st->bindParam(':suspend', $time1);
		$st->bindParam(':sdown', $time2);
		$st->bindParam(':reason', $reason);
		$st->bindParam(':auth', $auth);
		if ($st->execute()) {
			$this->set_log($log);
			return true;
		} else {
			return false;
		}
	}
}

function clear_string($string) {
	return htmlspecialchars(strip_tags($string));
}

function time_diff_conv($start, $s) {
	$t = array(
		'w' => 604800,
		'd' => 86400,
		'h' => 3600,
		'm' => 60,
	);
	$s = abs($s - $start);
	foreach($t as $key => &$val) {
		$$key = floor($s/$val);
		$s -= ($$key*$val);
		$string .= ($$key==0) ? '' : $$key . "$key ";
	}
	return $string . $s. 's';
}

function get_user_ip() {
	switch(true) {
		case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
		case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
		case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
		default : return $_SERVER['REMOTE_ADDR'];
	}
}

function get_flag_from_ip($ip) {
	if (strpos($ip, 'T') == 1) {
		return '<img src="icons/steam.png" width="18" height="18" title="'.$ip.'">';
	} else {
		$get_country = json_decode(file_get_contents("http://ipinfo.io/".$ip));
		$flag = $get_country->country;
		return '<img src="http://www.geognos.com/api/en/countries/flag/'.$flag.'.png" width="18" height="12" title="'.$ip.'">';
	}
}

function replace_char($array, $string) {
	return str_ireplace(array_keys($array),array_values($array), $string);
}

function suspend_time($time) {
	switch ($time) {
		case '86400':
			echo '1 zi';
			break;
		case '259200':
			echo '3 zile';
			break;
		case '432000':
			echo '5 zile';
			break;
		case '604800':
			echo '7 zile';
			break;
		case '1209600':
			echo '14 zile';
			break;
		case '2592000':
			echo '1 luna';
			break;
		case '999':
			echo 'Permanent';
			break;
		
		default:
			echo 'Perioada nedeterminata.';
			break;
	}
}

function user_type($type) {
	switch ($type) {
		case 'admin':
			echo 'Administrator';
			break;
		case 'mod':
			echo 'Moderator';
			break;
		case 'user':
			echo 'Membru';
			break;
		
		default:
			echo 'Unknow';
			break;
	}
}

function get_hours_from_minutes($minute) {
	$st = $minute / 60;
	return round($st, 2);
}

function nagitive_check($value){
	if (isset($value)){
		if (substr(strval($value), 0, 1) == "-"){
			return true;
		} else {
			return false;
		}
	}
}

function make_links($s) {
  return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
}

function get_steam_name($userID, $steamID, $steamLink = 1) {
	if (str_split($steamID, 5)[0] != "STEAM") {
		if ($userID == 0) {
			return $steamID;
		} else {
			return "<a href='profile&user_id=".$userID."'>".$steamID."</a>";
		}
	} else {
		$iServer = '0';
		$iAuthID = '0';
		$szTmp = strtok($steamID, ":");

		while(($szTmp = strtok(":")) !== false) {
			$szTmp2 = strtok(":");
			if($szTmp2 !== false) {
				$iServer = $szTmp;
				$iAuthID = $szTmp2;
			}
		}

		$steamId64 = bcmul($iAuthID, "2");
		$steamId64 = bcadd($steamId64, bcadd("76561197960265728", $iServer));
		if (strpos($steamId64, ".")) {
			$steamId64=strstr($steamId64,'.', true);
		}

		$xml = @simplexml_load_file("http://steamcommunity.com/profiles/$steamId64/?xml=1");

		if(!empty($xml) && !isset($xml->error)) {
			if( isset($xml->steamID) && $xml->steamID == "" ) {
				$username = "Not Setup"; // Example: steamcommunity.com/profiles/76561198077095013/?xml=1
			} else {
				if ($steamLink == 0) {
					$username = $xml->steamID;
				} else {
					$username = "<a href='http://steamcommunity.com/profiles/".$xml->steamID64."' target='_BLANK' title='".$steamID."'>".$xml->steamID."</a>";
				}
			}
		} else {
			$username = "Not Found"; // Example: steamcommunity.com/profiles/0/?xml=1
		}

		return $username;
	}
}
<?php
session_start();
ob_start();
error_reporting(0);

if (file_exists('./config.php')) {
	require_once("./config.php");
} else {
	header("Location: install.php");
}
require_once 'function/function.php';
require_once 'gameq/GameQ.php';
require_once 'PayPal/autoload.php';

$servers['user_panel'] = array(GQ_MOD, GQ_IP, GQ_PORT);

$payer = new \PayPal\Api\Payer();
$amount = new \PayPal\Api\Amount();
$transaction = new \PayPal\Api\Transaction();
$redirectUrls = new \PayPal\Api\RedirectUrls();
$payment = new \PayPal\Api\Payment();
$execute = new \PayPal\Api\PaymentExecution();

$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        P_KEY,
        P_SEC
    )
);

$apiContext->setConfig(
    array(
        'log.LogEnabled' => true,
        'log.FileName' => 'PayPal.log',
        'mode' => P_MODE,
        'log.LogLevel' => 'DEBUG'
    )
);

$gq 		= new GameQ;
$general 	= new User();
$select 	= new Select();
$count 		= new Select_count();
$delete 	= new Delete();
$insert 	= new Insert();

$errors		= array();
$succes		= array();

$gq->addServers($servers);

try {
	$data = array();
	$data = $gq->requestData();
	$stats = $data;
	$data = $data['user_panel']['players'];
	usort($data,
		function($a, $b) {
			return $a['score'] <= $b['score'];
		}
	);
} catch (GameQ_Exception $e) {
	echo 'An error occurred.';
}

//************************************88

//            Conditii                88

//************************************88


if (isset($_GET['buy'])) {
	$buy_id = $_GET['buy'];
	if (SHOP) {
		$item_price = $general->get_item_shop_price_with_id($buy_id);
		if (FOLDER === true) {
			$url_succes = "http://".$_SERVER['HTTP_HOST']."/".FOL_NAME."/shop&buy_id=".$buy_id."&status=succes";
			$url_reject = "http://".$_SERVER['HTTP_HOST']."/".FOL_NAME."/shop&buy_id=".$buy_id."&status=reject";
		} else {
			$url_succes = "http://".$_SERVER['HTTP_HOST']."/shop&buy_id=".$buy_id."&status=succes";
			$url_reject = "http://".$_SERVER['HTTP_HOST']."/shop&buy_id=".$buy_id."&status=reject";

		}


		if ($general->logged_in() === true) {
			if ($general->get_item_shop_value_with_id($buy_id) != $general->get_user_access_from_name($general->get_username_from_id($_SESSION['id']))) {
				$payer->setPaymentMethod("paypal");

				$item = $buy_id;
				$itemPrice = $item_price;
				$itemCurrency = 'EUR';

				$payer->setPaymentMethod('paypal');
				$amount->setTotal($itemPrice);
				$amount->setCurrency($itemCurrency);
				$transaction->setAmount($amount);
				$redirectUrls->setReturnUrl($url_succes)
				    ->setCancelUrl($url_reject);
				$payment->setIntent('sale')
				    ->setPayer($payer)
				    ->setTransactions(array($transaction))
				    ->setRedirectUrls($redirectUrls);


				try {
				    $payment->create($apiContext);
				} catch (Exception $ex) {
					// print "<pre>";
					// print_r($ex->getData());
					// print "</pre>";
					array_push($errors, 'A aparut o eroare la logarea in portofelul administratorului, incearca mai tarziu.');
				}

				$approvalUrl = $payment->getApprovalLink();
				header("location:".$approvalUrl);
			} else {
				array_push($errors, 'Achizitie nu a putut fi finalizata, deoarece detineti acest access.');
			}
		} else {
			array_push($errors, 'Obiectele din shop pot fi cumparate doar de utilizatorii logati.');
		}
	} else {
		array_push($errors, 'Shop-ul este dezactivat momentan.');
	}
}

if ($_GET['buy_id'] && $_GET['status'] === 'reject') {
	array_push($errors, 'Achizitie nu a putut fi finalizata.');
}

if ($_GET['buy_id'] && $_GET['status'] && $_GET['paymentId'] && $_GET['PayerID']) {
	$paymentId = $_GET["paymentId"];
	$payerId = $_GET["PayerID"];
	$buy_id = $_GET['buy_id'];

	if ($general->logged_in() === true) {
		$peym = $payment->get($paymentId, $apiContext);
		$execute->setPayerId($payerId);

		try {
			$result = $peym->execute($execute, $apiContext);
			//print_r($result->transactions[0]->related_resources[0]->sale->parent_payment);
			$price = $result->transactions[0]->amount->total;
			$payID = $result->transactions[0]->related_resources[0]->sale->parent_payment;
			if ($general->get_shop_log_paymentid($payID) != true) {
				if ($general->get_user_qery_from_shop($buy_id, $price) === true) {
					$general->set_shop_log($price, 'succes', $payID);
					array_push($succes, 'Achizitie finalizata cu succes! Total plata: '.$price.' euro.');
				} else {
					$general->set_shop_log($price, 'reject / frauda', $payID);
					array_push($errors, 'Achizitie nu a putut fi finalizata, contactati administratorul in cazul in care ati ramas fara suma de bani, pentru incercarea de fraudare, banii nu se returneaza!');
				}
			} else {
				array_push($succes, 'Achizitie finalizata cu succes! Total plata: '.$price.' euro.');
			}
		} catch(Exception $e) {
			$data = json_decode($e->getData());
			//var_dump($data->message);
			array_push($errors, $data->message);
		}
	}
}

if (isset($_GET['delete_item'])) {
	$id = $_GET['delete_item'];

	if (empty($id)) {
		array_push($errors, "Id invalid.");
	} else {
		if ($general->is_admin() === true) {
			if ($delete->delete_shop_item_from_db($id) === true) {
				header('Location: '.$_GET['page']);
			} else {
				array_push($errors, "Mysql error, try again.");
			}
		}
	}
}

if (isset($_POST['b_search'])) {
	$sc = $_POST['b_search'];

	header('Location: bans&search='.$sc);
}


if (isset($_POST['search_name'])) {
	$sc = $_POST['search_name'];

	header('Location: search&nickname='.$sc);
}

if (isset($_GET['delete_access'])) {
	$id = $_GET['delete_access'];

	if (empty($id)) {
		array_push($errors, "Id invalid.");
	} else {
		if ($general->is_admin() === true) {
			if ($delete->delete_access_from_db($id) === true) {
				header('Location: '.$_GET['page']);
			} else {
				array_push($errors, "Mysql error, try again.");
			}
		}
	}
}

if (isset($_GET['unban_request'])) {
	if ($general->logged_in() === true) {
		if ($general->check_is_banned() === true) {
			if ($cereri->check_time_unban_request() === true) {
				if ($cereri->check_unban_request_active() != true) {
					if ($_GET['unban_request'] == true) {
						$cereri->create_new_unban_request();
						array_push($succes, "Cererea de unban a fost trimisa cu succes!");
					} else {
						array_push($errors, "Nu incerca sa modifici valorile prestabilite!");
					}
				} else {
					array_push($errors, "Deja ai o cerere de unban deschisa!");
				}
			} else {
				array_push($errors, "Poti face o noua cerere peste 3 zile!");
			}
		} else {
			array_push($errors, "Doar jucatorii cu ban pot face cereri de debanare!");
		}
	} else {
		array_push($errors, "Logheaza-te pentru a beneficia de aceasta functie!");
	}
}

if (isset($_GET['delete_suspend'])) {
	$id = $_GET['delete_suspend'];

	if (empty($id)) {
		array_push($errors, "Id invalid.");
	} else {
		if ($general->is_admin() === true) {
			if ($delete->delete_suspend_from_db($id) === true) {
				header('Location: '.$_GET['page']);
			} else {
				array_push($errors, "Mysql error, try again.");
			}
		}
	}
}

if (isset($_POST['status_user_search'])) {
	
	$sc = $_POST['status_user_search'];

	header('Location: stats&search='.$sc);
	
}

if (isset($_POST['update_access_btn'])) {
	$access = $_POST['access_access'];
	$access_name = $_POST['access_name'];
	$access_id = $_POST['access_id'];

	if (empty($access) || empty($access_name) || empty($access_id)) {
		array_push($errors, "Completeaza toate campurile.");
	} else {
		if ($general->is_admin() === true) {
			if ($general->update_admin_access($access, $access_name, $access_id) == true) {
				array_push($succes, "Access editat cu success!");
			} else {
				array_push($errors, "Acest access exista deja in baza de date.");
			}
		}
	}
}

if (isset($_POST['change_avatar_btn'])) {
	$avatar = $_FILES['change_avatar'];
	$name = $general->get_user_id_from_name($_POST['name']);

	if ($_SESSION['id'] == $name || $general->get_user_type_from_id($_SESSION['id']) == 'admin') {
	    if (empty($avatar)) {
	        array_push($errors, "Alege un fisier.");
	    } else {
	        $fileName = rand(1000,100000)."-".rand(1000,100000).".png";
	        $check = getimagesize($avatar["tmp_name"]);
	        $maxsize = 2097152;
	        $image_width = $check[0];
	        $image_height = $check[1];

	        if ($avatar["error"] > 0) {
	            array_push($errors, "Eroare, fisierul nu este acceptat.");
	        } else {
	            if ($image_width > '150' && $image_height > '250') {
	                array_push($errors, "Dimensiunea maxima este de 150x250.");
	            } else {
	                if (($avatar['size'] >= $maxsize) || ($avatar["size"] == 0)) {
	                    array_push($errors, "Dimensinuea maxima a fisierului nu trebuie sa depaseasca 2MB.");
	                } else {
	                    $extension = pathinfo($avatar["name"], PATHINFO_EXTENSION);
	                    if ($extension == 'png') {
	                        if (move_uploaded_file($avatar["tmp_name"], "icons/avatars/" . $fileName)) {
	                        	if ($general->check_avatar($name) != 'default.png') {
	                            	unlink("icons/avatars/".$general->check_avatar($name));
		                            $general->change_avatar($fileName, $name);
		                            array_push($succes, "Avatar schimbat cu succes!");
	                            } else {
		                            $general->change_avatar($fileName, $name);
		                            array_push($succes, "Avatar schimbat cu succes!");
	                            }
	                        } else {
	                            array_push($errors, "Fisierul nu a putut fi mutat, mai incearca odata!");
	                        }
	                    } else {
	                        array_push($errors, "Sunt permise doar imagini de tip PNG!");
	                    }
	                }
	            }
	        }
	    }
	} else {
		array_push($errors, "Nu poti face acest lucru.");
	}
}

if (isset($_GET['warn_user'])) {

	$id = $_GET['warn_user'];

	if ($general->is_admin() === true || $general->is_mod() === true) {

		if (empty($id)) {

			array_push($errors, "ID is not found.");

		} else {

			if ($general->staff_get_warn($id) === true) {

				header('Location: staff');

			} else {

				array_push($errors, "Mysql error, try again.");

			}

		}

	}

}


if (isset($_POST['add_access_btn'])) {

	$acc_access = $_POST['add_access_access'];

	$acc_name = $_POST['add_access_name'];

	if (empty($acc_access) || empty($acc_name)) {

		array_push($errors, "Completeaza toate campurile.");

	} else {

		if ($insert->add_new_access($acc_access, $acc_name) == true) {

			array_push($succes, "Acces-ul a fost adaugat cu succes!");

		} else {

			array_push($errors, "Mysql error, try again.");

		}

	}

}


if (isset($_GET['unwarn_user'])) {

	$id = $_GET['unwarn_user'];

	if ($general->is_admin() === true || $general->is_mod() === true) {

		if (empty($id)) {

			array_push($errors, "ID is not found.");

		} else {

			if ($general->staff_get_unwarn($id) === true) {

				header('Location: staff');

			} else {

				array_push($errors, "Mysql error, try again.");

			}

		}

	}

}



if (isset($_GET['delete_user'])) {

	$id = $_GET['delete_user'];



	if ($general->is_admin() === true) {

		if (empty($id)) {

			array_push($errors, "ID is not found.");

		} else {

			if ($delete->delete_user($id) === true) {

				header('Location: staff');

			} else {

				array_push($errors, "Mysql error, try again.");

			}

		}

	}

}



if (isset($_GET['delete_news'])) {

	$id = $_GET['delete_news'];



	if ($general->is_admin() === true || $general->is_mod() === true) {

		if (empty($id)) {

			array_push($errors, "ID is not found.");

		} else {

			if ($delete->delete_news($id) === true) {

				header('Location: home');

			} else {

				array_push($errors, "Mysql error, try again.");

			}

		}

	}

}



if (isset($_POST['update_staff_btn'])) {

	$user 	= $_POST['staff_user'];

	$pass 	= $_POST['staff_pass'];

	$email 	= $_POST['staff_email'];

	$access = $_POST['staff_access'];

	$flags 	= $_POST['staff_flags'];

	$type 	= $_POST['staff_type'];

	$u_id 	= $_POST['staff_user_id'];



	if ($general->is_admin() === true) {

		if (empty($user) || empty($pass) || empty($email) || empty($access) || empty($flags) || empty($type) || empty($u_id)) {

			array_push($errors, "Completeaza toate campurile.");

		} else {

			if ($general->update_user($user, $pass, $email, $access, $flags, $type, $u_id) === true) {

				array_push($succes, "User editat cu succes!");

			} else {

				array_push($errors, "Ceva nu a mers bine, incearca mai tarziu.");

			}

		}

	}

}


if (isset($_POST['login_btn'])) {
	$user = $_POST['login_username'];
	$pass = $_POST['login_password'];

	if (!empty($user) || !empty($pass)) {
		if ($general->logged_in() != true) {
			if ($general->Login($user, $pass) != true) {
				header('Location: home');
				array_push($succes, "Te-ai autentificat cu succes!");
			} else {
				array_push($errors, "Numele sau parola sunt gresite.");
			}
		} else {
			array_push($errors, "Nu te poti conecta de mai multe ori.");
		}
	} else {
		array_push($errors, "Completeaza toate campurile.");
	}
}



if (isset($_GET['delete_ban'])) {

	$name = $_GET['delete_ban'];



	if ($general->is_admin() === true || $general->is_mod() === true) {

		if ($delete->delete_ban($name) === true) {

			array_push($succes, "Banul a fost scos cu succes!");

		} else {
			array_push($errors, "Mysql error, try again.");
		}

	}

}



if (isset($_POST['contact_btn'])) {

	$nume = clear_string($_POST['contact_user']);

	$email = clear_string($_POST['contact_email']);

	$mesaj = clear_string($_POST['contact_msg']);



	//Daca se lasa spatii libere apar urmatoarele mesaje

	if (empty($nume)) { array_push($errors, "Introdu numele."); }

	if (empty($email)) { array_push($errors, "Introdu email-ul tau."); }

	if (empty($mesaj)) { array_push($errors, "Introdu mesajul."); }



	//Daca nu sunt spatii libere continua cu blocul de comenzi

	if (count($errors) == 0) {

		//Verifica daca e-mail-ul este corect

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

			array_push($errors, "Email-ul introdus nu este valid.");

		} else {

			//Daca nu exista erori trece la urmatorul bloc de comenzi

			if (empty($errors)) {

				//Catre cine trimite e-mail

				$to = WEB_EMAIL; 

				//Subiectul din e-mail

				$email_subject = "Contact form: " . $nume . ".";

				//Continutul mesajului din e-mail

				$email_body = "

					<h4>Ai fost contactat de catre " . $nume . ", acesta dorind sa ia legatura cu tine.</h4><br>

					<br>

					<b>Mesaj</b>: " . $mesaj . "<br><br>

					<b>E-mail</b>: " . $email;

				//Indicam ca folosim 2 tipuri de scriere (text si html)

				$headers = 'Content-type: text/html; charset=iso-8859-1' . "\n"; 

				

				//Trimiterea mesajului

				mail($to, $email_subject, $email_body, $headers);



				//Mesaj de succes aratat persoanei

				array_push($succes, "Email trimis cu succes!");

			} else {

				//Mesaj de eroare, daca nu s-a putu trimite e-mail-ul

				array_push($errors, "A aparut o eroare, mai incearca odata.");

			}

		}

	}

}



if (isset($_POST['reg_btn'])) {
	$user = $_POST['reg_user'];
	$email = $_POST['reg_email'];
	$pass = $_POST['reg_pass'];
	$pass_2 = $_POST['reg_pass_2'];

	if (empty($user) || empty($email) || empty($pass) || empty($pass_2)) {
		array_push($errors, "Completeaza toate campurile.");
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		array_push($errors, "Email-ul introdus nu este valid.");
	} else if ($pass != $pass_2) {
		array_push($errors, "Cele doua parole nu corespund.");
	} else if ($general->logged_in() === true) {
		array_push($errors, "Nu poti inregistra un cont cand esti logat.");
	} else {
		if ($general->Register($user, $email, $pass) === true) {
			array_push($succes, "Te-ai inregistrat cu succes.");
		} else {
			array_push($errors, "Acest nume exista deja in baza de date.");
		}
	}
}



if (isset($_POST['f_p_btn'])) {

	$username 	= $_POST['f_p_user'];

	$email 	= $_POST['f_p_email'];



	if (empty($username) || empty($email)) {

		array_push($errors, "Completeaza toate campurile.");

	} else {

		if ($general->forgot_password($username, $email) === true) {

			array_push($succes, "Un email a fost trimis la adresa specificate.");

		} else {

			array_push($errors, "Numele sau email-ul sunt gresite.");

		}

	}

}



if (isset($_POST['change_password_btn'])) {

	$old_pass 	= $_POST['change_old_pass'];

	$new_pass 	= $_POST['change_new_pass'];

	$new_pass_2	= $_POST['change_new_pass_2'];



	if (empty($old_pass) || empty($new_pass) || empty($new_pass_2)) {

		array_push($errors, "Completeaza toate campurile.");

	} else {

		if ($new_pass != $new_pass_2) {

			array_push($errors, "Cele doua parole nu corespund.");

		} else {

			if ($general->change_password($old_pass, $new_pass) === true) {

				array_push($succes, "Ai schimbat parola cu succes!");

			} else {

				array_push($errors, "Vechea parola este incorecta.");

			}

		}

	}

}

if (isset($_POST['change_name_btn'])) {
	$new_name 	= $_POST['change_new_name'];
	$new_name_2 = $_POST['change_new_name_2'];

	if (empty($new_name) || empty($new_name_2)) {
		array_push($errors, "Completeaza toate campurile.");
	} else {
		if ($new_name != $new_name_2) {
			array_push($errors, "Cele doua username-uri nu corespund.");
		} else {
			if ($general->change_username($new_name) === true) {
				array_push($succes, "Ai schimbat username-ul cu succes!");
			} else {
				array_push($errors, "Acest username exista deja.");
			}
		}
	}
}



if (isset($_POST['c_e_btn'])) {

	$old_email 		= $_POST['c_o_e'];

	$new_email 		= $_POST['c_n_e'];

	$new_email_2 	= $_POST['c_n_e_2'];



	if (empty($old_email) || empty($new_email) || empty($new_email_2)) {

		array_push($errors, "Completeaza toate campurile.");

	} else {

		if ($new_email != $new_email_2) {

			array_push($errors, "Cele doua email-uri nu corespund.");

		} else {

			if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {

				array_push($errors, "Noul email nu este valid.");

			} else {

				if ($general->change_email($old_email, $new_email) === true) {

					array_push($succes, "Ai schimbat email-ul cu succes!");

				} else {

					array_push($errors, "Vechiul email este incorect.");

				}

			}

		}

	}

}



if (isset($_POST['add_ban_btn'])) {

	$username = $_POST['add_ban_username'];

	$steamid = $_POST['add_ban_steamid'];

	$reason = $_POST['add_ban_reason'];



	if ($general->is_admin() === true || $general->is_mod() === true) {

		if (empty($username) || empty($steamid) || empty($reason)) {

			array_push($errors, "Completeaza toate campurile.");

		} else {

			if ($insert->insert_ban($username, $steamid, $reason) === true) {

				array_push($succes, "Utilizator banat cu succes!");

			} else {

				array_push($errors, "SteamID-ul sau ip-ul exista deja.");

			}

		}

	}

}



if (isset($_POST['add_admin_btn'])) {

	$username	= $_POST['add_admin_user'];

	$password	= $_POST['add_admin_pass'];

	$email		= $_POST['add_admin_email'];

	$access		= $_POST['add_admin_access'];

	$flag		= $_POST['add_admin_flag'];

	$type		= $_POST['add_admin_type'];



	if ($general->is_admin() === true) {

		if (empty($username) || empty($password) || empty($email) || empty($access) || empty($flag) || empty($type)) {

			array_push($errors, "Completeaza toate campurile.");

		} else {

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

				array_push($errors, "Acest enail nu este valid.");

			} else {

				if ($insert->insert_new_user($username, $password, $email, $access, $flag, $type)) {

					array_push($succes, "Membru adaugat cu succes!");

				} else {

					array_push($errors, "Acest username exista deja.");

				}

			}

		}

	}

}



if (isset($_POST['add_news_btn'])) {

	$title = $_POST['add_news_title'];

	$msg = $_POST['add_news_msg'];



	if ($general->is_admin() === true || $general->is_mod() === true) {

		if (empty($title) || empty($msg)) {

			array_push($errors, "Completeaza toate campurile.");

		} else {

			if ($insert->insert_news($title, $msg) === true) {

				array_push($succes, "Noutate adaugata cu succes!");

			} else {

				array_push($errors, "Mysql error, try again.");

			}

		}

	}

}

if (isset($_POST['add_shopitem_btn'])) {
	$item_title	 = $_POST['add_shopitem_title'];
	$item_desc	 = $_POST['add_shopitem_description'];
	$item_price  = $_POST['add_shopitem_price'];
	$item_table  = $_POST['add_shopitem_table'];
	$item_column = $_POST['add_shopitem_column'];
	$item_column_2 = $_POST['add_shopitem_column_2'];
	$item_value = $_POST['add_shopitem_value'];
	$item_query = "UPDATE `".$item_table."` SET `".$item_column."` = :value WHERE `".$item_column_2."` = :auth;";

	if ($general->is_admin() === true || $general->is_mod() === true) {
		if (empty($item_title) || empty($item_desc) || empty($item_price) || empty($item_table) || empty($item_column) || empty($item_column_2) || empty($item_value)) {
			array_push($errors, "Completeaza toate campurile.");
		} else {
			if ($general->check_column($item_column, $item_table) === true && $general->check_column($item_column_2, $item_table) === true ) {
				if ($insert->insert_new_item_shop($item_title, $item_desc, $item_price, $item_value, $item_query) === true) {
					array_push($succes, "Item adaugata cu succes in shop!");
				} else {
					array_push($errors, "Mysql error, try again.");
				}
			} else {
				array_push($errors, "Una din coloanele respective nu exista in tabel.");
			}
		}
	}
}



if (isset($_POST['add_suspend_btn'])) {

	$username = $_POST['add_suspend_user'];

	$durata = $_POST['add_suspend_time'];

	$reason = $_POST['add_suspend_reason'];



	if ($general->is_admin() === true || $general->is_mod() === true) {

		if (empty($username) || empty($durata) || empty($reason)) {

			array_push($errors, "Completeaza toate campurile.");

		} else {

			if ($general->exist_user($username) == true) {

				if ($general->user_suspend_panel($username) != false) {

					array_push($errors, "Acest utilizator a mai fost suspendat!");

				} else {

					if ($insert->insert_suspend($username, $durata, $reason) === true) {

						array_push($succes, "Suspend adaugat cu succes!");

					} else {

						array_push($errors, "Mysql error, try again.");

					}

				}

			} else {

				array_push($errors, "Acest utilizator nu extista!");

			}

		}

	}

}

if (isset($_POST['new_admin_request_btn'])) {
	$name = $_POST['new_admin_request_name'];
	$age = $_POST['new_admin_request_age'];
	$rules = $_POST['new_admin_request_q1'];
	$activ = $_POST['new_admin_request_q2'];
	$comand = $_POST['new_admin_request_q3'];

	if ($general->logged_in() === true) {
		if ($general->server_admin() != true) {
			if (empty($name) || empty($age) || empty($rules) || empty($activ) || empty($comand)) {
				array_push($errors, 'Completeaza toate campurile!');
			} else {
				if ($cereri->check_time() === true) {
					if ($general->check_is_banned() === true) {
						array_push($errors, 'Nu poti face cereri admin cand ai banul activ!');
					} else {
						if ($age >= 16) {
							if ($rules === 'Yes') {
								if ($cereri->create_new_application($name, $age, $rules, $activ, $comand) === true) {
									array_push($succes, 'Aplicatie creata cu succes!');
								} else {
									array_push($errors, 'Ceva nu a functionat cum trebuie, incearca mai tarziu.');
								}
							} else {
								array_push($errors, 'Citeste regulamentul inainte de a aplica!');
							}
						} else {
							array_push($errors, 'Varsta minima este de 16 ani!');
						}
					}
				} else {
					array_push($errors, 'Poti face o aplicatie odata la 3 zile.');
				}
			}
		} else {
			array_push($errors, 'Nu poti crea o cerere de admin daca ai deja admin.');
		}
	} else {
		array_push($errors, 'Poti aplica doar daca esti logat!');
	}
}

if (isset($_POST['new_ticket_request_btn'])) {
	$problem = $_POST['new_ticket_request_name'];

	if ($general->logged_in() === true) {
		if (empty($problem)) {
			array_push($errors, 'Completeaza toate campurile!');
		} else {
			if ($cereri->check_time_ticket() === true) {
				if ($cereri->create_new_ticket($problem) === true) {
					array_push($succes, 'Ai deschis un ticket cu succes!');
				} else {
					array_push($errors, 'Ceva nu a functionat cum trebuie, incearca mai tarziu.');
				}
			} else {
				array_push($errors, 'Poti face o aplicatie odata la 3 zile.');
			}
		}
	} else {
		array_push($errors, 'Poti aplica doar daca esti logat!');
	}
}

if (isset($_POST['new_admin_request_comment_btn'])) {
	$vote = $_POST['new_admin_request_vote'];
	$comm = $_POST['new_admin_request_comment'];
	$id = $_POST['new_admin_request_application_id'];

	if ($general->logged_in() === true) {
		if ($general->server_admin() == true) {
			if (empty($vote) || empty($comm) || empty($id)) {
				array_push($errors, 'Completeaza toate campurile!');
			} else {
				if ($cereri->check_application_status($id) === true) {
					if ($cereri->check_application_comment_admin($id) != true) {
						if ($vote == 'pro' || $vote == 'contra') {
							if ($cereri->add_new_comment_to_application($vote, $comm, $id) === true) {
								array_push($succes, 'Comentariu adaugat cu succes!');
							} else {
								array_push($errors, 'Ceva nu a functionat cum trebuie, incearca mai tarziu.');
							}
						} else {
							array_push($errors, 'Voturile permise sunt doar "pro" si "contra"!');
						}
					} else {
						array_push($errors, 'Ai dreptul de a vota o singura data!');
					}
				} else {
					array_push($errors, 'Aplicatia este inchisa, nu mai poti comenta la aceasta!');
				}
			}
		} else {
			array_push($errors, 'Doar adminii au permisiunea de a vota si comenta.');
		}
	} else {
		array_push($errors, 'Poti aplica doar daca esti logat!');
	}
}

if (isset($_POST['new_ticket_request_comment_btn'])) {
	$vote = $_POST['new_ticket_request_vote'];
	$comm = $_POST['new_ticket_request_comment'];
	$id = $_POST['new_ticket_request_application_id'];

	if ($general->logged_in() === true) {
		if (empty($vote) || empty($comm) || empty($id)) {
			array_push($errors, 'Completeaza toate campurile!');
		} else {
			if ($cereri->check_ticket_status($id) === true) {
				if ($cereri->check_ticket_comment_admin($id) != true) {
					if ($vote == 'pro' || $vote == 'contra') {
						if ($cereri->add_new_comment_to_ticket($vote, $comm, $id) === true) {
							array_push($succes, 'Comentariu adaugat cu succes!');
						} else {
							array_push($errors, 'Ceva nu a functionat cum trebuie, incearca mai tarziu.');
						}
					} else {
						array_push($errors, 'Voturile permise sunt doar "pro" si "contra"!');
					}
				} else {
					array_push($errors, 'Ai dreptul de a vota o singura data!');
				}
			} else {
				array_push($errors, 'Aplicatia este inchisa, nu mai poti comenta la aceasta!');
			}
		}
	} else {
		array_push($errors, 'Poti aplica doar daca esti logat!');
	}
}

if (isset($_POST['new_unban_request_comment_btn'])) {
	$vote = $_POST['new_unban_request_vote'];
	$comm = $_POST['new_unban_request_comment'];
	$id = $_POST['new_unban_request_application_id'];

	if ($general->logged_in() === true) {
		if ($general->server_admin() == true) {
			if (empty($vote) || empty($comm) || empty($id)) {
				array_push($errors, 'Completeaza toate campurile!');
			} else {
				if ($cereri->check_unban_request_status($id) === true) {
					if ($cereri->check_unban_request_comment_admin($id) != true) {
						if ($vote == 'pro' || $vote == 'contra') {
							if ($cereri->add_new_comment_to_unban_request($vote, $comm, $id) === true) {
								array_push($succes, 'Comentariu adaugat cu succes!');
							} else {
								array_push($errors, 'Ceva nu a functionat cum trebuie, incearca mai tarziu.');
							}
						} else {
							array_push($errors, 'Voturile permise sunt doar "pro" si "contra"!');
						}
					} else {
						array_push($errors, 'Ai dreptul de a vota o singura data!');
					}
				} else {
					array_push($errors, 'Aplicatia este inchisa, nu mai poti comenta la aceasta!');
				}
			}
		} else {
			array_push($errors, 'Doar adminii au permisiunea de a vota si comenta.');
		}
	} else {
		array_push($errors, 'Poti aplica doar daca esti logat!');
	}
}

?>
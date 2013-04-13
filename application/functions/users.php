<?php

if (!defined("MW_DB_TABLE_USERS")) {
	define('MW_DB_TABLE_USERS', MW_TABLE_PREFIX . 'users');
}
if (!defined("MW_DB_TABLE_LOG")) {
	define('MW_DB_TABLE_LOG', MW_TABLE_PREFIX . 'log');
}
action_hook('mw_db_init_users', 'mw_db_init_users_table');

function mw_db_init_users_table() {
	$function_cache_id = false;

	$args = func_get_args();

	foreach ($args as $k => $v) {

		$function_cache_id = $function_cache_id . serialize($k) . serialize($v);
	}

	$function_cache_id = __FUNCTION__ . crc32($function_cache_id);

	$cache_content = cache_get_content($function_cache_id, 'db');

	if (($cache_content) != false) {

		return $cache_content;
	}

	$table_name = MW_DB_TABLE_USERS;

	$fields_to_add = array();

	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('expires_on', 'datetime default NULL');
	$fields_to_add[] = array('last_login', 'datetime default NULL');
	$fields_to_add[] = array('last_login_ip', 'TEXT default NULL');

	$fields_to_add[] = array('created_by', 'int(11) default NULL');

	$fields_to_add[] = array('edited_by', 'int(11) default NULL');

	$fields_to_add[] = array('username', 'TEXT default NULL');

	$fields_to_add[] = array('password', 'TEXT default NULL');
	$fields_to_add[] = array('email', 'TEXT default NULL');

	$fields_to_add[] = array('is_active', "char(1) default 'n'");
	$fields_to_add[] = array('is_admin', "char(1) default 'n'");
	$fields_to_add[] = array('is_verified', "char(1) default 'n'");
	$fields_to_add[] = array('is_public', "char(1) default 'y'");

	$fields_to_add[] = array('basic_mode', "char(1) default 'n'");

	$fields_to_add[] = array('first_name', 'TEXT default NULL');
	$fields_to_add[] = array('last_name', 'TEXT default NULL');
	$fields_to_add[] = array('thumbnail', 'TEXT default NULL');

	$fields_to_add[] = array('parent_id', 'int(11) default NULL');

	$fields_to_add[] = array('api_key', 'TEXT default NULL');

	$fields_to_add[] = array('user_information', 'TEXT default NULL');
	$fields_to_add[] = array('subscr_id', 'TEXT default NULL');
	$fields_to_add[] = array('role', 'TEXT default NULL');
	$fields_to_add[] = array('medium', 'TEXT default NULL');

	$fields_to_add[] = array('oauth_uid', 'TEXT default NULL');
	$fields_to_add[] = array('oauth_provider', 'TEXT default NULL');
	$fields_to_add[] = array('oauth_token', 'TEXT default NULL');
	$fields_to_add[] = array('oauth_token_secret', 'TEXT default NULL');

	$fields_to_add[] = array('profile_url', 'TEXT default NULL');
	$fields_to_add[] = array('website_url', 'TEXT default NULL');
	$fields_to_add[] = array('password_reset_hash', 'TEXT default NULL');

	set_db_table($table_name, $fields_to_add);

	db_add_table_index('username', $table_name, array('username(255)'));
	db_add_table_index('email', $table_name, array('email(255)'));

	if (MW_IS_INSTALLED != true) {

		if (isset($_POST['admin_username']) and isset($_POST['admin_password'])) {

			$new_admin = array();
			$new_admin['username'] = $_POST['admin_username'];
			$new_admin['password'] = $_POST['admin_password'];
			$new_admin['is_active'] = 'y';
			$new_admin['is_admin'] = 'y';
			mw_var('FORCE_SAVE', MW_TABLE_PREFIX . 'users');
			save_user($new_admin);

		}

	}

	$table_name = MW_DB_TABLE_LOG;

	$fields_to_add = array();

	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('created_by', 'int(11) default NULL');
	$fields_to_add[] = array('edited_by', 'int(11) default NULL');
	$fields_to_add[] = array('rel', 'TEXT default NULL');

	$fields_to_add[] = array('rel_id', 'TEXT default NULL');
	$fields_to_add[] = array('position', 'int(11) default NULL');

	$fields_to_add[] = array('field', 'longtext default NULL');
	$fields_to_add[] = array('value', 'TEXT default NULL');

	$fields_to_add[] = array('data_type', 'TEXT default NULL');
	$fields_to_add[] = array('title', 'longtext default NULL');
	$fields_to_add[] = array('description', 'TEXT default NULL');
	$fields_to_add[] = array('content', 'TEXT default NULL');
	$fields_to_add[] = array('user_ip', 'TEXT default NULL');

	set_db_table($table_name, $fields_to_add);

	cache_save(true, $function_cache_id, $cache_group = 'db');
	// $fields = (array_change_key_case ( $fields, CASE_LOWER ));
	return true;

	//print '<li'.$cls.'><a href="'.admin_url().'view:settings">newsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl eter</a></li>';
}

api_expose('delete_log_entry');

function delete_log_entry($data) {
	$adm = is_admin();
	if ($adm == false) {
		error('Error: not logged in as admin.');
	}

	if (isset($data['id'])) {
		$c_id = intval($data['id']);
		db_delete_by_id('log', $c_id);
		return $c_id;

	}
	return $data;
}

function save_log($params) {
	$params = parse_params($params);

	$params['user_ip'] = USER_IP;
	$data_to_save = $params;
	$table = MW_DB_TABLE_LOG;
 						mw_var('FORCE_SAVE', $table);
	$save = save_data($table, $params);
	$id = $save;
	cache_clean_group('log' . DIRECTORY_SEPARATOR . 'global');
	return $id;
}

function get_log($params) {
	$params = parse_params($params);
	$table = MW_DB_TABLE_LOG;
	$params['table'] = $table;

	if (is_admin() == false) {
		$params['user_ip'] = USER_IP;
	}

	$q = get($params);

	return $q;
}

api_expose('delete_user');

function delete_user($data) {
	$adm = is_admin();
	if ($adm == false) {
		error('Error: not logged in as admin.');
	}

	if (isset($data['id'])) {
		$c_id = intval($data['id']);
		db_delete_by_id('users', $c_id);
		return $c_id;

	}
	return $data;
}

//api_expose('register_user');
api_expose('register_user');

function register_user($params) {

	$user = isset($params['username']) ? $params['username'] : false;
	$pass = isset($params['password']) ? $params['password'] : false;
	$email = isset($params['email']) ? $params['email'] : false;

	if (!isset($params['captcha'])) {
		return array('error' => 'Please enter the captcha answer!');
	} else {
		$cap = session_get('captcha');
		if ($cap == false) {
			return array('error' => 'You must load a captcha first!');
		}
		if ($params['captcha'] != $cap) {
			return array('error' => 'Invalid captcha answer!');
		}
	}
	if (!isset($params['password'])) {
		return array('error' => 'Please set password!');
	} else {
		if ($params['password'] == '') {
			return array('error' => 'Please set password!');
		}
	}

	if ($email != false) {

		$data = array();
		$data['email'] = $email;
		$data['password'] = $pass;
		$data['oauth_uid'] = '[null]';
		$data['oauth_provider'] = '[null]';

		// $data ['is_active'] = 'y';
		$data = get_users($data);
		if (empty($data)) {

			$data = array();
			$data['username'] = $email;
			$data['password'] = $pass;
			$data['oauth_uid'] = '[null]';
			$data['oauth_provider'] = '[null]';

			// $data ['is_active'] = 'y';
			$data = get_users($data);
		}

		if (empty($data)) {
			$data = array();
			$data['username'] = $email;
			$data['password'] = $pass;
			$data['is_active'] = 'n';

			$table = MW_TABLE_PREFIX . 'users';

			$q = " INSERT INTO  $table set email='$email',  password='$pass',   is_active='n' ";
			$next = db_last_id($table);
			$next = intval($next) + 1;
			$q = "INSERT INTO $table (id,email, password, is_active)
			VALUES ($next, '$email', '$pass', 'n')";
			db_q($q);
			cache_clean_group('users' . DIRECTORY_SEPARATOR . 'global');
			//$data = save_user($data);
			session_del('captcha');

			$notif = array();
			$notif['module'] = "users";
			$notif['rel'] = 'users';
			$notif['rel_id'] = $next;
			$notif['title'] = "New user registration";
			$notif['description'] = "You have new user registration";
			$notif['content'] = "You have new user registered with the username [" . $data['username'] . '] and id [' . $next . ']';
			post_notification($notif);

			return array($next);
		} else {
			return array('error' => 'This user already exists!');
		}
	}
}

api_expose('save_user');

function save_user($params) {

	if (isset($params['id'])) {
		//error('COMLETE ME!!!! ');

		$adm = is_admin();
		if ($adm == false) {
			error('Error: not logged in as admin.');
		}
	} else {
		if (MW_IS_INSTALLED == true) {
			error('COMLETE ME!!!! ');
		}
	}

	$data_to_save = $params;

	$table = MW_DB_TABLE_USERS;
	$save = save_data($table, $data_to_save);
	$id = $save;
	cache_clean_group('users' . DIRECTORY_SEPARATOR . 'global');
	cache_clean_group('users' . DIRECTORY_SEPARATOR . '0');
	cache_clean_group('users' . DIRECTORY_SEPARATOR . $id);
	return $id;
}

api_expose('captcha');

function captcha_vector($palette, $startx, $starty, $angle, $length, $colour) {
	$angle = deg2rad($angle);
	$endx = $startx + cos($angle) * $length;
	$endy = $starty - sin($angle) * $length;
	return (imageline($palette, $startx, $starty, $endx, $endy, $colour));
}

function captcha() {
	$roit1 = rand(1, 6);
	$font = INCLUDES_DIR . DS . 'admin' . DS . 'catcha_fonts' . DS . 'font' . $roit1 . '.ttf';
	$font = normalize_path($font, 0);

	header("Content-type: image/png");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	$text1 = mt_rand(100, 4500);
	$text2 = mt_rand(2, 9);
	$roit = mt_rand(1, 5);
	$text = "$text1";
	$answ = $text1;
	$x = 100;
	$y = 20;
	$image = @imagecreate($x, 20) or die("Unable to render a CAPTCHA picture!");

	$tcol1z = rand(1, 150);
	$ttcol1z1 = rand(0, 150);
	$tcol1z11 = rand(0, 150);

	$bgcolor = imagecolorallocate($image, 255, 255, 255);
	// $black = imagecolorallocate($image, $tcol1z, $ttcol1z1, $tcol1z11);
	$black = imagecolorallocate($image, 0, 0, 0);
	session_set('captcha', $answ);

	$col1z = rand(200, 242);
	$col1z1 = rand(150, 242);
	$col1z11 = rand(150, 242);
	$color1 = imagecolorallocate($image, $col1z, $col1z1, $tcol1z11);
	$color2 = imagecolorallocate($image, $tcol1z - 1, $ttcol1z1 - 1, $tcol1z11 - 2);
	// imagefill($image, 0, 0, $color1);
	for ($i = 0; $i < $x; $i++) {
		for ($j = 0; $j < $y; $j++) {
			if (mt_rand(0, 20) == 20) {

				//  $coords = array(mt_rand(0, 10), mt_rand(0, 10), mt_rand(0, 10), mt_rand(0, 10), 5, 6);

				$y21 = mt_rand(5, 20);
				captcha_vector($image, $x - mt_rand(0, 10), mt_rand(0, 10), mt_rand(0, 180), 200, $bgcolor);
				//   imagesetpixel($image, $i, $j, $color2);
			}
		}
	}
	$x1 = mt_rand(15, 30);
	$y1 = mt_rand(15, 20);
	$tsize = rand(13, 16);
	imagettftext($image, $tsize, $roit, $x1, $y1, $black, $font, $text);

	$y21 = mt_rand(5, 20);
	captcha_vector($image, $x, $y21 / 2, 180, 200, $bgcolor);

	$y21 = mt_rand(5, 20);
	captcha_vector($image, $x, $y21 / 2, $col1z11, 200, $bgcolor);

	$y21 = mt_rand(5, 20);
	captcha_vector($image, $x / 3, $y21 / 3, $col1z11, 200, $bgcolor);

	//   imagestring($image, 5, 2, 2, $text, $black);

	$emboss = array( array(2, 0, 0), array(0, -1, 0), array(0, 0, -1));
	$embize = mt_rand(1, 4);
	// imageconvolution($image, $emboss, $embize, 255);
	//   imagefilter($image, IMG_FILTER_SMOOTH, 50);
	imagepng($image);
	imagecolordeallocate($image, $bgcolor);
	imagecolordeallocate($image, $black);

	imagedestroy($image);
}

function api_login($api_key = false) {

	if ($api_key == false and isset($_REQUEST['api_key']) and user_id() == 0) {
		$api_key = $_REQUEST['api_key'];
	}

	if ($api_key == false) {
		return false;
	} else {
		if (trim($api_key) == '') {
			return false;
		} else {
			$api_key = db_escape_string($api_key);
			if (user_id() > 0) {
				return true;
			} else {
				$data = array();
				$data['api_key'] = $api_key;
				$data['is_active'] = 'y';
				$data['limit'] = 1;

				$data = get_users($data);

				if ($data != false) {
					if (isset($data[0])) {
						$data = $data[0];

						if (isset($data['api_key']) and $data['api_key'] == $api_key) {
							return user_login($data);

						}

					}

				}
			}

		}
	}

}

function update_user_last_login_time() {

	$uid = user_id();
	if (intval($uid) > 0) {

		$data_to_save = array();
		$data_to_save['id'] = $uid;
		$data_to_save['last_login'] = date("Y-m-d H:i:s");
		$data_to_save['last_login_ip'] = USER_IP;

		$table = MW_DB_TABLE_USERS;
		$save = save_data($table, $data_to_save);

	}

}

api_expose('social_login_process');
function social_login_process() {
	set_exception_handler('social_login_exception_handler');

	$api = new \mw\auth\Social();
	$api -> process();

	// d($err);
	//$err= $api->is_error();

}

function social_login_exception_handler($exception) {

	if (isAjax()) {
		return array('error' => $exception -> getMessage());
	}

	$after_log = session_get('user_after_login');
	if ($after_log != false) {
		safe_redirect($after_log);
	} else {
		safe_redirect(site_url());
	}

}

api_expose('user_social_login');
function user_social_login($params) {
	set_exception_handler('social_login_exception_handler');
	$params2 = array();

	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $params2;
	}

	$return_after_login = false;
	if (isset($_SERVER["HTTP_REFERER"]) and stristr($_SERVER["HTTP_REFERER"], site_url())) {
		$return_after_login = $_SERVER["HTTP_REFERER"];
		session_set('user_after_login', $return_after_login);

	}

	$provider = false;
	if (isset($_REQUEST['provider'])) {
		$provider = $_REQUEST['provider'];
		$provider = trim(strip_tags($provider));
	}

	if ($provider != false and isset($params) and !empty($params)) {

		$api = new \mw\auth\Social();

		try {

			$authenticate = $api -> authenticate($provider);
			if (isarr($authenticate) and isset($authenticate['identifier'])) {

				$data = array();
				$data['oauth_provider'] = $provider;
				$data['oauth_uid'] = $authenticate['identifier'];

				$data_ex = get_users($data);
				if (empty($data_ex)) {
					$data_to_save = $data;
					$data_to_save['first_name'] = $authenticate['firstName'];
					$data_to_save['last_name'] = $authenticate['lastName'];
					$data_to_save['thumbnail'] = $authenticate['photoURL'];
					$data_to_save['email'] = $authenticate['emailVerified'];
					$data_to_save['user_information'] = $authenticate['description'];
					$data_to_save['is_active'] = 'y';
					$data_to_save['is_admin'] = 'n';

					$table = MW_DB_TABLE_USERS;
					mw_var('FORCE_SAVE', $table);

					$save = save_data($table, $data_to_save);
					cache_clean_group('users/global');
					if ($save > 0) {
						$data = array();
						$data['id'] = $save;
					}
					//d($save);
				}

				$data_ex = get_users($data);

				if (isset($data_ex[0])) {
					$data = $data_ex[0];
					$user_session['is_logged'] = 'yes';
					$user_session['user_id'] = $data['id'];

					if (!defined('USER_ID')) {
						define("USER_ID", $data['id']);
					}

					session_set('user_session', $user_session);
					$user_session = session_get('user_session');
					$return_after_login = session_get('user_after_login');
					update_user_last_login_time();
					if ($return_after_login != false) {
						safe_redirect($return_after_login);
						exit();
					}

					//d($user_session);
				}

			}

			//d($authenticate);

		} catch( Exception $e ) {
			die("<b>got an error!</b> " . $e -> getMessage());
		}

	}
}

api_expose('user_reset_password_from_link');
function user_reset_password_from_link($params) {
	if (!isset($params['captcha'])) {
		return array('error' => 'Please enter the captcha answer!');
	} else {
		$cap = session_get('captcha');
		if ($cap == false) {
			return array('error' => 'You must load a captcha first!');
		}
		if ($params['captcha'] != $cap) {
			return array('error' => 'Invalid captcha answer!');
		}
	}

	if (!isset($params['id']) or trim($params['id']) == '') {
		return array('error' => 'You must send id parameter');
	}

	if (!isset($params['password_reset_hash']) or trim($params['password_reset_hash']) == '') {
		return array('error' => 'You must send password_reset_hash parameter');
	}

	if (!isset($params['pass1']) or trim($params['pass1']) == '') {
		return array('error' => 'Enter new password!');
	}

	if (!isset($params['pass2']) or trim($params['pass2']) == '') {
		return array('error' => 'Enter repeat new password!');
	}

	if ($params['pass1'] != $params['pass2']) {
		return array('error' => 'Your passwords does not match!');
	}

	$data1 = array();
	$data1['id'] = intval($params['id']);
	$data1['password_reset_hash'] = db_escape_string($params['password_reset_hash']);
	$table = MW_DB_TABLE_USERS;

	$check = get_users("single=true&password_reset_hash=[not_null]&password_reset_hash=" . $data1['password_reset_hash'] . '&id=' . $data1['id']);
	if (!isarr($check)) {
		return array('error' => 'Invalid data or expired link!');
	} else {
		$data1['password'] = $params['pass1'];
		$data1['password_reset_hash'] = '';
	}

	mw_var('FORCE_SAVE', $table);

	$save = save_data($table, $data1);
	return array('success' => 'Your password have been changed!');

}

api_expose('user_send_forgot_password');
function user_send_forgot_password($params) {

	if (!isset($params['captcha'])) {
		return array('error' => 'Please enter the captcha answer!');
	} else {
		$cap = session_get('captcha');
		if ($cap == false) {
			return array('error' => 'You must load a captcha first!');
		}
		if ($params['captcha'] != $cap) {
			return array('error' => 'Invalid captcha answer!');
		}
	}
	if (!isset($params['username']) or trim($params['username']) == '') {
		return array('error' => 'Enter username or email!');
	}

	if (isset($params) and !empty($params)) {

		$user = isset($params['username']) ? $params['username'] : false;

		if (trim($user != '')) {
			$data1 = array();
			$data1['username'] = $user;
			//$data1['oauth_uid'] = '[null]';
			//$data1['oauth_provider'] = '[null]';
			$data = array();
			$data_res = false;
			if (trim($user != '')) {
				$data = get_users($data1);
			}

			if (isset($data[0])) {
				$data_res = $data[0];

			} else {
				$data1 = array();
				$data1['email'] = $user;
				//$data1['oauth_uid'] = '[null]';
				//$data1['oauth_provider'] = '[null]';
				$data = get_users($data1);
				if (isset($data[0])) {
					$data_res = $data[0];

				}

			}
			if (!isarr($data_res)) {
				return array('error' => 'Enter right username or email!');

			} else {
				$to = $data_res['email'];
				if (isset($to) and (filter_var($to, FILTER_VALIDATE_EMAIL))) {

					$subject = "Password reset!";
					$content = "Hello, <br> ";
					$content .= "You have requested a password reset link from IP address: " . USER_IP . "<br><br> ";

					//$content .= "on " . curent_url(1) . "<br><br> ";

					$security = array();
					$security['ip'] = USER_IP;
					$security['hash'] = encode_var($data_res);
					$function_cache_id = md5(serialize($security)) . uniqid() . rand();
					//cache_save($security, $function_cache_id, $cache_group = 'password_reset');
					if (isset($data_res['id'])) {
						$data_to_save = array();
						$data_to_save['id'] = $data_res['id'];
						$data_to_save['password_reset_hash'] = $function_cache_id;

						$table = MW_DB_TABLE_USERS;
						mw_var('FORCE_SAVE', $table);

						$save = save_data($table, $data_to_save);
					}
					$pass_reset_link = curent_url(1) . '?reset_password_link=' . $function_cache_id;
					$content .= "Click here to reset your password  <a href='{$pass_reset_link}'>" . $pass_reset_link . "</a><br><br> ";

					//d($data_res);
					mw_mail($to, $subject, $content, true, $no_cache = true);

					return array('success' => 'Your password reset link has been sent to ' . $to);
				} else {
					return array('error' => 'Error: the user doesn\'t have a valid email address!');
				}

			}

		}

	}

}

function user_login_set_failed_attempt() {

	save_log("title=Failed login&rel=login_failed&user_ip=" . USER_IP);

}

function user_login($params) {
	$params2 = array();

	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $params2;
	}

	if (isset($params) and !empty($params)) {

		$user = isset($params['username']) ? $params['username'] : false;
		$pass = isset($params['password']) ? $params['password'] : false;
		$email = isset($params['email']) ? $params['email'] : false;

		if (trim($user) == '' and trim($email) == '' and trim($pass) == '') {
			return array('error' => 'Please enter username and password!');

		}

		$check = get_log("count=1&created_on=[mt]1 min ago&rel=login_failed&user_ip=" . USER_IP);
		if ($check > 5) {
			return array('error' => 'There are ' . $check . ' failed login attempts from your ip in the last minute. Try again in 1 minute!');
		}

		//d($check);

		$api_key = isset($params['api_key']) ? $params['api_key'] : false;

		$data1 = array();
		$data1['username'] = $user;
		$data1['password'] = $pass;
		//$data1['debug'] = 1;
		$data1['search_in_fields'] = 'username,password,email';
		$data1['is_active'] = 'y';

		$data = array();

		if (trim($user != '') and trim($pass != '')) {
			$data = get_users($data1);
		}
		if (isset($data[0])) {
			$data = $data[0];
		} else {
			if (trim($email) != '') {
				$data = array();
				$data['email'] = $email;
				$data['password'] = $pass;
				$data['is_active'] = 'y';
				$data['search_in_fields'] = 'username,password,email';
				//$data['debug'] = 1;
				if (trim($user != '') and trim($email != '')) {
					$data = get_users($data);
				}
				$data = get_users($data);
				if (isset($data[0])) {
					$data = $data[0];
				} else {

					user_login_set_failed_attempt();
					return array('error' => 'Please enter right username and password!');

				}
			} else {
				//	return array('error' => 'Please enter username or email!');

			}

			// return false;
		}

		if (!isarr($data)) {
			if (trim($user) != '') {
				$data = array();
				$data['email'] = $user;
				$data['password'] = $pass;
				$data['is_active'] = 'y';
				//	 $data ['debug'] = 1;

				$data = get_users($data);

				if (isset($data[0])) {
					$data = $data[0];
				}
			}
		}
		if (!isarr($data)) {
			user_login_set_failed_attempt();

			$user_session = array();
			$user_session['is_logged'] = 'no';
			session_set('user_session', $user_session);

			return array('error' => 'Please enter the right username and password!');
			return false;
		} else {
			$user_session = array();
			$user_session['is_logged'] = 'yes';
			$user_session['user_id'] = $data['id'];

			if (!defined('USER_ID')) {
				define("USER_ID", $data['id']);
			}

			session_set('user_session', $user_session);
			$user_session = session_get('user_session');
			update_user_last_login_time();
			if (isset($data["is_admin"]) and $data["is_admin"] == 'y') {
				if (isset($params['where_to']) and $params['where_to'] == 'live_edit') {

					$p = get_page();
					if (!empty($p)) {
						$link = page_link($p['id']);
						$link = $link . '/editmode:y';
						safe_redirect($link);
					}
				}
			}

			$aj = isAjax();

			if ($aj == false and $api_key == false) {
				if (isset($_SERVER["HTTP_REFERER"])) {
					//	d($user_session);
					//exit();
					safe_redirect($_SERVER["HTTP_REFERER"]);
					exit();
				}
			} else if ($aj == true) {
				$user_session['success'] = "You are logged in!";
			}

			return $user_session;
		}
	}

	return false;
}

api_expose('logout');

function logout() {

	if (!defined('USER_ID')) {
		define("USER_ID", false);
	}

	// static $uid;
	$aj = isAjax();
	session_end();

	if (isset($_COOKIE['editmode'])) {
		setcookie('editmode');
	}

	if ($aj == false) {
		if (isset($_SERVER["HTTP_REFERER"])) {
			safe_redirect($_SERVER["HTTP_REFERER"]);
		}
	}
}

function user_id() {

	// static $uid;
	if (defined('USER_ID')) {
		// print USER_ID;
		return USER_ID;
	} else {

		$user_session = session_get('user_session');
		if ($user_session == FALSE) {
			return false;
		}
		$res = false;
		if (isset($user_session['user_id'])) {
			$res = $user_session['user_id'];
		}

		if ($res != false) {
			// $res = $sess->get ( 'user_id' );
			define("USER_ID", $res);
		}
		return $res;
	}
}

function has_access($function_name) {

	$is_a = is_admin();

	if ($is_a == true) {
		return true;
	} else {
		return false;
	}
}

function admin_access() {
	if (is_admin() == false) {
		exit('You must be logged as admin');
	}

}

function only_admin_access() {
	if (is_admin() == false) {
		exit('You must be logged as admin');
	}

}

function is_admin() {

	static $is = 0;
	if (MW_IS_INSTALLED == false) {
		return true;
	}
	if ($is != 0 or defined('USER_IS_ADMIN')) {
		// var_dump( $is);
		return $is;
	} else {
		$usr = user_id();
		if ($usr == false) {
			return false;
		}
		$usr = get_user($usr);

		if (isset($usr['is_admin']) and $usr['is_admin'] == 'y') {
			define("USER_IS_ADMIN", true);
			define("IS_ADMIN", true);
		} else {
			define("USER_IS_ADMIN", false);
			define("IS_ADMIN", false);
		}
		$is = USER_IS_ADMIN;
		// var_dump( $is);
		// var_dump( $is);
		// var_dump( USER_IS_ADMIN.USER_IS_ADMIN.USER_IS_ADMIN);
		return USER_IS_ADMIN;
	}
}

/**
 * user_name
 *
 * gets the user's FULL name
 *
 * @access public
 * @category users
 * @author Microweber
 * @link http://microweber.com
 * @param $user_id -
 *        	the is of the user. If false it will use the curent user (you)
 * @param string $mode
 *        	= 'full' //prints full name (first +last)
 *
 *        	$mode = 'first' //prints first name
 *        	$mode = 'last' //prints last name
 *        	$mode = 'username' //prints username
 *
 */
function user_name($user_id = false, $mode = 'full') {
	if ($mode != 'username') {
		if ($user_id == user_id()) {
			// return 'You';
		}
	}
	if ($user_id == false) {
		$user_id = user_id();
	}

	$name = nice_user_name($user_id, $mode);
	return $name;
}

/**
 * get_users
 *
 * get_users
 *
 * @access public
 * @category users
 * @author Microweber
 * @link http://microweber.com
 * @param $params =
 *        	array();
 * @return array array of users;
 */
function get_users($params) {
	$params = parse_params($params);

	$table = MW_TABLE_PREFIX . 'users';

	$data = string_clean($params);
	$orig_data = $data;

	if (isset($data['ids']) and is_array($data['ids'])) {
		if (!empty($data['ids'])) {
			$ids = $data['ids'];
		}
	}
	if (!isset($params['search_in_fields'])) {
		$data['search_in_fields'] = array('first_name', 'last_name', 'username', 'email');
		// $data ['debug'] = 1;
	}

	$cache_group = 'users/global';
	if (isset($data['id']) and intval($data['id']) != 0) {
		$cache_group = 'users/' . $data['id'];
	} else {

	}
	$cache_group = 'users/global';
	if (isset($limit) and $limit != false) {
		$data['limit'] = $limit;
	}

	if (isset($count_only) and $count_only != false) {
		$data['get_count'] = $count_only;
	}

	if (isset($data['only_those_fields']) and $data['only_those_fields']) {
		$only_those_fields = $data['only_those_fields'];
	}

	if (isset($data['count']) and $data['count']) {
		$count_only = $data['count'];
	}

	// $data ['no_cache'] = 1;

	if (isset($data['username']) and $data['username'] == null) {
		unset($data['username']);
	}
	if (isset($data['username']) and $data['username'] == '') {
		//return false;
	}

	// $function_cache_id = __FUNCTION__ . crc32($function_cache_id);
	$data['table'] = $table;
	//  $data ['cache_group'] = $cache_group;

	$get = get($data);

	//$get = db_get($table, $criteria = $data, $cache_group);
	// $get = db_get($table, $criteria = $data, $cache_group);
	// var_dump($get, $function_cache_id, $cache_group);
	//  cache_save($get, $function_cache_id, $cache_group);

	return $get;
}

/**
 * get_user
 *
 * get_user get the user info from the DB
 *
 * @access public
 * @category users
 * @author Microweber
 * @link http://microweber.com
 * @param $id =
 *        	the id of the user;
 * @return array
 */
function get_user($id = false) {
	if ($id == false) {
		$id = user_id();
	}

	if ($id == 0) {
		return false;
	}

	$res = get_user_by_id($id);

	if (empty($res)) {

		$res = get_user_by_username($id);
	}

	return $res;
}

/**
 * Generic function to get the user by id.
 * Uses the getUsers function to get the data
 *
 * @param
 *        	int id
 * @return array
 *
 */
function get_user_by_id($id) {
	$id = intval($id);
	if ($id == 0) {
		return false;
	}

	$data = array();
	$data['id'] = $id;
	$data['limit'] = 1;
	$data = get_users($data);
	if (isset($data[0])) {
		$data = $data[0];
	}
	return $data;
}

function get_user_by_username($username) {
	$data = array();
	$data['username'] = $username;
	$data['limit'] = 1;
	$data = get_users($data);
	if (isset($data[0])) {
		$data = $data[0];
	}
	return $data;
}

/**
 * Function to get user printable name by given ID
 *
 * @param
 *        	$id
 * @param
 *        	$mode
 * @return string
 * @usage Delete relation:
 *          $this->users_model->getPrintableName(10, 'full');
 *
 */
function nice_user_name($id, $mode = 'full') {
	$user = get_user_by_id($id);
	$user_data = $user;
	if (empty($user)) {
		return false;
	}

	switch ($mode) {
		case 'first' :
		case 'fist' :
			// because of a common typo :)
			$user_data['first_name'] ? $name = $user_data['first_name'] : $name = $user_data['username'];
			$name = ucwords($name);
			return $name;
			break;

		case 'last' :
			$user_data['last_name'] ? $name = $user_data['last_name'] : $name = $user_data['last_name'];
			$name = ucwords($name);
			return $name;
			break;

		case 'username' :
			$name = $user_data['username'];
			return $name;
			break;

		case 'full' :
		default :
			$name = $user_data['first_name'] . ' ' . $user_data['last_name'];

			if (trim($name) == '') {
				$name = $user_data['username'];
			}

			$name = ucwords($name);
			return $name;

			break;
	}
	exit();
}

/**
 * get_new_users
 *
 * get_new_users
 *
 * @access public
 * @category users
 * @author Microweber
 * @link http://microweber.com
 * @param $period =
 *        	7 days;
 * @return array $ids - array of user ids;
 */
function get_new_users($period = '7 days', $limit = 20) {

	// $CI = get_instance ();
	get_instance() -> load -> model('Users_model', 'users_model');
	$data = array();
	$data['created_on'] = '[mt]-' . $period;
	$data['fields'] = array('id');
	$limit = array('0', $limit);
	// $data['debug']= true;
	// $data['no_cache']= true;
	$data =                                                                                  get_instance() -> users_model -> getUsers($data, $limit, $count_only = false);
	$res = array();
	if (!empty($data)) {
		foreach ($data as $item) {
			$res[] = $item['id'];
		}
	}
	return $res;
}

function user_id_from_url() {
	if (url_param('username')) {
		$usr = url_param('username');
		// $CI = get_instance ();
		get_instance() -> load -> model('Users_model', 'users_model');
		$res =                                                                                  get_instance() -> users_model -> getIdByUsername($username = $usr);
		return $res;
	}

	if (url_param('user_id')) {
		$usr = url_param('user_id');
		return $usr;
	}
	return user_id();
}

/**
 * user_thumbnail
 *
 * get the user_thumbnail of the user
 *
 * @access public
 * @category general
 * @author Microweber
 * @link http://microweber.com
 * @param $params =
 *        	array();
 *        	$params['id'] = 15; //the user id
 *        	$params['size'] = 200; //the thumbnail size
 * @return string - The thumbnail link.
 * @usage Use
 *          user_thumbnail
 *
 *          get the user_thumbnail of the user
 *
 * @access public
 * @category general
 * @author Microweber
 * @link http://microweber.com
 * @param $params =
 *        	array();
 *        	$params['id'] = 15; //the user id
 *        	$params['size'] = 200; //the thumbnail size
 * @return string - The thumbnail link.
 * @usage Use print post_thumbnail($post['id']);
 */
function user_picture($params) {
	return user_thumbnail($params);
}

function user_thumbnail($params) {
	$params2 = array();

	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $params2;
	}

	//
	// $CI = get_instance ();

	if (!$params['size']) {
		$params['size'] = 200;
	}

	// $pic = get_picture($params ['id'], $for = 'user');
	// $media = get_instance()->core_model->mediaGetThumbnailForMediaId (
	// $pic['id'],
	// $params ['size'], $size_height );
	// p($media);

	$thumb =                                                                                  get_instance() -> core_model -> mediaGetThumbnailForItem($rel = 'users', $rel_id = $params['id'], $params['size'], 'DESC');

	return $thumb;
}

function users_count() {
	$options = array();
	$options['get_count'] = true;
	// $options ['debug'] = true;
	$options['count'] = true;
	// $options ['no_cache'] = true;
	$options['cache_group'] = 'users/global/';

	$data = get_users($options);

	return $data;
}

function cf_get_user($user_id, $field_name) {
	$fields = get_custom_fields_for_user($user_id);
	if (empty($fields)) {
		return false;
	}

	foreach ($fields as $field) {
		if (trim(strtolower($field_name)) == trim(strtolower($field['custom_field_name']))) {

			if ($field['custom_field_value']) {
				return $field['custom_field_value'];
			} else {

				if ($field['custom_field_values']) {
					return $field['custom_field_values'];
				}
			}

			// p ( $field );
		}
	}
}

function get_custom_fields_for_user($user_id, $field_name = false) {
	// p($content_id);
	$more = false;
	$more =                                                                                  get_instance() -> core_model -> getCustomFields('users', $user_id, true, $field_name);
	return $more;
}

function friends_count($user_id = false) {

	// $CI = get_instance ();
	if ($user_id == false) {
		$user_id = user_id();
	}

	$query_options = array();

	$query_options['get_count'] = true;
	$query_options['debug'] = false;
	$query_options['group_by'] = false;
	get_instance() -> load -> model('Users_model', 'users_model');
	$users =                                                                                  get_instance() -> users_model -> realtionsGetFollowedIdsForUser($aUserId = $user_id, $special = false, $query_options);
	return intval($users);
}

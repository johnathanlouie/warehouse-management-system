<?php
auth::get_instance();

class auth
{
	public const PREV_NLOG = 0;
	public const PREV_LOG = 1;
	public const INIT_ATTEMPT = 2;
	public const SUCCESS = 3;
	public const FAIL = 4;
	public const LOGOUT = 5;

	private static $instance;
	private static $auth_status;

	private function __construct()
	{
		session_start();
		$auth_status = is_authenticated() ? PREV_LOG : PREV_NLOG;
	}

	public static function get_instance()
	{
		if ($instance === null)
		{
			$instance = new auth();
		}
		return $instance;
	}

	public static function get_auth_status()
	{
		return $auth_status;
	}

	public static function is_authenticated()
	{
		return
			isset($_SESSION['auth_key']) &&
			isset($_COOKIE['auth_key']) &&
			$_COOKIE['auth_key'] === $_SESSION['auth_key'];
	}

	public static function is_attempt()
	{
		return
			isset($_POST['username']) &&
			isset($_POST['password']) &&
			strlen($_POST['username']) &&
			strlen($_POST['password']);
	}

	private static function delete_session_cookie()
	{
		$params = session_get_cookie_params();
		setcookie
		(
			session_name(),
			'',
			time() - 42000,
			$params["path"],
			$params["domain"],
			$params["secure"],
			$params["httponly"]
		);
	}

	public static function logout()
	{
		delete_session_cookie();
		session_destroy();
	}

	private static function get_user_row($username)
	{
		$db = new mysqlp();
		$db->prepare('SELECT * FROM users WHERE username = ?');
		$db->stmt->bind_param('s', $username);
		$db->execute();
		$rs = $db->get_results();
		$db->close_stmt();
		$db->close();
		return isset($rs[0]) : $rs[0] ? null;
	}

	private static function user_exists($row)
	{
		return $row !== null;
	}

	private static function attach_user($row)
	{
		$_SESSION['username'] = $row['username'];
		$_SESSION['name_first'] = $row['name_first'];
		$_SESSION['name_last'] = $row['name_last'];
		$_SESSION['name_full'] = $_SESSION['name_first'] . ' ' . $_SESSION['name_last'];
		$_SESSION['role'] = $row['role'];
	}

	private static function new_auth_key()
	{
		return hash('sha256', $_SESSION["username"] . time());
	}

	public static function login()
	{
		$row = get_user_row($_POST['username']);
		if (user_exists($row) && $row['password'] === $_POST['password'])
		{
			attach_user($row);
			$auth_key = new_auth_key();
			$_SESSION['auth_key'] = $auth_key;
			setcookie('auth_key', $auth_key);
			return true;
		}
		return false;
	}
}

class ui
{
	public static function req_login_alert()
	{
		include 'req_login_alert.html';
	}
	public static function unauthorized_alert()
	{
		include 'unauthorized_alert.html';
	}

	public static function html_head()
	{
		include 'head_contents.html';
	}

	public static function navbar()
	{
		include 'navbar.php';
	}
}

function insert_schema($db, $schema_name, $schema_description, $schema_format)
{
	$db->prepare('INSERT INTO sku_schemata (schema_name, schema_description, schema_format) VALUES (?, ?, ?);');
	$db->stmt->bind_param('sss', $schema_name, $schema_description, $schema_format);
	$db->execute();
	$db->close_stmt();
}

function get_id($db)
{
	$db->prepare('SELECT LAST_INSERT_ID();');
	$db->execute();
	$rs = $db->get_results();
	$db->close_stmt();
	return $rs;
}

function get_fields()
{
	$fields = array();
	for ($i = 1; $i <= $_POST['field-count']; $i++)
	{
		$fields[$i] = array();
		$fields[$i]['digits'] = $_POST["field-digit-$i"];
		$fields[$i]['name'] = $_POST["field-name-$i"];
	}
	return $fields;
}

function insert_fields($db, $schema_id, $sku_field_pos, $sku_field_digit, $sku_field_name)
{
	$db->prepare('INSERT INTO sku_fields (schema_id, sku_field_pos, sku_field_digit, sku_field_name) VALUES (?, ?, ?, ?);');
	$db->stmt->bind_param('iiis', $schema_id, $sku_field_pos, $sku_field_digit, $sku_field_name);
	$db->execute();
	$db->close_stmt();
}

function handle_schema_addition()
{
	$db = new mysqlp();
	insert_schema($_POST['name'], $_POST['description'], $_POST['field-count']);
	$schema_id = get_id()[0]['LAST_INSERT_ID()'];
	foreach (get_fields() as $i => $j)
	{
		insert_fields($schema_id, $i, $j['digits'], $j['name']);
	}
	$db->close();
}

class mysqlp
{
	private $mysqli;
	public $stmt;

	public function __construct()
	{
		$cfg_file = 'database.cfg';
		$ini_array = parse_ini_file($cfg_file);
		$this->mysqli = new mysqli
		(
			$ini_array['mysql_server'],
			$ini_array['mysql_user'],
			$ini_array['mysql_pw'],
			$ini_array['mysql_db_name']
		);
		$this->mysqli->set_charset('utf8mb4');
	}

	public function prepare($sql)
	{
		$this->stmt = $this->mysqli->prepare($sql);
	}

	public function execute()
	{
		$this->stmt->execute();
	}

	public function close_stmt()
	{
		$this->stmt->close();
	}

	public function get_results()
	{
		$result = $this->stmt->get_result();
		$result_set = false;
		if ($result !== false)
		{
			$result_set = $result->fetch_all(MYSQLI_ASSOC);
			$result->free();
		}
		return $result_set;
	}

	public function query($sql)
	{
		$this->prepare($sql);
		$this->execute();
		$rs = $this->get_results();
		$this->close_stmt();
		return $rs;
	}

	public function close()
	{
		$this->mysqli->close();
	}
}

function handle_error($err_free, $quit, $cl_msg, $svr_msg)
{
	if ($err_free === false)
	{
		if ($cl_msg !== null) {send_json_error($cl_msg);}
		if ($svr_msg !== null) {append_error_log($svr_msg);}
		if ($quit) {exit;}
	}
}

function append_error_log($msg)
{
	$timestamp = date('Y-m-d D H:i:s');
	error_log("$timestamp $msg\n", 3, 'error.log');
}

function send_json_error($msg)
{
	$object = ['error' => $msg];
	echo json_encode($object);
}
?>
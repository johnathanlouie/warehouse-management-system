<?php
require 'phplibrary.php';

if ($login_attempt && !$authenticated)
{
	$sql = 'SELECT * FROM users WHERE username = ?';
	$db = new mysqlp();
	$db->prepare($sql);
	$db->stmt->bind_param('s', $_POST['username']);
	$db->execute();
	$rs = $db->get_results();
	$db->close_stmt();
	$db->close();
	if (isset($rs[0]))
	{
		$user_exists = true;
		$user = $rs[0];
		if ($user['password'] === $_POST["password"])
		{
			$correct_password = true;
			session_regenerate_id(true);
			$_SESSION['username'] = $user["username"];
			$_SESSION['name_first'] = $user["name_first"];
			$_SESSION['name_last'] = $user["name_last"];
			$_SESSION['name_full'] = $user["name_first"] . ' ' . $user["name_last"];
			$_SESSION['role'] = $user["role"];
			$session_key = hash('sha256', $user["username"] . time());
			$_SESSION['session_key'] = $session_key;
			setcookie('session_key', $session_key);
			$authenticated = true;
			$just_logged_in = true;
		}
	}
}
			
require 'template_top.html';
include 'navbar.php';
?>

<div class="container">
<h1>Login</h1>

<?php if ($authenticated && !$just_logged_in): ?>
<?php ui::alert_login_already(); ?>
<?php endif; ?>

<?php if ($just_logged_in): ?>
<?php ui::alert_login_success(); ?>
<?php endif; ?>

<?php if ($login_attempt && $user_exists && !$correct_password): ?>
<?php ui::alert_wrong_pw(); ?>
<?php endif; ?>

<?php if($login_attempt && !$user_exists): ?>
<?php ui::alert_wrong_username(); ?>
<?php endif; ?>

<?php if ($authenticated):?>
<a href="menu.php" class="btn btn-info btn-block" role="button">Menu</a>
<a href="logout.php" class="btn btn-info btn-block" role="button">Logout</a>

<?php else: ?>
<form id="login-form" method="post" role="form">
<div class="form-group">
<label for="username">Username:</label>
<input type="text" class="form-control" id="username" name="username" autofocus="autofocus" placeholder="Username" required="required" pattern="[a-z]+[0-9]*">
</div>
<div class="form-group">
<label for="password">Password:</label>
<input type="password" class="form-control" id="password" name="password" placeholder="password" required="required" pattern="[A-Za-z0-9]+">
</div>
<div class="checkbox">
<label for="remember"><input type="checkbox" id="remember" name="remember">Remember me</label>
</div>
<button type="submit" class="btn btn-default">Submit</button>
</form>

<?php endif; ?>

</div>

<?php
require 'template_bot.html';
?>
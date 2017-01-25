<?php
require 'session.php';
require 'template_top.html';
include 'navbar.php';
?>

<div class="container">
<h1>Logout</h1>

<?php
if ($authenticated):
	logout();
?>
<div class="alert alert-success">
<p>You have successfully logged out.</p>
</div>

<?php else: ?>
<div class="alert alert-info">
<p>You were already logged out!</p>
</div>

<?php endif; ?>

<a href="index.php" class="btn btn-info btn-block" role="button">Home</a>
<a href="login.php" class="btn btn-info btn-block" role="button">Login</a>
</div>

<?php
require 'template_bot.html';
?>
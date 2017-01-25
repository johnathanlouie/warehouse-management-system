<?php require 'phplibrary.php'; ?>
<!doctype html>
<html lang="en">
<head>
<?php ui::html_head(); ?>
</head>
<body style="background-image: url(generic_warehouse.jpg); background-size: cover;">

<div class="container">
<div class="jumbotron">
<h1>Prometheus</h1>
<p>Warehouse/Inventory Management System</p>
<!--<img src="generic_warehouse.jpg" class="img-rounded img-responsive">-->
</div>

<?php if (auth::is_authenticated()):?>
<div class="well"><p>Hello <? echo $_SESSION['username']; ?>!</p></div>
<a href="menu.php" class="btn btn-info btn-block" role="button">Menu</a>
<a href="logout.php" class="btn btn-info btn-block" role="button">Logout</a>

<?php else: ?>
<a href="login.php" class="btn btn-info btn-block" role="button">Login</a>

<?php endif; ?>

</div>

</body>
</html>
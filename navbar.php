<nav class="navbar navbar-inverse">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="index.php">Prometheus</a>
</div>
<div class="collapse navbar-collapse" id="myNavbar">
<ul class="nav navbar-nav">
<?php if ($authenticated): ?>
<li><a href="menu.php">Menu</a></li>
<?php endif; ?>
</ul>
<ul class="nav navbar-nav navbar-right">
<?php if ($authenticated): ?>
<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
<?php else: ?>
<li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
<?php endif; ?>
</ul>
</div>
</div>
</nav>
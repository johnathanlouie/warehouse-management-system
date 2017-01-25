<?php
require 'session.php';
require 'template_top.html';
include 'navbar.php';
?>

<div class="container">
<h1>Menu</h1>

<?php if ($authenticated):?>
<ul class="nav nav-tabs">
<li class="active"><a data-toggle="pill" href="#menu0">Account</a></li>
<li><a data-toggle="pill" href="#menu1">Users</a></li>
<li><a data-toggle="pill" href="#menu2">SKU Schemata</a></li>
<li><a data-toggle="pill" href="#menu3">Units</a></li>
<li><a data-toggle="pill" href="#menu4">Bundles</a></li>
<li><a data-toggle="pill" href="#menu5">Sales Order</a></li>
</ul>

<div class="tab-content">
<div id="menu0" class="tab-pane fade in active">
<h3>Account</h3>
<div>
<a>Change Password</a>
</div>
</div>
<div id="menu1" class="tab-pane fade">
<h3>Users</h3>
<div>
<a>Add User</a><br>
<a>Modify Roles</a><br>
<a>Display Users</a><br>
<a>Assign Roles</a>
</div>
</div>
<div id="menu2" class="tab-pane fade">
<h3>Schemata</h3>
<div>
<a href="add_schema.php">Add Schema</a><br>
<a href="view_schemata.php">Display Schemata</a>
</div>
<h3>Dictionaries</h3>
<div>
<a>Add Entry</a><br>
<a>View Entries</a>
</div>
</div>
<div id="menu3" class="tab-pane fade">
<h3>Menu 3</h3>
<p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
</div>
</div>

<?php else: ?>
<div class="alert alert-danger">
<p><strong>Error!<strong> You need to log in to use Prometheus!</p>
</div>

<?php endif; ?>

</div>

<?php
require 'template_bot.html';
?>
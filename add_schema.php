<?php
require 'phplibrary.php';
require 'session.php';
require 'template_top.html';
require 'database.php';
include 'navbar.php';
$step = isset($_POST['step']) ? $_POST['step'] : false;
?>

<div class="container">

<?php if ($authenticated && $step == 2): ?>
<?php handle_schema_addition(); ?>

<div class="alert alert-success">
<p><strong>Success!</strong> A new schema was created.</p>
</div>
<?php endif; ?>

<h1>Add Schema</h1>

<?php if ($authenticated && !$step): ?>
<form role="form" method="post">
<div class="form-group">
<label for="name">Name:</label>
<input type="text" class="form-control" id="name" name="name" required="required" pattern="[a-zA-Z0-9]+( [a-zA-Z0-9]+)*">
</div>
<div class="form-group">
<label for="description">Description:</label>
<textarea class="form-control" id="description" name="description" rows="5"></textarea>
</div>
<div class="form-group">
<label for="field-count">Number of Fields:</label>
<input type="number" min="1" class="form-control" id="field-count" name="field-count" required="required">
</div>
<input type="number" class="hidden" name="step" value="1">
<button type="submit" class="btn btn-default">Next</button>
</form>

<?php elseif ($authenticated && $step == 1): ?>
<form role="form" method="post">
<p>How many digits are in each field? What does each field represent?</p>
<div class="container-fluid">
<?php for ($i = 1; $i <= $_POST['field-count']; $i++): ?>
<div class="row no-gutter">
<div class="col-xs-1"><?php echo $i; ?></div>
<div class="col-xs-3 col-sm-1"><input class="form-control" name="field-digit-<?php echo $i; ?>" type="number" min="1" required="required"></div>
<div class="col-xs-8 col-sm-10"><input class="form-control" name="field-name-<?php echo $i; ?>" type="text" pattern="[a-zA-Z0-9]+( [a-zA-Z0-9]+)*" min="1" required="required" placeholder="Field Name"></div>
</div>
<?php endfor; ?>
</div>
<input type="number" class="hidden" name="step" value="2">
<input type="text" class="hidden" name="name" value="<?php echo $_POST['name']; ?>">
<textarea class="hidden" name="description"><?php echo $_POST['description']; ?></textarea>
<input type="number" class="hidden" name="field-count" value="<?php echo $_POST['field-count']; ?>">
<button type="submit" class="btn btn-default">Save</button>
</form>

<?php elseif ($authenticated && $step == 2): ?>
<a href="add_schema.php" class="btn btn-info btn-block" role="button">Add Another Schema</a>

<?php elseif (!$authenticated): ?>
<div class="alert alert-danger">
<p><strong>Error!<strong> You need to log in to use Prometheus!</p>
</div>

<?php endif; ?>

</div>

<?php
require 'template_bot.html';
?>
<?php
require 'session.php';
require 'template_top.html';
require 'database.php';
include 'navbar.php';
?>

<div class="container">
<h1>View Schemata</h1>

<?php
$db = new Mysql();

$schemata = $db->query('SELECT * FROM sku_schemata');
$db->close();
?>
<table class="table">
<thead>
<tr>
<th>Name</th>
<th>Description</th>
<th>Format</th>
<th>Dictionary</th>
</tr>
</thead>
<tbody>
<?php
function get_xs($num)
{
	$string = '';
	for ($i = 0; $i < $num; $i++)
	{
		$string .= 'X';
	}
	return $string;
}

function get_format($input)
{
	$string = '';
	$first = array_pop($input);
	$string .= get_xs($first['sku_field_digit']);
	foreach ($input as $i => $field)
	{
		$string .= '-' . get_xs($field['sku_field_digit']);
	}
	return $string;
}

function get_format2($input)
{
	$string = '';
	$first = array_pop($input);
	$string .= $first['sku_field_name'];
	foreach ($input as $i => $field)
	{
		$string .= '-' . $field['sku_field_name'];
	}
	return $string;
}

$db = new Mysql();
$db->prepare('SELECT * FROM sku_fields WHERE schema_id = ? ORDER BY sku_field_pos ASC');
foreach ($schemata as $i => $schema):
$db->stmt->bind_param('i', $schema['schema_id']);
$db->execute();
?>
<tr>
<td><?= $schema['schema_name']; ?></td>
<td><?= $schema['schema_description']; ?></td>
<td><?= get_format($schema_fields); ?><br><?= get_format2($schema_fields); ?></td>
<td><a href="add_field_dictionary.php?id=<?= $schema['schema_id']; ?>">Edit</a></td>
</tr>
<?php endforeach; ?>
<?php
$db->close_stmt();
$db->close();
?>
</tbody>
</table>
</div>

<?php
require 'template_bot.html';
?>
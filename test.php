<?php
include 'database.php';

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
	return $rs[0]['LAST_INSERT_ID()'];
}

function get_fields()
{
	$fields = array();
	for ($i = 1; $i <= $_POST['field-count']; $i++)
	{
		$fields[$i] = array();
		$fields[$i]['length'] = $_POST["field-digit-$i"];
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

function get_x($num)
{
	$x = '';
	for ($i = 0; $i < $num; $i++)
	{
		$x .= 'X';
	}
	return $x;
}

function get_format($fields)
{
	$output = '';
	$output = get_x(array_unshift($fields)['length']);
	for ($fields as $i => $field)
	{
		$output .= '-' . get_x($field['length']);
	}
	return $output;
}

function asdf()
{
	$db = new mysqlp();
	$fields = get_fields();
	$format = get_format($fields);
	insert_schema($db, $_POST['name'], $_POST['description'], $format);
	$schema_id = get_id();
	foreach ($fields as $i => $field)
	{
		insert_fields($db, $schema_id, $i, $field['length'], $field['name']);
	}
	$db->close();
}
?>
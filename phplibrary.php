<?php

require 'config.php';

auth::get_instance();

class ui {

    public static function req_login_alert() {
        include 'req_login_alert.html';
    }

    public static function unauthorized_alert() {
        include 'unauthorized_alert.html';
    }

    public static function html_head() {
        include 'head_contents.html';
    }

    public static function navbar() {
        include 'navbar.php';
    }

}

function insert_schema($db, $schema_name, $schema_description, $schema_format) {
    $db->prepare('INSERT INTO sku_schemata (schema_name, schema_description, schema_format) VALUES (?, ?, ?);');
    $db->stmt->bind_param('sss', $schema_name, $schema_description, $schema_format);
    $db->execute();
    $db->close_stmt();
}

function get_id($db) {
    $db->prepare('SELECT LAST_INSERT_ID();');
    $db->execute();
    $rs = $db->get_results();
    $db->close_stmt();
    return $rs;
}

function get_fields() {
    $fields = array();
    for ($i = 1; $i <= $_POST['field-count']; $i++) {
        $fields[$i] = array();
        $fields[$i]['digits'] = $_POST["field-digit-$i"];
        $fields[$i]['name'] = $_POST["field-name-$i"];
    }
    return $fields;
}

function insert_fields($db, $schema_id, $sku_field_pos, $sku_field_digit, $sku_field_name) {
    $db->prepare('INSERT INTO sku_fields (schema_id, sku_field_pos, sku_field_digit, sku_field_name) VALUES (?, ?, ?, ?);');
    $db->stmt->bind_param('iiis', $schema_id, $sku_field_pos, $sku_field_digit, $sku_field_name);
    $db->execute();
    $db->close_stmt();
}

function handle_schema_addition() {
    $db = new Mysql();
    insert_schema($_POST['name'], $_POST['description'], $_POST['field-count']);
    $schema_id = get_id()[0]['LAST_INSERT_ID()'];
    foreach (get_fields() as $i => $j) {
        insert_fields($schema_id, $i, $j['digits'], $j['name']);
    }
    $db->close();
}

function handle_error($err_free, $quit, $cl_msg, $svr_msg) {
    if ($err_free === false) {
        if ($cl_msg !== null) {
            send_json_error($cl_msg);
        }
        if ($svr_msg !== null) {
            append_error_log($svr_msg);
        }
        if ($quit) {
            exit;
        }
    }
}

function append_error_log($msg) {
    $timestamp = date('Y-m-d D H:i:s');
    error_log("$timestamp $msg\n", 3, 'error.log');
}

function send_json_error($msg) {
    $object = ['error' => $msg];
    echo json_encode($object);
}

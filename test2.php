<?php
session_start();
echo @$_SESSION['username'] . "\n";
echo session_id() . "\n";
echo session_name() . "\n";
echo session_status();
print_r($_COOKIE);
?>
<?php require 'phplibrary.php'; ?>
<!doctype html>
<html lang="en">
<head>
<?php ui::navbar(); ?>
</head>
<body>

<?php if (!$authenticated) {ui::req_login_alert();} ?>
<?php if ($authenticated && !$authorized) {ui::unauthorized_alert();} ?>
<?php if ($authenticated && $authorized): ?>
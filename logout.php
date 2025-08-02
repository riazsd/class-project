<?php
require_once 'classes/Auth.php';

Auth::logout();
header('Location: login.php');
exit();
?>

<?php
session_start();
// remove all session variables
session_unset();
// destroy the session
session_destroy();
?>
<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php

flash("You have successfully been logged out!<br>");

die(header("Location: login.php"));


//echo "<pre>" . var_export($_SESSION, true) . "</pre>";
?>

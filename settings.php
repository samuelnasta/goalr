<?php
session_start();
include_once('functions.php');

echo 'settings is loaded';

if (isset($db) && $db) $db = NULL;
?>
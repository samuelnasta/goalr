<?php
session_start();
include_once('functions.php');

echo 'cards is loaded';

if (isset($db) && $db) $db = NULL;
?>
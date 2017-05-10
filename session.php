<?php
ini_set('session.referer_check', 'www.yourdomain.edu/ltr/'); 
session_start();

$old_sessionid = session_id();
print_r($_COOKIE);

echo "Old Session: $old_sessionid<br />";
/*session_regenerate_id();

$new_sessionid = session_id();


echo "New Session: $new_sessionid<br />";
*/
print_r($_SESSION);


if (!isset($_SESSION['visits'])) { $_SESSION['visits'] = 1; }
else { $_SESSION['visits']++; }
echo $_SESSION['visits'];
?>

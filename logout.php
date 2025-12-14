<?php
session_start();

//destroy ang session
session_unset();
session_destroy();

//iprevent mag back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

header("Location: index.php");
exit();
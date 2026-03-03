<?php
session_start();
session_unset();
session_destroy();
sleep(2);
header("location:index.php");
exit();
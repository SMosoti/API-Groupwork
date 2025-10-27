<?php
session_start();
<<<<<<< HEAD
session_unset();
session_destroy();
header('Location: login.php');
exit;
=======
session_destroy();
header("Location: login.php");
exit;
?>
>>>>>>> 60a6e252ca144b505ab40824e18cabeda9a7f0fe

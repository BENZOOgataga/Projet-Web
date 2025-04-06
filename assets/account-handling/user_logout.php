<?php
// créer une session et la détruit
session_start();
session_destroy();
header("Location: login.php");
exit;
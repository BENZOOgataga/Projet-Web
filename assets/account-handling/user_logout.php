<?php
// créer une session et la détruire
session_start();
session_destroy();
header("Location: login.php");
exit;
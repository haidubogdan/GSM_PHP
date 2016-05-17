<?php
session_start();
ob_start();
require_once ("app/controllers/base.php");
require_once ("app/controllers/home.php");
require_once ("app/models/GsmLanguage.php");
require_once ("app/gsm/SL60_gsm.php");
require_once ("app/gsm3Dscript/SL60_3d_script_gsm.php");
?>
<!DOCTYPE html>
<?php
require "app/controllers/controller.php";
new controller\Controller();



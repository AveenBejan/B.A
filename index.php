<?php require_once('Connections/accounts.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_accounts, $accounts);
$query_Recordset1 = "SELECT * FROM `user`";
$Recordset1 = mysql_query($query_Recordset1, $accounts) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "level";
  $MM_redirectLoginSuccess = "indexx.php";
  $MM_redirectLoginFailed = "admin/register/fail.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_accounts, $accounts);
  	
  $LoginRS__query=sprintf("SELECT username, password, level FROM `user` WHERE username=%s AND password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $accounts) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'level');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
  <link href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed" rel="stylesheet"><link rel="stylesheet" type="text/css" href="css/bootstraplogin.css">

  <link rel="stylesheet" type="text/css" href="login-form-design-in-bootstrap-4-modal-usign-html-and-css/custom.css">
 <script src="js/loginform/jquery3.2.1jquery.min.js"></script>
 <script src="js/loginform/umdpopper.min.js"></script>
<script src="js/loginform/bootstrap.min.js"></script>

</head>
<body>


  <div class="modal-dialog">
    <div class="col-lg-8 col-sm-8 col-12 main-section">
      <div class="modal-content">
        <div class="col-lg-12 col-sm-12 col-12 user-img">
          <img src="login-form-design-in-bootstrap-4-modal-usign-html-and-css/image/man.png">
        </div>
        <div class="col-lg-12 col-sm-12 col-12 user-name">
          <h1>User Login</h1>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="col-lg-12 col-sm-12 col-12 form-input">
          <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" >
            <div class="form-group">
              <input type="text" name="username" class="form-control" placeholder="User Name">
            </div>
            <div class="form-group">
              <input type="password"  name="password" class="form-control" placeholder="Password">
            </div>
            <button type="submit"  class="btn btn-success" value="<?php echo  '2019-10-08';?>" >Login</button>
          </form>
        </div>
        <div class="col-lg-12 col-sm-12 col-12 link-part">
       <a href="admin/register/newmember.p" target="_blank">ADD New Member</a>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysql_free_result($Recordset1);
?>

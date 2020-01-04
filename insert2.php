<?php require_once('../../Connections/accounts.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../register/fail.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO tail (IDCO, MatType,  curency, buyPiceNO, buypricepice, buytotalprice, RegName, RegDate) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       
                       GetSQLValueString($_POST['IDCO'], "int"),
                       GetSQLValueString($_POST['MatType'], "text"),
               
                       GetSQLValueString($_POST['curency'], "text"),
                       GetSQLValueString($_POST['buyPiceNO'], "int"),
                       GetSQLValueString($_POST['buypricepice'], "double"),
		GetSQLValueString($_POST['buyPiceNO']*$_POST['buypricepice'], "double"),
                       GetSQLValueString($_POST['RegName'], "text"),
                       GetSQLValueString($_POST['RegDate'], "date"));

  mysql_select_db($database_accounts, $accounts);
  $Result1 = mysql_query($insertSQL, $accounts) or die(mysql_error());
}

mysql_select_db($database_accounts, $accounts);
$query_companename = "SELECT * FROM company";
$companename = mysql_query($query_companename, $accounts) or die(mysql_error());
$row_companename = mysql_fetch_assoc($companename);
$totalRows_companename = mysql_num_rows($companename);

$colname_materialpricee = "-1";
if (isset($_GET['MCode'])) {
  $colname_materialpricee = $_GET['MCode'];
}
mysql_select_db($database_accounts, $accounts);
$query_materialpricee = sprintf("SELECT * FROM materialprice WHERE MCode = %s or MType = %s", GetSQLValueString($colname_materialpricee, "text"),GetSQLValueString($colname_materialpricee, "text"));
$materialpricee = mysql_query($query_materialpricee, $accounts) or die(mysql_error());
$row_materialpricee = mysql_fetch_assoc($materialpricee);
$totalRows_materialpricee = mysql_num_rows($materialpricee);

$maxRows_taill = 10;
$pageNum_taill = 0;
if (isset($_GET['pageNum_taill'])) {
  $pageNum_taill = $_GET['pageNum_taill'];
}
$startRow_taill = $pageNum_taill * $maxRows_taill;

$colname_taill = "-1";
if (isset($_POST['IDCO'])) {
  $colname_taill = $_POST['IDCO'];
}
mysql_select_db($database_accounts, $accounts);
$query_taill = sprintf("SELECT * FROM tail WHERE IDCO = %s", GetSQLValueString($colname_taill, "int"));
$query_limit_taill = sprintf("%s LIMIT %d, %d", $query_taill, $startRow_taill, $maxRows_taill);
$taill = mysql_query($query_limit_taill, $accounts) or die(mysql_error());
$row_taill = mysql_fetch_assoc($taill);

if (isset($_GET['totalRows_taill'])) {
  $totalRows_taill = $_GET['totalRows_taill'];
} else {
  $all_taill = mysql_query($query_taill);
  $totalRows_taill = mysql_num_rows($all_taill);
}
$totalPages_taill = ceil($totalRows_taill/$maxRows_taill)-1;

$maxRows_headbuyy = 1;
$pageNum_headbuyy = 0;
if (isset($_GET['pageNum_headbuyy'])) {
  $pageNum_headbuyy = $_GET['pageNum_headbuyy'];
}
$startRow_headbuyy = $pageNum_headbuyy * $maxRows_headbuyy;

$colname_headbuyy = "-1";
if (isset($_POST['IDCO'])) {
  $colname_headbuyy = $_POST['IDCO'];
}
mysql_select_db($database_accounts, $accounts);
$query_headbuyy = sprintf("SELECT * FROM headbuy WHERE IDCO = %s", GetSQLValueString($colname_headbuyy, "int"));
$query_limit_headbuyy = sprintf("%s LIMIT %d, %d", $query_headbuyy, $startRow_headbuyy, $maxRows_headbuyy);
$headbuyy = mysql_query($query_limit_headbuyy, $accounts) or die(mysql_error());
$row_headbuyy = mysql_fetch_assoc($headbuyy);

if (isset($_GET['totalRows_headbuyy'])) {
  $totalRows_headbuyy = $_GET['totalRows_headbuyy'];
} else {
  $all_headbuyy = mysql_query($query_headbuyy);
  $totalRows_headbuyy = mysql_num_rows($all_headbuyy);
}
$totalPages_headbuyy = ceil($totalRows_headbuyy/$maxRows_headbuyy)-1;

$maxRows_materialname = 10;
$pageNum_materialname = 0;
if (isset($_GET['pageNum_materialname'])) {
  $pageNum_materialname = $_GET['pageNum_materialname'];
}
$startRow_materialname = $pageNum_materialname * $maxRows_materialname;

mysql_select_db($database_accounts, $accounts);
$query_materialname = "SELECT * FROM material";
$query_limit_materialname = sprintf("%s LIMIT %d, %d", $query_materialname, $startRow_materialname, $maxRows_materialname);
$materialname = mysql_query($query_limit_materialname, $accounts) or die(mysql_error());
$row_materialname = mysql_fetch_assoc($materialname);

if (isset($_GET['totalRows_materialname'])) {
  $totalRows_materialname = $_GET['totalRows_materialname'];
} else {
  $all_materialname = mysql_query($query_materialname);
  $totalRows_materialname = mysql_num_rows($all_materialname);
}
$totalPages_materialname = ceil($totalRows_materialname/$maxRows_materialname)-1;

$colname_usr = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_usr = $_SESSION['MM_Username'];
}
mysql_select_db($database_accounts, $accounts);
$query_usr = sprintf("SELECT * FROM `user` WHERE username = %s", GetSQLValueString($colname_usr, "text"));
$usr = mysql_query($query_usr, $accounts) or die(mysql_error());
$row_usr = mysql_fetch_assoc($usr);
$totalRows_usr = mysql_num_rows($usr);
?>
<!DOCTYPE html>
<html lang="en-US"><!--<![endif]-->
<head>
 <link rel="stylesheet" type="text/css" href="chosen/chosen.css"/>

   <link rel="stylesheet" href="../../css/bootstrap.css" type="text/css" media="all">
<link rel="stylesheet" href="../../css/ui.datepicker.css" type="text/css" media="all">
 <link rel="stylesheet" href="../../css/main.css" type="text/css" media="all">
<link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css"><script src="../../js/bootstrap.min.js"></script>
<script src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/jquery-1.2.6.js"></script>
<script type="text/javascript" src="../../js/ui.datepicker.js"></script>
<script src="../../js/jquery/1.9.1/jquery.js"></script>
<script type="text/javascript" src="chosen/chosen.jquery.js"></script>
</head>
<body>
<BR/>

  <div class="container">
    <div class="row"  dir="rtl">
      <form action="inserthead1.php" method="post" name="form1" id="form1">
        <h4 align="right"><a href="../../indexx.php"><img src="../../images/images.png" width="43" height="41" title="الرجوع الى الصفحة الرئيسية"/></a></h4>
        <div class="row">
          <div class="col" align="center">
           <label>اسماء الشركات</label>
            <select name="company">
              <option value="" selected disabled>اسماء الشركات</option>
              <?php 
do {  
?>
              <option value="<?php echo $row_companename['companyname']?>" ><?php echo $row_companename['companyname']?></option>
              <?php
} while ($row_companename = mysql_fetch_assoc($companename));
?>
            </select>
          </div>
          <div class="col" align="center">
          <label>رقم الوصل</label>
            <input type="text" name="numberbail" value="" placeholder="رقم الوصل" />
          </div>
         
          <div class="col" align="center">
           <label>تاريخ الوصل</label>
            <input type="date" name="BuilDate" value="" placeholder="تاريخ الوصل"/>

          </div>
          <div class="col" align="center">
          <label>العملات</label>
            <select name="curency" >
              <option value="دولار" <?php if (!(strcmp("دولار", ""))) {echo "SELECTED";} ?>>دولار</option>
            </select>
          </div>
          <div class="col" align="center">
          <label>طريقة الدفع</label>
            <select name="waypaid" value="" placeholder="طريقة الدفع" >
            <option value="نقد" <?php if (!(strcmp("نقد", ""))) {echo "SELECTED";} ?>>نقد</option>
            <option value="اجل" <?php if (!(strcmp("اجل", ""))) {echo "SELECTED";} ?>>اجل</option>
           </select>
          </div>
          <div class="col" align="center">
          <label>المبلغ المدفوع</label>
            <input type="text" name="howmuchpaid" value="0" placeholder="المبلغ المدفوع"  />
          </div>
          <input type="hidden" name="RegName" value="<?php echo $row_usr['username']; ?>" size="32" />
          <input type="hidden" name="RegDate" value="<?php echo date('Y-m-d :H:m:s'); ?>" size="32" />
          <div class="col" align="center">
          <label>ادخال المشتریات</label>
            <input type="submit" value="ادخال المشتریات" />
            
        </div></div>
        <input type="hidden" name="MM_insert" value="form1" />
    </form></div>
    <?php if ($totalRows_headbuyy > 0) { // Show if recordset not empty ?>
    <h3 align="right" style="font-size:20px; color:#000">
      اسم الشركة&nbsp;:&nbsp;<?php echo $row_headbuyy['company']; ?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      رقم القائمة&nbsp;:&nbsp;<?php echo $row_headbuyy['numberbail']; ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; تاريخ القائمة&nbsp;:&nbsp;<?php echo $row_headbuyy['BuilDate']; ?></h3>
    <br/>
    
    <div class="container"> 
      <form action="<?php echo $editFormAction; ?>" method="post" name="form3" id="form3">
      <table width="630" border="4" class="fa-angle-up" style="word-wrap:break-word; text-align:center" dir="rtl" align="center">
        <thead>         
          <tr >
            <th width="2">#</th>
            <th width="2">رقم القائمة</th>
            <th width="300">نوع المادة</th>
            <th width="80">عدد القطع</th>
            <th width="120">سعر القطعة</th>
            <th width="2">option</th>
        </tr></thead>
        <tbody>
          <tr>
          
              <td class="count" style="width:50px;"></td>
              <td><input name="IDCO" type="text" style="width:50px;" value="<?php echo $row_headbuyy['IDCO']; ?>" readonly  /></td>

              
              
              
              
              <td><select style="width:500px;" name="MatType" id="MatType" class="chosen" >
                <?php 
do {  
?>
                <option value='<?php echo $row_materialname['MType']; ?>' ><?php echo $row_materialname['MType']; ?></option>
                <?php
} while ($row_materialname = mysql_fetch_assoc($materialname));
?>
              </select></td>
              
              
              
              <input type="hidden" name="curency" value="<?php echo $row_headbuyy['curency']; ?>">
              <td><input type="text" name="buyPiceNO" value="1" style="width:100px;"  /></td>
              <td><input type="text" name="buypricepice" value="" style="width:120px;"  /></td>
              <td><input type="submit" value="شراء" /></td>
              
              <input type="hidden" name="RegName" value="<?php echo $row_usr['username']; ?>" />
              <input type="hidden" name="RegDate" value="<?php echo date ('Y-m-d :H:m:s'); ?>" />
              
              </tr></tbody>
              <input type="hidden" name="MM_insert" value="form3" />
              <input name="IDCO" type="hidden" id="IDCO" value="<?php echo $row_headbuyy['IDCO']; ?>" />
</table></form></div>
    <p><br/><br/>
    <div class="container">
      <div class="row"  dir="rtl">
        <?php do { ?>
          <form name="form4" method="post" action="editbuy.php">  
            <div class="row">
              <div class="col" align="right">
                <label >رقم القائمة</label>
                <input name="IDCO" type="text" placeholder="IDCO" value="<?php echo $row_taill['IDCO']; ?>" readonly>
              </div>
              <div class="col" align="right">
                <label>نوع المادة</label>
                <input name="MatType" type="text" placeholder="نوع المادة" value="<?php echo $row_taill['MatType']; ?>" readonly>
              </div>

              <div class="col" align="right">
                <label>عدد القطع</label>
                <input name="buyPiceNO" type="text"placeholder="عدد القطع" value="<?php echo $row_taill['buyPiceNO']; ?>">
              </div>
              <div class="col" align="right">
                <label>سعر القطعة</label>
                <input name="buypricepice" type="text" placeholder=" سعر القطعة" value="<?php echo number_format($row_taill['buypricepice'],2); ?>">
              </div> 
              <div class="col" align="right">
                <label>المبلغ</label>
                <input name="buytotalprice" type="text" value="<?php echo number_format($row_taill['buytotalprice'],2); ?>" readonly>
              </div>
              <div class="col" align="right">
                <label>تعديل لقائمة الشراء</label>
                <input name="MM_update" type="submit" id="MM_update" value="تعديل لقائمة الشراء"> 
                  
              </div>
                <span style="font-size:24px;color:#000"> <a href="delete.php?ID=<?php echo $row_taill['ID']; ?>&IDCO=<?php echo $row_headbuyy['IDCO']; ?>"><img src="../../images/ifffndex.jpg"  width="40" height="38" title="حذف"></a></span>       
            </div>
            <input type="hidden" name="MM_update" value="form2" />
            <input name="ID" type="hidden" value="<?php echo $row_taill['ID']; ?>" />
          </form>
            
          <?php $bb=$bb +  $row_taill['buytotalprice'] ;?>
            
            
            
          <?php } while ($row_taill = mysql_fetch_assoc($taill)); ?>
        <p><br/>
        </p>
      </div>
      <?php $cc=$row_headbuyy['khasam']; ?>
      <?PHP $ff=$row_headbuyy['howmuchpaid']  ;?>
      <?php $hh=$row_headbuyy['curency']; ?>
            <span style="font-size:24px; color:#F00; float:right; display:inline-block"> <?php echo المبلغ.'    '  ;?><?php echo الفاتورة.' = '.number_format($bb,2).'  '.$hh ;?>    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  </span>

      <span style="font-size:24px; float:right;color:#000"> 
      <?php echo المبلغ.'    '  ;?>
      <?php echo المدفوع.'    '  ;?>=
      <?php echo  number_format($row_headbuyy['howmuchpaid'],2).'      '.$hh ;?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
      <span style="font-size:24px; float:right;color:#000"> 
      <?php echo المبلغ.'    '  ;?>
      <?php echo المتبقي.'    '  ;?>=
      <?php echo  number_format(($bb-$ff-$cc),2).'      '.$hh ;?>
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>&nbsp;&nbsp;&nbsp;
  </div></p></div></div>
  <script >
//$(".chosen").chosen({rtl: true});

$('.chosen').chosen();
$('.chosen').trigger('chosen:open');
$('.chosen').trigger('chosen:activate');

</script>
 
  <?php } // Show if recordset not empty ?>
</body>
</html>
<?php
mysql_free_result($companename);

mysql_free_result($materialpricee);

mysql_free_result($taill);

mysql_free_result($headbuyy);

mysql_free_result($materialname);

mysql_free_result($usr);
?>

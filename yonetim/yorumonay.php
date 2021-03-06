<?php require_once('../Connections/baglan.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.html";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "giris.php";
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE yorumlar SET isim=%s, email=%s, yorum=%s, tarih=%s, durum=%s WHERE id=%s",
                       GetSQLValueString($_POST['isim'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['yorum'], "text"),
                       GetSQLValueString($_POST['tarih'], "date"),
                       GetSQLValueString($_POST['durum'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_baglan, $baglan);
  $Result1 = mysql_query($updateSQL, $baglan) or die(mysql_error());

  $updateGoTo = "yorumlar.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_baglan, $baglan);
$query_ayar = "SELECT * FROM ayar";
$ayar = mysql_query($query_ayar, $baglan) or die(mysql_error());
$row_ayar = mysql_fetch_assoc($ayar);
$totalRows_ayar = mysql_num_rows($ayar);

$colname_yorunonay = "-1";
if (isset($_GET['id'])) {
  $colname_yorunonay = $_GET['id'];
}
mysql_select_db($database_baglan, $baglan);
$query_yorunonay = sprintf("SELECT * FROM yorumlar WHERE id = %s", GetSQLValueString($colname_yorunonay, "int"));
$yorunonay = mysql_query($query_yorunonay, $baglan) or die(mysql_error());
$row_yorunonay = mysql_fetch_assoc($yorunonay);
$totalRows_yorunonay = mysql_num_rows($yorunonay);
include "ust.php";
?>

    <div class="container">
	<!-- InstanceBeginEditable name="icerik alanı" -->
		<div class="row"><!-- Başlangıç -->
		<div class="col-md-4 col-md-offset-4">
			<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
				<div class="input-group">
					<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
					<td><input type="text" name="isim" value="<?php echo htmlentities($row_yorunonay['isim'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" aria-describedby="sizing-addon2" required>
				</div></br>
				<div class="input-group">
					<span class="input-group-addon" id="sizing-addon2">@</span>
					<input type="text" name="email" value="<?php echo htmlentities($row_yorunonay['email'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" placeholder="Email Adresiniz..." aria-describedby="sizing-addon2" required>
				</div></br>
				<div class="input-group">
					<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-import" aria-hidden="true"></span></span>
					<textarea name="yorum" cols="32" class="form-control"><?php echo htmlentities($row_yorunonay['yorum'], ENT_COMPAT, 'utf-8'); ?></textarea>
				</div></br>
				<div class="input-group">
					<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Yayınlansın mı?</span>
					<select name="durum" class="form-control">
					<option value="Yayında" <?php if (!(strcmp("Yayında", htmlentities($row_yorunonay['durum'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Onayla</option>
					<option value="Taslak" <?php if (!(strcmp("Taslak", htmlentities($row_yorunonay['durum'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Onaylama</option>
					</select>
				</div></br>
				<div class="input-group">
					<input type="submit" value="Kaydı Güncelleştir" class="btn btn-info" aria-describedby="sizing-addon2">
					<input type="hidden" name="tarih" value="<?php echo htmlentities($row_yorunonay['tarih'], ENT_COMPAT, 'utf-8'); ?>" />
					<input type="hidden" name="MM_update" value="form1" />
					<input type="hidden" name="id" value="<?php echo $row_yorunonay['id']; ?>" />
				</div>
			</form></br></br>
		</div>
    <!-- InstanceEndEditable -->
    </div>

<?php
include "alt.php";
mysql_free_result($ayar);
mysql_free_result($yorunonay);
?>
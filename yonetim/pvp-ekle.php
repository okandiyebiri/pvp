<?php require_once('../Connections/baglan.php');
if (!isset($_SESSION)) {
  session_start();
}

$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
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

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  $isValid = False; 

  if (!empty($UserName)) { 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO pvplist (id, baslik, durum, link, servertipi, uridium, yayinlanmadurumu) VALUES (%s, %s, %s, %s, %s, %s, %s)",
     GetSQLValueString($_POST['id'], "int"),
     GetSQLValueString($_POST['baslik'], "text"),
     GetSQLValueString($_POST['durum'], "text"),
     GetSQLValueString($_POST['link'], "text"),
     GetSQLValueString($_POST['servertipi'], "text"),
     GetSQLValueString($_POST['uridium'], "text"),
     GetSQLValueString($_POST['yayinlanmadurumu'], "text"));

  mysql_select_db($database_baglan, $baglan);
  $Result1 = mysql_query($insertSQL, $baglan) or die(mysql_error());

  $insertGoTo = "liste.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_baglan, $baglan);
$query_ayar = "SELECT * FROM ayar";
$ayar = mysql_query($query_ayar, $baglan) or die(mysql_error());
$row_ayar = mysql_fetch_assoc($ayar);
$totalRows_ayar = mysql_num_rows($ayar);

mysql_select_db($database_baglan, $baglan);
$query_pvpekle = "SELECT * FROM pvplist";
$pvpekle = mysql_query($query_pvpekle, $baglan) or die(mysql_error());
$row_pvpekle = mysql_fetch_assoc($pvpekle);
$totalRows_pvpekle = mysql_num_rows($pvpekle);
include "ust.php";
?>
    
  <div class="container">
    <div class="row">
    	<div class="col-md-4 col-md-offset-4">
    	<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
      	<div class="input-group">
        	<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span></span>
        	<input type="text" name="baslik" class="form-control" placeholder="Site başlığını yazın..." aria-describedby="sizing-addon2" required>
      	</div></br>

      	<div class="input-group">
        	<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> Server Durumu</span>
        	<select name="durum" class="form-control" aria-describedby="sizing-addon2">
        	<option value="Açık" <?php if (!(strcmp("Açık", ""))) {echo "SELECTED";} ?>>Açık</option>
        	<option value="Kapalı" <?php if (!(strcmp("Kapalı", ""))) {echo "SELECTED";} ?>>Kapalı</option>
        	<option value="Bakımda" <?php if (!(strcmp("Bakımda", ""))) {echo "SELECTED";} ?>>Bakımda</option>
        	</select>
      	</div></br>

      	<div class="input-group">
        	<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-link" aria-hidden="true"></span></span>
        	<input type="text" name="link" class="form-control" placeholder="http://" required>
      	</div></br>

      	<div class="input-group">
        	<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Server Tipi</span>
        	<select name="servertipi" class="form-control" aria-describedby="sizing-addon2">
        	<option value="Kasılmalık" <?php if (!(strcmp("Kasılmalık", ""))) {echo "SELECTED";} ?>>Kasılmalık</option>
        	<option value="VSlik" <?php if (!(strcmp("VSlik", ""))) {echo "SELECTED";} ?>>VSlik</option>
        	<option value="Bilinmiyor" <?php if (!(strcmp("Bilinmiyor", ""))) {echo "SELECTED";} ?>>Bilinmiyor</option>
        	</select>
      	</div></br>

      	<div class="input-group">
        	<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> Kapasite</span>
        	<input type="text" name="uridium" class="form-control" placeholder="Rakamsal bir değer girin..." required>
      	</div></br>

      	<div class="input-group">
        	<span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Yayınlanma Durumu</span>
        	<select name="yayinlanmadurumu" class="form-control" aria-describedby="sizing-addon2">
        	<option value="Yayınlanmış" <?php if (!(strcmp("Yayınla", ""))) {echo "SELECTED";} ?>>Yayınla</option>
        	<option value="Taslak" <?php if (!(strcmp("Taslak", ""))) {echo "SELECTED";} ?>>Taslak</option>
        	</select>
      	</div></br>

      	<div class="input-group">
          <input type="hidden" name="MM_insert" value="form1" />
        	<input type="submit" value="Kayıt Ekle" class="btn btn-info">
      	</div></br>
    	<div class="input-group">
    	</div>
    	
    	</form></br></br>
  	</div>
</div>

    </div>
<?php
include "alt.php";
mysql_free_result($ayar);
mysql_free_result($pvpekle);
?>
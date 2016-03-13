<?
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/* Dateiname: change_pw.php
*  Zweck: Formular zur Passwortänderung
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
isAllow(isAdmin() || isSecretary() || isEmployee());

/* _POST einlesen */
$save_x = $_POST['save_x'];

/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }
if (!isset($no_error)) $no_error=1;
?>

<HTML>

<HEAD>
	<link rel="stylesheet" href="../css/ta.css">
</HEAD>
<BODY>

<?
if (isset($save_x))
{
	if ($print_debug == 1) var_dump($_POST);

	$username = $_POST['username'];
	$current_password = $_POST['current_password'];
	$new_password = $_POST['new_password'];
	$password_check = $_POST['password_check'];

	$current_password_crypt=crypt($current_password,'activities');

	$rs_pwcheck_select = getrs("select id, password from employees where id=$id",$print_debug,$print_error);
	LIST($id,$password_db)=$rs_pwcheck_select -> fetch_row();
	
	if (isAdmin()) { $current_password="1234";}
	$no_error = true && CheckEmpty($current_password,$error_current_password) &&
				CheckPwd($new_password,$password_check ,0,$error_password) && 
				CheckPwd($new_password,$password_check ,0,$error_password_check) && true;

	if ((($password_db == $current_password_crypt) || (isAdmin())) && (isset($id) && $id!="" && $no_error))
 	{
		$new_password_db=crypt($new_password, 'activities');
		$sql_pw_update = "update employees set password='$new_password_db' where id=".$id;
		$rs_pw_change = getrs($sql_pw_update,$print_debug,$print_error);
?>
	<table border=0 bordercolor=black cellspacing=0 cellpadding=0>
		<tr><td height=12></td></tr>
		<tr><td width=200 height=27 align=center>
			<SPAN class="headline">Passwortänderung durchgeführt!</SPAN><br>
		</td></tr>
	</table>
<?
	die;

	}
	else 
	{
		$no_error=false;
		$error_current_password="Eingegebenes Passwort ist falsch!";
	}
}

// Daten aus DB Laden
if (isset($id) && $id!="" && $no_error)
{
		$rs_employee_select = getrs("select id, firstname, lastname, username,password from employees where id=$id",$print_debug,$print_error);
		LIST($id,$firstname,$lastname,$username,$pwd)=$rs_employee_select -> fetch_row();
}
?>
	<center>
	<table border=0 bordercolor=black cellspacing=0 cellpadding=0>
		<tr><td height=12></td></tr>
		<tr><td width=200 height=27 align=center>
			<SPAN class="headline">Passwortänderung <br><?print($lastname." ".$firstname)?></SPAN><br>
		</td></tr>
	</table>
	<br>
	
	<BR>
	<!-- Formular Anfang -->

	<FORM  action="<? echo $PHP_SELF?>" method="POST" enctype="multipart/form-data" name=formular>

	<TABLE width=500 border=0 CELLPADDING=0 CELLSPACING=0>
	<tr>
		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Benutzername</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<? echo $username?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
<?if (isEmployee() || isSecretary())
{?>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%> aktuelles Passwort </TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<?echo display_error($error_current_password);?>
		<INPUT TYPE=PASSWORD MAXLENGTH=150 SIZE=50 NAME=current_password VALUE="<?echo $current_password?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
<?}?>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%> neues Passwort </TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<?echo display_error($error_password);?>
		<INPUT TYPE=PASSWORD MAXLENGTH=150 SIZE=50 NAME=new_password VALUE="<?echo $new_password?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%><nobr> neues Passwort bestätigen </nobr></TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<?echo display_error($error_password);?>
		<INPUT TYPE=PASSWORD MAXLENGTH=150 SIZE=50 NAME=password_check VALUE="<?echo $password_check?>"></TD></TR></TABLE></TD>
	</TR>
	<TR>
		<TD colspan=3 height=50 class=form_footer>
		<TABLE WIDTH=100% HEIGHT=100% border=0><TR><TD ALIGN=CENTER>
		<INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD > <TD ALIGN=CENTER>
		<INPUT TYPE="HIDDEN" NAME="id" value=<?echo $id?>>
		<INPUT TYPE="HIDDEN" NAME="print_debug" value=<?echo $print_debug?>>
		<INPUT TYPE="HIDDEN" NAME="print_error" value=<?echo $print_error?>>
		</TD></TR></TABLE></TD>    </FORM>
	</TR>

</CENTER>
</BODY>

</HTML>
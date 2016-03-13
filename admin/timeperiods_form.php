<?
/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");
isAllow(isAdmin() || isSecretary());

/* Dateiname: timeperiods_form.php
*  Zweck: Formular zur Eingabe von Zeitperioden
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
isAllow(isAdmin() || isSecretary());

/* _POST einlesen */
$confirm = $_POST['confirm'];
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$timeperiods = $_POST['timeperiods'];
$status = $_POST['status'];
$camp = $_POST['camp'];
$detail=$_POST['detail'];

?>

<HTML>
<HEAD>
	<link rel="stylesheet" href="../css/ta.css">
	<script type="text/javascript">
	function delete_form()
	{
  		document.formular.confirm.value = confirm("Wollen Sie diesen Eintrag wirklich l�en?");
	}

	function show_message(){
    	document.getElementById('light').style.display='block';
    	document.getElementById('fade').style.display='block';
    }

	</script>
</HEAD>

<? 
// Delete-Button wird gedr￿$back_url="../admin/timeperiods_list.php";

if (isset($delete_x))
{
        if ($confirm=="true" && $id>0)
        {
           $sql_timeperiods_delete="update timeperiods set status='Entfernt' where id=".$id;
           $rs_timeperiods_delete=getrs($sql_timeperiods_delete,$print_debug,$print_error);
           ok_site($back_url);
        }
        else
        {
           error_site($back_url);
        }
}

// Save-Button wird gedr￿
elseif (isset($save_x))
{

  $no_error= CheckEmpty($timeperiods,$error_timeperiods);

  if ($status=="")
    $status="Inaktiv";

  if (isset($id) && $id!="" && $no_error)
  {
       $sql_timeperiods_update="update timeperiods set
					                 name='$timeperiods',
                 					 status ='$status',
                 					 camp ='$camp',
				 					 change_date = sysdate(),
									 detail ='$detail'
       							where id=".$id;
       $rs_timeperiods_update=getrs($sql_timeperiods_update,$print_debug,$print_error);
  }
  elseif($no_error)
  {

       $sql_timeperiods_insert="insert into timeperiods
					                (name,status,camp,detail,change_date)
              					values
               						('$timeperiods','$status','$camp','$detail',sysdate())";
       $rs_timeperiods_insert=getrs($sql_timeperiods_insert,$print_debug,$print_error);
  }
  else
  {
		$update=1;
  }
}

// Back-Button wird gedr￿
elseif (isset($back_x))
{
  header("Location: $back_url");
}

// Daten aus DB Laden

if (isset($id) && $id!="" && !$update)
{
           $sql_timeperiods_select="select id,name,status,camp,detail from timeperiods where id=".$id;
           $rs_timeperiods_select=getrs($sql_timeperiods_select,$print_debug,$print_error);
           LIST($id,$timeperiods,$status,$camp,$detail)=$rs_timeperiods_select -> fetch_row();
}

?>

<?if (isset($save_x) && $no_error) $command="show_message();";?>

<BODY onload="<?print($command)?>">
<!--  Div f￿sagebox und fade des Hintergrundes -->		
		<div id="light" class="white_content">
			<center>
			<b>&Auml;derungen erfolgreich gespeichert!</b>
			<br>
			<a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
			</center>
		</div>
		<div id="fade" class="black_overlay"></div>
		
	<center>
	<table border=0 cellspacing=0 cellpadding=0>
		<tr><td height=12></td></tr>
			<SPAN class="headline">Zeitperiode</SPAN><br>
		</td></tr>
		<tr><td height=10></td></tr>
	</table>
	<BR><BR>

	<FORM  action="<? echo $PHP_SELF?>" method="POST" name=formular>
	<TABLE width=400 border=0 CELLPADDING=0 CELLSPACING=0>
	<tr>
   		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   		<td colspan=3 HEIGHT=1  class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
   		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Zeitperiode</TD></TR></TABLE></TD>
   		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   		<?echo display_error($error_timeperiods);?>
   		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=timeperiods VALUE="<?echo $timeperiods?>">
   		</TD></TR></TABLE></TD>
	</TR>
	<tr>
   		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
   		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Aktiv</TD></TR></TABLE></TD>
   		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
<?
   if ($status=="Aktiv")
           $text="checked";
   else
           $text="";
   ?>
	   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%><INPUT TYPE=checkbox NAME=status <? echo $text?> VALUE='Aktiv'></TD></TR></TABLE></TD>
	</TR>
	<tr>
   		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
   		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Camp ?</TD></TR></TABLE></TD>
   		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
<?
   if ($camp=="1")
           $text="checked";
   else
           $text="";
   ?>
	   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%><INPUT TYPE=checkbox NAME=camp <? echo $text?> VALUE='1'></TD></TR></TABLE></TD>
	</TR>
	<tr>
   		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
   		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>genaues Campdatum:</TD></TR></TABLE></TD>
   		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=detail VALUE="<?echo $detail?>">
   		</TD></TR></TABLE></TD>
	</TR>
	<tr>
   		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
   		<TD colspan=3 height=50 class=form_footer>
   		<TABLE WIDTH=100% HEIGHT=100%><TR><TD ALIGN=CENTER>
   			<INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD > <TD ALIGN=CENTER>
<?if (isAdmin()) { ?>
		   <INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
<?}?>
   			<INPUT TYPE="HIDDEN" NAME="confirm" value=0>
   			<INPUT TYPE="HIDDEN" NAME="modus" value=<?echo $modus?>>
   			<INPUT TYPE="HIDDEN" NAME="id" value=<?echo $id?>>
    		</TD></TR>
    	</TABLE></TD>    </FORM>
	</TR>
	<tr>
   		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
</TABLE>
<BR><br><br>
</CENTER>
</BODY>
</HTML>
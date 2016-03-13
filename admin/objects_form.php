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

/* Dateiname: objects_form.php
*  Zweck: Formular zur Eingabe der Sportgeräte
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
isAllow(isAdmin() || isSecretary());

/* _POST einlesen */
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$confirm = $_POST['confirm'];

$back_url="../admin/institution_list.php";

// Delete-Button wird gedrückt

if (isset($delete_x))
{
        if ($confirm=="true" && $id>0)
        {
           $sql_objects_delete="update objects set status='Entfernt' where id=".$id;
           $rs_objects_delete=getrs($sql_objects_delete);
           ok_site($back_url);
        }
	    else
        {
           error_site($back_url);
        }
}

// Save-Button wird gedrückt
elseif (isset($save_x))
{
  $pieces = $_POST['pieces'];
  $name = $_POST['name'];
  $desc = $_POST['desc'];
  $status = $_POST['status'];

  $no_error=true &&
  			CheckEmpty($name,$error_name) &&
			CheckEmpty($pieces,$error_pieces);

  if ($status=="")
    $status="Inaktiv";

  if (isset($id) && $id!="" && $no_error)
  {
  	  $sql_objects_update="update objects set
	                 pieces       = '$pieces',
	                 name         = '$name',
	                 description  = '$desc',
					 available    = '$pieces'-loan,
	                 status  = '$status'
			      where id=".$id;
	  $rs_objects_update=getrs($sql_objects_update,$print_debug,$print_error);
  }
  elseif($no_error)
  {
      $sql_objects_insert="insert into objects
                              (name,pieces,available,description,status)
                           values
				              ('$name','$pieces','$pieces','$description','$status')";
      $rs_objects_insert=getrs($sql_objects_insert,$print_debug,$print_error);
  }
}

// Daten aus DB Laden
if (isset($id) && $id!="")
{
           $sql_objects_select="select * from objects where id=".$id;
           $rs_objects_select=getrs($sql_objects_select);
           LIST($obj_id,$name,$pieces,$available,$loan,$desc,$status)=$rs_objects_select -> fetch_row();
}
?>

<HTML>
<HEAD>
	<link rel="stylesheet" href="../css/ta.css">
	<script type="text/javascript">
	function delete_form()
	{
		  document.formular.confirm.value = confirm("Wollen Sie diesen Eintrag wirklich löschen?");
	}
	function check_before_change()
	{
		  confirm("Achtung!! Sie verändern dadurch den Anfangslagerstand");
	}
	function show_message(){
		document.getElementById('light').style.display='block';
		document.getElementById('fade').style.display='block';
	}

	</script>
</HEAD>

<?if (isset($save_x) && $no_error) $command="show_message();";?>

<BODY onload="<?print($command)?>">
<!--  Div für messagebox und fade des Hintergrundes -->		
		<div id="light" class="white_content">
			<center>
			<b>Änderungen erfolgreich gespeichert!</b>
			<br>
			<a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
			</center>
		</div>
		<div id="fade" class="black_overlay"></div>

	<center>
	<table border=0 cellspacing=0 cellpadding=0>
		<tr><td height=12></td></tr>
		<SPAN class="headline">Geräteinventar</SPAN><br>
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
	<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Menge</TD></TR></TABLE></TD>
	<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	<TD width=250  class=form_row>
	<TABLE>
	    <TR><TD >
			<input type="Text" size=5 name=pieces onchange="check_before_change()" value="<?print $pieces?>">
		</TD></TR>
	</TABLE>
	</TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Gerätebezeichnung</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <?echo display_error($error_name);?>
   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=name VALUE="<?echo $name?>"></TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Beschreibung</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <?echo display_error($error_description);?>
   <TEXTAREA ROWS=5 COLS=40 NAME="desc"><?echo $desc?></TEXTAREA></TD></TR></TABLE></TD>
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
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Entlehner</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%><nobr>
<?
   $rs_loan_select=getrs("select concat(a.firstname,' ',a.lastname,' - ',b.pieces,' Stück')
   				from employees a, loan_objects b 
				where $id=b.name_id and b.employees_id=a.id and b.status='Nein'",$print_debug,$print_error);

   if ($rs_loan_select -> num_rows>0)
   {
         while(LIST($loan_name)=$rs_loan_select->fetch_row())
         {
               echo "$loan_name<br>";
         }
   }
   else { echo "<table><tr><td class=error>Wurde nicht entlehnt !</td></tr></table>";}
?>
   </nobr></TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD colspan=3 height=50 class=form_footer>
   <TABLE WIDTH=100% HEIGHT=100%><TR><TD ALIGN=CENTER>
   <INPUT TYPE="IMAGE"  NAME="save" src="../images/buttons/send.gif"></TD > <TD ALIGN=CENTER>
   <INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
   <INPUT TYPE="HIDDEN" NAME="confirm" value=0>
   <INPUT TYPE="HIDDEN" NAME="modus" value=<?echo $modus?>>
   <INPUT TYPE="HIDDEN" NAME="id" value=<?echo $id?>>
    </TD></TR></TABLE></TD>    </FORM>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
</TABLE>
</CENTER>
</BODY>
</HTML>
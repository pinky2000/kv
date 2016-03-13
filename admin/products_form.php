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

/* Dateiname: products_form.php
*  Zweck: Formular zur Eingabe von Produkten
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
isAllow(isAdmin() || isSecretary());

/* _POST einlesen */
$confirm = $_POST['confirm'];
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$productname = $_POST['productname'];
$standard_hourcost = $_POST['standard_hourcost'];
$status = $_POST['status'];

// Delete-Button wird gedrückt
$back_url="../admin/products_list.php";

if (isset($delete_x))
{
        if ($confirm=="true" && $id>0)
        {
           $sql_products_delete="update products set status='Entfernt' where id=".$id;
           $rs_products_delete=getrs($sql_products_delete,$print_debug,$print_error);
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
  $no_error = CheckEmpty($productname,$error_productname);

  if ($status=="")
    $status="Inaktiv";

  if (isset($id) && $id!="" && $no_error)
  {
       $sql_products_update="update products set
				                 name='$productname',
                 				 status ='$status',
                 				 standard_hourcost  ='$standard_hourcost',
				 				 change_date = sysdate()
       						 where id=".$id;
       $rs_products_update=getrs($sql_products_update,$print_debug,$print_error);
  }
  elseif($no_error)
  {
       $sql_products_insert="insert into products
                				(name,status,standard_hourcost,change_date)
                            values
               					('$productname','$status','$standard_hourcost',sysdate())";
       $rs_products_insert=getrs($sql_products_insert,$print_debug,$print_error);
	   $id=mysqli_insert_id($DB_TA_CONNECT);
	   
	   $sql_hourcosts_insert="insert into hourcosts (value,products_id,employee_id)
    							select '$standard_hourcost', '$id', employees.id 
    							from employees  
    							where employees.status = 'Aktiv'";
       $rs_hourcosts_insert=getrs($sql_hourcosts_insert,$print_debug,$print_error);
  }
  else
  {
	$update=1;
  }
  	
}

// Daten aus DB Laden

if (isset($id) && $id!="" && !$update)
{
    $sql_products_select="select * from products where id=".$id;
    $rs_products_select=getrs($sql_products_select,$print_debug,$print_error);
    LIST($id,$productname,$status,$standard_hourcost)=$rs_products_select -> fetch_row();
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
		<SPAN class="headline">Produkt</SPAN><br>
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
   		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Produktname</TD></TR></TABLE></TD>
   		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   		<?echo display_error($error_productname);?>
   		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=productname VALUE="<?echo $productname?>">
   		</TD></TR></TABLE></TD>
	</TR>
	<tr>
   		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
	    <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Standardstundensatz</TD></TR></TABLE></TD>
   		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
	   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=standard_hourcost VALUE="<?echo $standard_hourcost?>"></TD></TR></TABLE></TD>
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
   		<TD colspan=3 height=50 class=form_footer>
   		<TABLE WIDTH=100% HEIGHT=100%><TR><TD ALIGN=CENTER>
   		<INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD > <TD ALIGN=CENTER>
<?if (isAdmin()) { ?>
   		<INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
<?}?>
   		<INPUT TYPE="HIDDEN" NAME="confirm" value=0>
   		<INPUT TYPE="HIDDEN" NAME="modus" value=<?echo $modus?>>
   		<INPUT TYPE="HIDDEN" NAME="orig_hc" value=<?echo $standard_hourcost?>>
   		<INPUT TYPE="HIDDEN" NAME="id" value=<?echo $id?>>
    	</TD></TR></TABLE></TD>    
	</TR>
	<tr>
   		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	</TABLE>
</FORM>
	
<BR><br><br>
</CENTER>
</BODY>
</HTML>
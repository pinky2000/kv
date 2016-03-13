<?
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");
isAllow(isAdmin() || isSecretary());

/* Dateiname: hourcost_form.php
*  Zweck: Formular zur Eingabe der Standardstundensätze 
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];

/* _POST einlesen */
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$employee = $_POST['employee'];
$hc_data = $_POST['hc_data'];
$action = $_POST['action'];

?>
<HTML>
<HEAD>
	<link rel="stylesheet" href="../css/ta.css">
	<script type="text/javascript">
	function show_message(){
    	document.getElementById('light').style.display='block';
    	document.getElementById('fade').style.display='block';
    }
	</script>
</HEAD>

<?if (isset($save_x)) $command="show_message();";?>

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
<? 
if ($print_debug == 1) var_dump($_POST);

if (isset($save_x))
{
	foreach ($hc_data as $e_id => $hc_data_1) 
	{
		foreach ($hc_data_1 as $p_id => $product_data)
		{
			if ($product_data[2]!="")
			{	
	      		$sql_hc_update="update hourcosts set
                 	 		value  ='$product_data[1]'
	  		      			where employee_id='$e_id' and products_id ='$product_data[0]'";
          		$rs_hc_update=getrs($sql_hc_update,$print_debug,$print_error);
			} else {
	  		  	$sql_hc_insert="insert into hourcosts
                                    (products_id,employee_id,value)
    	                    	values
        	       			    ('$product_data[0]','$e_id','$product_data[1]')";
	        	$rs_hc_insert=getrs($sql_hc_insert,$print_debug,$print_error);
			}
		}
	}
}


// Back-Button wird gedrückt

elseif (isset($back_x))
{
  header("Location: $back_url");
}

// Daten aus DB Laden

elseif (isset($id) && $id!="")
{
           $sql="select * from products where id=".$id." and status in ('Aktiv') order by name asc";
           $rs=getrs($sql,$DEBUG);
           LIST($id,$productname,$status,$standard_hourcost)=mysql_fetch_row($rs);
}

?>

<center>

<table border=0 cellspacing=0 cellpadding=0>
	<tr><td height=12></td></tr>
	<SPAN class="headline">Stundensätze</SPAN><br>
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
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Mitarbeiter:<br>
   <input type="submit" name="action" value="anzeigen">
   </TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
	<select size=20 rows=10 multiple name="employee[]">
		<option value="0">
<?
	$sql_employees_select="select id,title,lastname,firstname from employees where status in ('Aktiv') order by lastname";
	$rs_employees_select=getrs($sql_employees_select,$print_debug,$print_error);
	while ((LIST($mid,$title,$lastname,$firstname)=$rs_employees_select -> fetch_row())) 
	{ ?>
		<option value="<?echo $mid?>" <?for($a=0;$a<sizeof($employee);$a++) { if ($employee[$a]==$mid) echo "selected"; }?>><?echo $lastname." ".$firstname?>
	<?}?>
	</select>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
<TABLE border=0 CELLPADDING=0 CELLSPACING=0>
<tr height=10><Td></Td></tr>
<TR>

<?
if ($action=="anzeigen")
{
foreach ($employee as $employee_id)
{
	echo "<TD>";
	$sql_employee_name_select="select lastname, firstname from employees where id=$employee_id";
	$rs_employee_name_select = getrs($sql_employee_name_select,$print_debug,$print_error);
	LIST($lastname,$firstname) = $rs_employee_name_select -> fetch_row();
	echo "<b>$lastname $firstname</b>";

	$sql_hourcost_select="select products.id,
								 products.name,
								 hourcosts.value,
								 products.standard_hourcost,
								 hourcosts.id 
						  from products 
						  left join hourcosts on (products.id=hourcosts.products_id) and hourcosts.employee_id=$employee_id and status in ('Aktiv') 
						  WHERE products.status = 'Aktiv' group by products.id order by name asc ";

	$rs_hourcost_select = getrs($sql_hourcost_select,$print_debug,$print_error);
	$i=0;
	if ($rs_hourcost_select -> errno == 0)
	{
	while ((LIST($hc_data[$employee_id][$i][0],$productname,$hc_data[$employee_id][$i][1],$product_standard_hourcost,$hc_data[$employee_id][$i][2])=$rs_hourcost_select -> fetch_row())) 
	{
		if (($hc_data[$employee_id][$i][1] == "") || ($hc_data[$employee_id][$i][1] == 0))
		{
			 $hc_data[$employee_id][$i][1] = $product_standard_hourcost;
		}
?>
<TABLE border=0 CELLPADDING=0 CELLSPACING=0>
<? if ($i==0) 
{ ?>
<tr>
   <td colspan=4 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<? } ?>
<TR>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif"></TD>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%><?echo $productname?></TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif"></TD>
   <TD class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <INPUT TYPE=HIDDEN NAME="hc_data[<?echo $employee_id?>][<?echo $i?>][0]" VALUE="<?echo $hc_data[$employee_id][$i][0]?>">
   <INPUT TYPE=TEXT NAME="hc_data[<?echo $employee_id?>][<?echo $i?>][1]" VALUE="<?echo $hc_data[$employee_id][$i][1]?>">
   <INPUT TYPE=HIDDEN NAME="hc_data[<?echo $employee_id?>][<?echo $i?>][2]" VALUE="<?echo $hc_data[$employee_id][$i][2]?>">
   </TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif"></TD>
   <TD width=50></TD>
</TR>
<tr>
   <td colspan=4 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

</TABLE>

<?
    	$i++;
	}
}
	echo "</TD>";
}
}
?>
</TR>
</TABLE>
<TR>
   <TD colspan=3 class=form_footer>
   <TABLE WIDTH=100%><TR><TD ALIGN=CENTER>
   <br>
   <INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD > <TD ALIGN=CENTER>
    </TD></TR></TABLE></TD>    
</FORM>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

</TABLE>
</CENTER>

</BODY>
</HTML>
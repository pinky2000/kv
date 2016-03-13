<?
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");
isAllow(isAdmin() || isSecretary() || isEmployee());

/* Dateiname: details.php
*  Zweck: Detailansicht über die eingetragenen Stundeninhalte von einem "übergebenen" Kurs
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];

/* _GET einlesen */
$id = $_GET['id'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<link href="../css/ta.css" type="text/css" rel="stylesheet">

<html>
<head>
	<title>Campübersicht</title>

<style type="text/css">
<!--
.top {
		font-family: Arial;
		font-size: 10;
	}

.top_red {
		font-family: Arial;
		font-size: 10;
		color: #cc0000;
	}

.text {
		font-family: Arial;
		font-size: 12;
	}

#webfx-about {
text-align: center;
writing-mode: tb-rl;
}
-->
</style>
</head>
<body>
<font size=+1>Allgemeine und zus&auml;tzliche Infos:</font><br><br>
<div align=left>
<?
$sql_details_select = "SELECT 
		b.remarks,
		b.locations_id,
		b.institutions_id,
		c.name,
		c.address,
		c.city,
		c.zip,
		c.contactperson,
		c.phone1,
		c.phone2,
		c.remarks,
		e.name,
		e.address,
		e.city,
		e.zip,
		e.contactperson,
		e.phone1,
		e.phone2,
		e.remarks
	FROM 
		products a,
		courses b,
		institutions c,
		institutions e
	where 
		b.id=$id and 
		b.products_id=a.id and 
		b.locations_id=e.id and
		b.institutions_id=c.id";

	$rs_details_select = getrs($sql_details_select,$print_debug,$print_error);
	LIST($course_remarks,$loc_name,$loc_address,$loc_city,$loc_zip,$loc_kontakt,$loc_tel1,$loc_tel2,$loc_remarks,$school_name,$school_address,$school_city,$school_zip,$school_kontakt,$school_tel1,$school_tel2,$school_remarks)=$rs_details_select -> fetch_row();
?>
<table border=1>
<tr>
	<td>
		<?echo $course_remarks;?>
	</td>
</tr>
</table>

</div>
<font size=+1>Detailinfos:</font><br><br>
<div align=left>
<table border=1>
<tr class="form_header" align=center>
	<td class="headline_s"> &nbsp;Termin &nbsp;</td>
	<td class="headline_s"> &nbsp;Bemerkungen &nbsp;</td>
	<td class="headline_s" colspan=5> &nbsp;Kursleiter &nbsp;</td>
</tr>
<?
$sql_content_select="select id,date,content,remarks,used_items,checked from coursetimes where courses_id=$id order by date asc";
$rs_content_select = getrs($sql_content_select,$print_debug,$print_error);
while(list($ctid,$date,$hourcontent,$remarks,$used_items)= $rs_content_select -> fetch_row())
{
	$d = explode("-",$date);
	$date=$d[2].".".$d[1].".".$d[0];
	print("<tr><td>$date</td><td>$remarks</td>");
	$sql_emp="select lastname,firstname,phone1,phone2 from employees, coursetimes_employees where coursetimes_employees.coursetimes_id=$ctid and employees.id=coursetimes_employees.employees_id order by coursetimes_employees.id";
	$rs_emp = getrs($sql_emp,$print_debug,$print_error);
	while(list($lastname,$firstname,$tel1,$tel2)=$rs_emp -> fetch_row())
	{
		print("<td>$lastname $firstname<br>$tel1, $tel2</td>");
	}
	print("</tr>");
	print("<tr class=form_header><td height=1 colspan=4></td></tr>");
}
?>
</table>
</div>

</body>
</html>



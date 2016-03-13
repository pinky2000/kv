<?
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");
isAllow(isAdmin() || isSecretary() || isEmployee());

/* Dateiname: personen_camp.php
*  Zweck: Liste der angemeldeten Personen
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];

/* _GET einlesen */
$id = $_GET['id'];
$client_id=$_POST['client_id'];
$comment=$_POST['comment'];
$send=$_POST['send'];
$anzahl_kunden=$_POST['anzahl_kunden'];

if (isset($send))
{
	for ($i=0;$i<$anzahl_kunden;$i++)
	{
		if ($comment[$i][id]=="")
		{
			// insert new dataset
			$sql_comment_insert="insert into rooms(courses_id,clients_id,comment) values ('".$id."','".$client_id[$i]."','".$comment[$i][value]."')";
       		$rs_comment_insert=getrs($sql_comment_insert,$print_debug,$print_error);
		}
		else
		{
			// update existing one
			$sql_comment_update="update rooms set comment='".htmlentities($comment[$i][value])."' where id='".$comment[$i][id]."'";
			$rs_comment_update=getrs($sql_comment_update,$print_debug,$print_error);
		}
	}
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<link href="../css/ta.css" type="text/css" rel="stylesheet">

<html>
<head>
	<title>Personen-Infos</title>

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
<font size=+1>Detailinfos zu den angemeldeten Kunden:</font><br><br>
<div align=left>
<?
$sql_personen_select = "SELECT 
		  a.id,
		  a.firstname,
		  a.lastname,
		  a.sex,
		  a.sprachlevel_deutsch,
		  b.name,
		  a.country,
		  a.birthdate,
		  a.sv_number,
		  a.remarks,
		  c.remarks,
		  a.campdaten_schwimmen,
		  a.campdaten_allergien,
		  d.comment,
		  d.id
		from
		  language b,
		  payments c,
  		  clients a
		left join rooms d on (d.courses_id=$id and d.clients_id=a.id)

		where
		  c.courses_id=$id and
		  a.language_id=b.id and
		  a.id=c.clients_id
		group by a.id  
		order by a.lastname asc";

	$rs_personen_select = getrs($sql_personen_select,$print_debug,$print_error);
	
?>
<form action="<?echo $PHP_SELF?>" method="post" name="personen">

<table border="1" style="border-style:solid">
<tr class="form_header" align=center>
	<td class="headline_s"> &nbsp;Nr. &nbsp;</td>
	<td class="headline_s"> &nbsp;Name &nbsp;</td>
	<td class="headline_s"> &nbsp;Geschlecht &nbsp;</td>
	<td class="headline_s"> &nbsp;Sprachniveau &nbsp;</td>
	<td class="headline_s"> &nbsp;Geburtstag &nbsp;</td>
	<td class="headline_s"> &nbsp;Land &nbsp;</td>
	<td class="headline_s"> &nbsp;Muttersprache &nbsp;</td>
	<td class="headline_s"> &nbsp;SV Nummer &nbsp;</td>
	<td class="headline_s"> &nbsp;Anmerkungen Kunde&nbsp;</td>
	<td class="headline_s"> &nbsp;Anmerkungen Anmeldung&nbsp;</td>
	<td class="headline_s"> &nbsp;Schwimmer &nbsp;</td>
	<td class="headline_s"> &nbsp;Allergien &nbsp;</td>
	<td class="headline_s"> &nbsp;Zimmereinteilung / Kommentierung &nbsp;</td>
</tr>
<?
$i=0;
while(($rs_personen_select > 0) && LIST($client_id[$i],$firstname,$lastname,$sex,$sprachlevel,$muttersprache,$country,$birthdate,$sv_number,$remarks1,$remarks2,$schwimmen,$allergien,$comment[$i][value],$comment[$i][id])=$rs_personen_select -> fetch_row())
{
?>
<tr>
	<td><?$count ++; print($count);?></td>
	<td><?print($lastname." ".$firstname);?></td>
	<? if ($sex=="0") { $geschlecht="weiblich"; } else { $geschlecht="m&auml;nnlich"; } ?>
	<td><?print($geschlecht);?></td>
	<?
	switch($sprachlevel)
	{
		case 1: $sprachlevel_text="A1-Anf&auml;nger I"; break;
		case 2: $sprachlevel_text="A2-Anf&auml;nger II"; break;
		case 3: $sprachlevel_text="B1-Fortgeschritten I"; break;
		case 4: $sprachlevel_text="B2-Fortgeschritten II"; break;
		case 5: $sprachlevel_text="C1-Native"; break;
	}?>	
	<td><?print($sprachlevel_text);?></td>
	<td><?print($birthdate);?></td>
	<td><?print($country);?></td>
	<td><?print($muttersprache);?></td>
	<td><?print($sv_number);?></td>
	<td><?print($remarks1);?></td>
	<td><?print($remarks2);?></td>
	<?
	switch($schwimmen)
	{
		case 0: $schwimmen_text="Nichtschwimmer"; break;
		case 1: $schwimmen_text="schlechter Schwimmer"; break;
		case 2: $schwimmen_text="guter Schwimmer"; break;
	}?>	
	<td><?print($schwimmen_text);?></td>
	<td><?print($allergien);?></td>
	<td>
		<input type=text name="comment[<?print($i);?>][value]" size=20 value="<?print($comment[$i][value])?>">
		<input type=hidden name="comment[<?print($i);?>][id]" value="<?print($comment[$i][id])?>">
		<input type=hidden name="client_id[<?print($i);?>]" value="<?print($client_id[$i])?>">

	</td>	
</tr>
<?
	$i ++;
}
?>
<tr height=10></tr>
<tr>
	<td colspan=15 align=center>
		<input type=submit name="send" value="Speichern">
		<input type=hidden name=anzahl_kunden value=<?print($i)?>
</tr>

</table>
</div>
</form>
</body>
</html>



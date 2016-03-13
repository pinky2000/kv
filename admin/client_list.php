<?
require_once("../include/session.php");
require_once("../include/autotable.php");

/* Dateiname: client_list.php
*  Zweck: Übersichtsliste über die Kunden mit Filter- und Sortierfunktion
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];

isAllow(isAdmin()   || isSecretary());

/* Anzahl der angezeigten Elemente */
$pagesize = $_GET["pagesize"];
/* Nummer der sortierten Spalte */
$sortcol = $_GET["sortcol"];
/* Aktuelle Position in der Ergebnisliste */
$rowpos = $_GET["rowpos"];

/* Suchfelder: field=allgemeines Suchfeld, field_vn=Vorname, field_nn=Nachname */
if (!empty($_POST["field"])) {
	$field = $_POST["field"];
} else {
	$field = $_GET["field"];
}

if (!empty($_POST["field_vn"])) {
	$field_vn = $_POST["field_vn"];
} else {
	$field_vn = $_GET["field_vn"];
}

if (!empty($_POST["field_nn"])) {
	$field_nn = $_POST["field_nn"];
} else {
	$field_nn = $_GET["field_nn"];
}

/* SQL Query für die Ausgabe der Kundenliste, inkl. Filterung der Sucheingabe */
$sql_client_list  = "SELECT   a.id,
						 	  a.id			  AS 'ID',
			                  a.lastname    AS 'Nachname',
			                  a.firstname   AS 'Vorname',
			                  a.zip         AS 'Plz',
			                  a.phone1         AS 'Tel',
			                  a.email         AS 'Email',
			                  a.city        AS 'Ort',
			                  CONCAT(a.address,'<br>',a.zip,' ',a.city)   AS 'Adresse',
			                  b.name        AS 'Schule'
			         FROM
			                  clients a,
			                  institutions b
					 WHERE
			                  a.status in ('Aktiv') and
			                  a.school_id = b.id and
							  a.id > 0";

if ($field!="") 
{
	$sql_client_list.=" and (";
	$ff=explode(",",$field);
	for ($i=0;$i<sizeof($ff);$i++)
	{
		$ff1=explode("|",$ff[$i]);
		for ($ii=0;$ii<sizeof($ff1);$ii++)
		{
			$sql_client_list.="	( a.lastname LIKE '%".$ff1[$ii]."%'
								or a.firstname LIKE '%".$ff1[$ii]."%'
								or a.id LIKE '".$ff1[$ii]."%'
								or a.zip LIKE '".$ff1[$ii]."%' 
								or a.address LIKE '".$ff1[$ii]."%' 
								or a.phone1 LIKE '".$ff1[$ii]."%' 
								or a.email LIKE '".$ff1[$ii]."%' 
								or a.city LIKE '".$ff1[$ii]."%' 
								or a.birthdate LIKE '".$ff1[$ii]."%' 
								or b.name LIKE '".$ff1[$ii]."%' 
								or a.status LIKE '".$ff1[$ii]."%'";
			if ($ii==(sizeof($ff1)-1)) 
			{
				$sql_client_list.=")";
			} else
			{
				$sql_client_list.=") and ";
			}
		}
		if ($i==(sizeof($ff)-1)) 
		{
			$sql_client_list.=")";
		} else
		{
			$sql_client_list.=" or ";
		}
	}
}

if ($field_nn!="")
{
	$sql_client_list.="	and ( a.lastname LIKE '".strtoupper($field_nn)."')";
}

if ($field_vn!="")
{
	$sql_client_list.="	and ( a.firstname LIKE '".$field_vn."')";
}

/* Ermittlung der Anzahl an Ergebnissen */
$rs_client_list = getrs($sql_client_list);
$sum=$rs_client_list -> num_rows;

if ($sum>=50) {$no_all = "50 von ";} else {$no_all = $sum." von ";} 

if ($pagesize == 100000)
{
    $no_all = "";
}
?>

<HTML>
<HEAD>
	<link rel="stylesheet" href="../css/ta.css">
</HEAD>

<BODY>
<!--	<?print($strConn_Database."-".$_SESSION["used_db"]."-".$new_connection."-".$_SESSION["string"]."-".$_SESSION["require"]."--".$_SESSION["new_db"]);?> -->
<center>
<table border=0 cellspacing=0 cellpadding=0>
	<tr><td height=5></td></tr>
	<tr>
		<td width=200 align=center valign=top >
			<SPAN class="headline">Kunden</SPAN><br>
		</td>
	</tr>
	<tr><td height=5></td></tr>
	<tr>
		<td width=200 height=27 align=center valign=top >
			<SPAN>Anzahl der sichtbaren Datensaetze: <?echo $no_all." ". $sum?></SPAN>
			<br>
			<? if ($pagesize==100000)
			{ ?>
			    <a href="client_list.php?pagesize=50&field=<?echo $field?>&field_vn=<?echo $field_vn?>&field_nn=<?echo $field_nn?>">geteilt anzeigen</a>
			<?} else { ?>
			    <a href="client_list.php?pagesize=100000&field=<?echo $field?>&field_vn=<?echo $field_vn?>&field_nn=<?echo $field_nn?>">alle anzeigen</a>
			<?}
			?>
		</td>
	</tr>
</table>

<BR>

<form action="<? echo $PHP_SELF ?>" method="post" name="kunden">
	Suchbegriff: <input type="text" name="field" value="<?echo $field?>"> &nbsp;&nbsp;&nbsp;
	Vorname: <input type="text" name="field_vn" value="<?echo $field_vn?>"> &nbsp;&nbsp;&nbsp;
	Nachname: <input type="text" name="field_nn" value="<?echo $field_nn?>"> &nbsp;&nbsp;&nbsp;
	<input type="button" onclick="javascript:window.document.kunden.submit()" name="search_button" value="Suchen">
	<br>

(% als Platzhalter; | als UND; , als ODER) 
</form>

<?
echo "<a target='_blank' href='client_form.php'ONMOUSEOVER=\"window.status='Neuer Eintrag'; return true;\" ONMOUSEOUT=\"window.status='';return true\"><img src='../images/buttons/neuereintrag.gif' border=no></a><br><br>";

HtmlTableFromSQLGlobalsInit(3);

$links[0] = new stdClass();
$colformat[4] = new stdClass();
$links[0]->url = "../admin/client_form.php?id=";
$links[0]->column = "ID";
$links[0]->param = "";
$links[0]->target = "_blank";
$tableparam = "&field=$field&field_vn=$field_vn&field_nn=$field_nn";
$colformat[4]->nosortlink=true;

HtmlTableFromSQL ($sql_client_list, $field, $links, $sortcol, true, $tableparam, $rowpos, $pagesize);
?>

<br>
<br>
<? echo session_id()."-".$_SESSION['session_diff_time'];?>
</CENTER>

</BODY>
</HTML>

<?
require_once("../include/session.php");
require_once("../include/autotable.php");
/* Dateiname: client_list.php
*  Zweck: ￿ersichtsliste ￿ie Kunden mit Filter- und Sortierfunktion
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

/* Suchfelder: field=allgemeines Suchfeld */
if (!empty($_POST["field"])) {
	$field = $_POST["field"];
} else {
	$field = $_GET["field"];
}


$sql_payment_list  = "SELECT     a.rechnung_id,
				   				   a.rechnung_id				AS 'ID',
				                   b.lastname   	AS 'Nachname',
				                   b.firstname   	AS 'Vorname',
								   CONCAT(c.products_id,c.year,c.timeperiods_id,c.institutions_id,c.locations_id,'-',c.info,'<br>(',d.name,'/',c.year,'/',g.name,'/',e.name,'/',f.name,')')	AS 'Code',
				                   a.billdate       AS 'Zahlungs-Datum',
				                   sum(a.amount)       	AS 'Betrag',
				                   a.remarks      	AS 'Bemerkung',
				                   a.status      	AS 'Forderung/Ein-Ausgang'
				         FROM
				                   payments a,
				                   clients b,
								   courses c,
								   products d,
								   institutions e,
								   institutions f,
								   timeperiods g
				         WHERE
				                   a.status in ('E','A','F') and c.status<>'Entfernt' and b.status<>'Entfernt' and
				                   a.courses_id = c.id and a.clients_id=b.id and c.products_id=d.id and e.id=c.institutions_id and c.locations_id=f.id and c.timeperiods_id=g.id";

if ($field!="") 
{
	$sql_payment_list.=" and (";
	$ff=explode(",",$field);
	for ($i=0;$i<sizeof($ff);$i++)
	{
		$ff1=explode("|",$ff[$i]);
		for ($ii=0;$ii<sizeof($ff1);$ii++)
		{
			$sql_payment_list.="(b.lastname LIKE '".$ff1[$ii]."%' 
				   or b.firstname LIKE '".$ff1[$ii]."%'
				   or a.billdate LIKE '".$ff1[$ii]."%'
				   or a.amount LIKE '".$ff1[$ii]."%'
				   or a.id LIKE '".$ff1[$ii]."%'
				   or a.remarks LIKE '".$ff1[$ii]."%'
				   or d.name LIKE '".$ff1[$ii]."%'
				   or c.year LIKE '".$ff1[$ii]."%'
				   or e.name LIKE '".$ff1[$ii]."%'
				   or f.name LIKE '".$ff1[$ii]."%'
				   or g.name LIKE '".$ff1[$ii]."%'
				   or c.info LIKE '".$ff1[$ii]."%'
				   or CONCAT(c.products_id,c.year,c.timeperiods_id,c.institutions_id,c.locations_id,'-',c.info) LIKE '".$ff1[$ii]."%'
				   or a.status LIKE '".$ff1[$ii]."%'";
			if ($ii==(sizeof($ff1)-1)) 
			{
				$sql_payment_list.=")";
			} else
			{
				$sql_payment_list.=") and ";
			}
		}
		if ($i==(sizeof($ff)-1)) 
		{
			$sql_payment_list.=")";
		} else
		{
			$sql_payment_list.=" or ";
		}
	}
}

$sql_payment_list.= " GROUP BY a.rechnung_id ";

/* Ermittlung der Anzahl an Ergebnissen */
$rs_payment_list = getrs($sql_payment_list);
$sum=$rs_payment_list -> num_rows;

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
	<center>
	<table border=0 cellspacing=0 cellpadding=0>
		<tr><td height=5></td></tr>
		<tr><td width=200 align=center valign=top >
			<SPAN class="headline">Zahlungen</SPAN><br>
		</td></tr>
		<tr><td height=5></td></tr>
		<tr><td width=200 height=27 align=center valign=top >
			<SPAN>Anzahl der sichtbaren Datens￿e: <?echo $no_all." ". $sum?></SPAN>
			<br>
<? if ($pagesize==100000)
{ ?>
		    <a href="payment_list.php?pagesize=50">geteilt anzeigen</a>
<?} else { ?>
    		<a href="payment_list.php?pagesize=100000">alle anzeigen</a>
<?}
?>
		</td></tr>
	</table>
	<BR>

	<form action="<? echo $PHP_SELF ?>" method="post" name="kunden">
	Suchbegriff: <input type="text" name="field" onchange="javascript:window.document.kunden.submit()" value="<?echo $field?>"><br>
	<input type="button" onclick="javascript:window.document.kunden.submit()" name="search_button" value="Suchen">
	<br>
	(% als Platzhalter; | als UND; , als ODER) 
	</form>

<?
echo "<a target='_blank' href='payment_form.php'ONMOUSEOVER=\"window.status='Neuer Eintrag'; return true;\" ONMOUSEOUT=\"window.status='';return true\"><img src='../images/buttons/neuereintrag.gif' border=no></a><br><br>";

HtmlTableFromSQLGlobalsInit(-2);
$links[0] = new stdClass();
$colformat[4] = new stdClass();
$links[0]->url = "../admin/payment_form.php?id=";
$links[0]->column = "ID";
$links[0]->param = "";
$links[0]->target = "_blank";

$tableparam = "";
$colformat[4]->nosortlink=true;
HtmlTableFromSQL ($sql_payment_list, $field, $links, $sortcol, true,$tableparam,$rowpos,$pagesize);
?>
	</CENTER>
</BODY>
</HTML>

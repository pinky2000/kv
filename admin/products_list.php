<?
require_once("../include/session.php");
require_once("../include/autotable.php");

/* Dateiname: products_list.php
*  Zweck: Übersichtsliste über die Produkte mit Filter- und Sortierfunktion
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];

isAllow(isAdmin() || isSecretary());

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

$sql_products_list  = "SELECT  id,
			   				   id					AS 'ID',
			                   name    			    AS 'Produktname',
			                   standard_hourcost   	AS 'Standardstundensatz',
			                   status   			AS 'Status'
			         FROM
			                   products
			         WHERE
			                   ((status='Aktiv') or (status='Inaktiv'))";

if ($field!="") 
{
	$sql_products_list.=" and (";
	$ff=explode(",",$field);
	for ($i=0;$i<sizeof($ff);$i++)
	{
		$ff1=explode("|",$ff[$i]);
		for ($ii=0;$ii<sizeof($ff1);$ii++)
		{
			$sql_products_list.="(name LIKE '".$ff1[$ii]."%' 
				   or id LIKE '".$ff1[$ii]."%'
				   or standard_hourcost LIKE '".$ff1[$ii]."%'
				   or status LIKE '".$ff1[$ii]."%'";
			if ($ii==(sizeof($ff1)-1)) 
			{
				$sql_products_list.=")";
			} else
			{

				$sql_products_list.=") and ";
			}
		}
		if ($i==(sizeof($ff)-1)) 
		{
			$sql_products_list.=")";
		} else
		{
			$sql_products_list.=" or ";
		}
	}
}

$rs_products_list = getrs($sql_products_list,$print_debug,$print_error);

$sum=$rs_products_list -> num_rows;

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
			<SPAN class="headline">Produkte</SPAN><br>
		</td></tr>
		<tr><td height=5></td></tr>
		<tr><td width=200 height=27 align=center valign=top >
			<SPAN>Anzahl der sichtbaren Datensätze: <?echo $no_all." ". $sum?></SPAN>
			<br>	
<? if ($pagesize==100000)
{ ?>
		    <a href="products_list.php?pagesize=50">geteilt anzeigen</a>
<?} else { ?>
    		<a href="products_list.php?pagesize=100000">alle anzeigen</a>
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
echo "<a target=_blank href='products_form.php'ONMOUSEOVER=\"window.status='Neuer Eintrag'; return true;\" ONMOUSEOUT=\"window.status='';return true\"><img src='../images/buttons/neuereintrag.gif' border=no></a><br><br>";

HtmlTableFromSQLGlobalsInit(3);

$links[0] = new stdClass();
$colformat[4] = new stdClass();
$links[0]->url = "../admin/products_form.php?id=";
$links[0]->column = "ID";
$links[0]->param = "";
$links[0]->target = "_blank";
$tableparam = "";
$colformat[4]->nosortlink=true;

HtmlTableFromSQL ($sql_products_list, $field, $links, $sortcol, true,$tableparam,$rowpos,$pagesize);
?>
	</CENTER>
</BODY>

</HTML>
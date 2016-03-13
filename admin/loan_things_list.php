<?
require_once("../include/session.php");
require_once("../include/autotable.php");
isAllow(isAdmin() || isSecretary());

/* Dateiname: loan_things_list.php
*  Zweck: Übersichtsliste über die ausgeborgten Sportgeräte
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];

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

$sql_loan_select = "SELECT
                  a.employees_id,
				  a.employees_id AS 'ID',
                  b.lastname     AS 'Nachname',
                  b.firstname    AS 'Vorname',
				  c.name         AS 'Sportgeräte',
                  count(c.name)  AS 'Anzahl ausgeborgter Geräte'
         FROM
                 loan_objects a, employees b, objects c
         WHERE
                  b.status in ('Aktiv','Inaktiv') and
                  a.employees_id=b.id and
                  a.name_id=c.id and a.status='Nein'";

if ($field!="") 
{
	$sql_loan_select.=" and (";
	$ff=explode(",",$field);
	for ($i=0;$i<sizeof($ff);$i++)
	{
		$ff1=explode("|",$ff[$i]);
		for ($ii=0;$ii<sizeof($ff1);$ii++)
		{
			$sql_loan_select.="(a.id LIKE '".$ff1[$ii]."%' 
						or b.lastname LIKE '".$ff1[$ii]."%' 
						or b.firstname LIKE '".$ff1[$ii]."%'
						or a.pieces LIKE '".$ff1[$ii]."%'
						or c.name LIKE '".$ff1[$ii]."%'
						or a.end LIKE '".$ff1[$ii]."%'
						or a.status LIKE '".$ff1[$ii]."%'
						or a.begin LIKE '".$ff1[$ii]."%'";
			if ($ii==(sizeof($ff1)-1)) 
			{
				$sql_loan_select.=")";
			} else
			{
				$sql_loan_select.=") and ";
			}
		}
		if ($i==(sizeof($ff)-1)) 
		{
			$sql_loan_select.=")";
		} else
		{
			$sql_loan_select.=" or ";
		}
	}
}

$sql_loan_select.=" group by b.lastname,b.firstname";

$rs_loan_select = getrs($sql_loan_select,$print_debug,$print_error);

$sum=$rs_loan_select -> num_rows;

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
<tr><td height=12></td></tr>
<tr><td width=200 height=27 align=center valign=top>
<SPAN class="headline">Geräteverleih</SPAN><br>
</td></tr>
<tr><td height=5></td></tr>
<tr><td width=200 height=27 align=center valign=top >
		<SPAN>Anzahl der sichtbaren Datensätze: <?echo $no_all." ". $sum?></SPAN>
</td></tr>
</table>
<BR>
<form action="<? echo $PHP_SELF ?>" method="post" name="kunden">
	Suchbegriff: <input type="text" name="field" value="<?echo $field?>"><br>
	<input type="button" onclick="javascript:window.document.kunden.submit()" name="search_button" value="Suchen">
	<br>
	(% als Platzhalter; | als UND; , als ODER) 
</form>
<br>
<?
echo "<br><a target='_blank' href='loan_things_form.php'ONMOUSEOVER=\"window.status='Neuer Eintrag'; return true;\" ONMOUSEOUT=\"window.status='';return true\"><img src='../images/buttons/neuereintrag.gif' border=no></a>";
?>
<BR><BR>
<?
HtmlTableFromSQLGlobalsInit(3);

$links[0] = new stdClass();
$colformat[4] = new stdClass();

$links[0]->url = "../admin/loan_things_form.php?id=";
$links[0]->column = "ID";
$links[0]->param = "";
$links[0]->target = "_blank";
$tableparam = "";
$colformat[4]->nosortlink=true;
HtmlTableFromSQL ($sql_loan_select, $field, $links, $sortcol, true,$tableparam,$rowpos,$pagesize,$field);
?>
</CENTER>
</BODY>
</HTML>
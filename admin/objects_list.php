<?
require_once("../include/session.php");
require_once("../include/autotable.php");
isAllow(isAdmin() || isSecretary());

/* Dateiname: objects_list.php
*  Zweck: Übersichtsliste über die Sportgeräte
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
?>

<?
      $sql_objects_select  = "SELECT
                  a.id,
				  a.id			AS 'ID',
                  a.name        AS 'Gerät',
                  a.pieces      AS 'Stand',
                  a.available   AS 'Verfügbar',
                  a.loan        AS 'Verliehen',
                  a.description AS 'Beschreibung'
         FROM
                 objects a 
		 WHERE
		 		 a.status in ('Aktiv','Inaktiv') ";

if ($field!="") 
{
	$sql_objects_select.=" and (";
	$ff=explode(",",$field);
	for ($i=0;$i<sizeof($ff);$i++)
	{
		$ff1=explode("|",$ff[$i]);
		for ($ii=0;$ii<sizeof($ff1);$ii++)
		{
			$sql_objects_select=$sql_objects_select."(a.id LIKE '".$ff1[$ii]."%' 
						or a.name LIKE '".$ff1[$ii]."%' 
						or a.pieces LIKE '".$ff1[$ii]."%'
						or a.available LIKE '".$ff1[$ii]."%'
						or a.loan LIKE '".$ff1[$ii]."%'
						or a.description LIKE '".$ff1[$ii]."%'";
			if ($ii==(sizeof($ff1)-1)) 
			{
				$sql_objects_select.=")";
			} else
			{
				$sql_objects_select.=") and ";
			}
		}
		if ($i==(sizeof($ff)-1)) 
		{
			$sql_objects_select.=")";
		} else
		{
			$sql_objects_select.=" or ";
		}
	}
}

$rs_objects_select = getrs($sql_objects_select,$print_debug,$print_error);

$sum=$rs_objects_select -> num_rows;

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
		<SPAN class="headline">Geräteinventar</SPAN><br>
	</td></tr>
	<tr><td width=200 height=27 align=center valign=top >
		<SPAN>Anzahl der sichtbaren Datensätze: <?echo $no_all." ". $sum?></SPAN>
		<br>
<? if ($pagesize==100000)
{ ?>
		<a href="objects_list.php?pagesize=50">geteilt anzeigen</a>
<?} else { ?>
    	<a href="objects_list.php?pagesize=100000">alle anzeigen</a>
<?}
?>
	</td></tr>
</table>

<BR>
<form action="<? echo $PHP_SELF ?>" method="post" name="kunden">
	Suchbegriff: <input type="text" name="field" value="<?echo $field?>"><br>
	<input type="button" onclick="javascript:window.document.kunden.submit()" name="search_button" value="Suchen">
	<br>
	(% als Platzhalter; | als UND; , als ODER) 
</form>

<?echo "<br><a target=_blank href='objects_form.php'ONMOUSEOVER=\"window.status='Neuer Eintrag'; return true;\" ONMOUSEOUT=\"window.status='';return true\"><img src='../images/buttons/neuereintrag.gif' border=no></a>";
?>
<BR><BR>
<?
HtmlTableFromSQLGlobalsInit(3);

$links[0] = new stdClass();
$colformat[4] = new stdClass();
$links[0]->url = "../admin/objects_form.php?id=";
$links[0]->column = "ID";
$links[0]->param = "";
$links[0]->target = "_blank";
$tableparam = "";
$colformat[4]->nosortlink=true;
HtmlTableFromSQL ($sql_objects_select, $field, $links, $sortcol, true,$tableparam,$rowpos,$pagesize);
?>

<br><br>
</CENTER>
</BODY>
</HTML>
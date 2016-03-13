<?
require_once("../include/session.php");
require_once("../include/autotable.php");
isAllow(isAdmin()   || isSecretary());

/* Dateiname: webregister_list.php
*  Zweck: Übersichtsliste der Online-Registrierungen für die Camps mit Filter- und Sortierfunktion
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

$mode=$_GET["mode"];

/* Suchfelder: field=allgemeines Suchfeld */
if (!empty($_POST["field"])) {
	$field = $_POST["field"];
} else {
	$field = $_GET["field"];
}

if (!isset($mode)) $mode=1;
	
if ($mode=='1')  // neue Anmeldungen
	{
		$abfrage="g.status='Neu'";
		$text="Neue Anmeldungen";
	}	
elseif ($mode=='2') // bearbeitete Anmeldungen
	{
		$abfrage="g.status='Bearbeitet'";
		$text="Bearbeitete Anmeldungen";
	}	
elseif ($mode=='3') // Storno Anmeldungen
	{
		$abfrage="g.status='Storno'";
		$text="Stornierte Anmeldungen";
	}	


$sql_course_select = "select g.id,
			g.id 				AS 'ID',
			g.create_date		AS 'Anmeldedatum',
			h.firstname			AS 'Vorname',
			h.lastname			AS 'Nachname',	
			h.birthdate			AS 'Geburtstag',
			CONCAT(b.name,'/',a.year,'/',c.name,'/',d.name,'-',a.info)	AS 'Camp'
			
		from 
			courses a,
			products b,
			timeperiods c,
			institutions d,
			web_camp g,
			web_clients h
		where
			g.web_clients_id=h.id
            and g.courses_id=a.id
			and	g.id > 0 
			and a.products_id=b.id
			and	a.timeperiods_id=c.id and c.camp='1'
			and a.locations_id=d.id
			and g.web_clients_id=h.id 
			and a.status='Aktiv' 
			and $abfrage";
			
if ($field!="") 
{
	$sql_course_select.=" and (";
	$ff=explode(",",$field);
	for ($i=0;$i<sizeof($ff);$i++)
	{
		$ff1=explode("|",$ff[$i]);
		for ($ii=0;$ii<sizeof($ff1);$ii++)
		{
			$sql_course_select.="(g.id = '".$ff1[$ii]."' 
				   or h.firstname LIKE '".$ff1[$ii]."%'
				   or h.lastname LIKE '".$ff1[$ii]."%'
				   or a.year LIKE '".$ff1[$ii]."%'
				   or c.name LIKE '".$ff1[$ii]."%'
				   or d.name LIKE '".$ff1[$ii]."%'
				   or h.birthdate LIKE '".$ff1[$ii]."%'";
			if ($ii==(sizeof($ff1)-1)) 
			{
				$sql_course_select.=")";
			} else
			{
				$sql_course_select.=") and ";
			}
		}
		if ($i==(sizeof($ff)-1)) 
		{
			$sql_course_select.=")";
		} else
		{
			$sql_course_select.=" or ";
		}
	}
}

$rs_course_select = getrs($sql_course_select,$print_debug,$print_error);

$sum=$rs_course_select -> num_rows;

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
	<tr><td width=200 align=center valign=top>
		<SPAN class="headline">Camp - Online-Anmeldungen-&Uuml;bersicht <br><br> <?print($text)?></SPAN><br>
	</td></tr>
	<tr><td height=5></td></tr>
	<tr><td width=200 height=27 align=center valign=top >
		<SPAN>Anzahl der sichtbaren Datens&auml;tze: <?echo $no_all." ". $sum?></SPAN>
		<br>
<? if ($pagesize==100000)
{ ?>
    	<a href="webregister_list.php?mode=<?echo $mode?>&pagesize=50&field=<?echo $field?>">geteilt anzeigen</a>
<?} else { ?>
	    <a href="webregister_list.php?mode=<?echo $mode?>&pagesize=100000&field=<?echo $field?>">alle anzeigen</a>
<?}
?>
	</td></tr>
</table>
<BR>
<form action="<? echo $PHP_SELF ?>" method="post" name="kurs">

	Suchbegriff: <input type="text" name="field" value="<?echo $field?>">
	<input type="button" onclick="javascript:window.document.kurs.submit()" name="search_button" value="Suchen">
	<br>
	(% als Platzhalter; | als UND; , als ODER) 

</form>

<br>

<?

HtmlTableFromSQLGlobalsInit(-3);

$links[0] = new stdClass();
$colformat[1] = new stdClass();

$links[0]->url = "../admin/webregister_form.php?id=";
$links[0]->column = "ID";
$links[0]->param = "";
$links[0]->target = "_blank";
$tableparam = "&mode=".$mode;
$colformat[1]->nosortlink=true;

HtmlTableFromSQL ($sql_course_select, $field, $links, $sortcol, true,$tableparam,$rowpos,$pagesize);
?>

<br>
<a href="<? echo $PHP_SELF ?>?mode=2">Anzeige der bearbeiteten Anmeldungen</a>
<br>
<a href="<? echo $PHP_SELF ?>?mode=3">Anzeige der stornierten Anmeldungen</a>


</CENTER>
<br>
<br>
</BODY>

</HTML>

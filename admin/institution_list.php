<?
require_once("../include/session.php");
require_once("../include/autotable.php");

isAllow(isAdmin()   || isSecretary());

/* Dateiname: institution_list.php
*  Zweck: Übersichtsliste über die Schulen und Institutionen mit Filter- und Sortierfunktion
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

$sql_inst_select  = "SELECT
                  a.id,
				  a.id			  AS 'ID',
                  a.name          AS 'Institut',
                  a.contactperson AS 'Ansprechpartner',
                  a.address       AS 'Adresse',
                  a.zip           AS 'Plz',
                  a.city          AS 'City',
                  a.phone1        AS 'Telefon',
                  a.email        AS 'Email',
                  a.link        AS 'Homepage',
                  a.status        AS 'Status'
         FROM
                 institutions a
         WHERE
                  a.status in ('Aktiv','Inaktiv')
		 AND
		 		  a.id > 0";

if ($field!="") 
{
	$sql_inst_select.=" and (";
	$ff=explode(",",$field);
	for ($i=0;$i<sizeof($ff);$i++)
	{
		$ff1=explode("|",$ff[$i]);
		for ($ii=0;$ii<sizeof($ff1);$ii++)
		{
			$sql_inst_select.=" (a.name LIKE '".$ff1[$ii]."%' 
								or a.contactperson LIKE '".$ff1[$ii]."%'
								or a.address LIKE '".$ff1[$ii]."%'
								or a.zip LIKE '".$ff1[$ii]."%'
								or a.city LIKE '".$ff1[$ii]."%'
								or a.phone1 LIKE '".$ff1[$ii]."%'
								or a.email LIKE '".$ff1[$ii]."%'
								or a.link LIKE '".$ff1[$ii]."%'
								or a.status LIKE '".$ff1[$ii]."%'";
			if ($ii==(sizeof($ff1)-1)) 
			{
				$sql_inst_select.=")";
			} else
			{
				$sql_inst_select.=") and ";
			}
		}
		if ($i==(sizeof($ff)-1)) 
		{
			$sql_inst_select.=")";
		} else
		{
			$sql_inst_select.=" or ";
		}
	}
}

$rs_inst_select = getrs($sql_inst_select,$print_debug,$print_error);
$sum=$rs_inst_select -> num_rows;

$no_all = "50 von ";
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
		<SPAN class="headline">Institutionen</SPAN><br>
	</td></tr>
	<tr><td height=5></td></tr>
	<tr><td width=200 height=27 align=center valign=top >
		<SPAN>Anzahl der sichtbaren Datensätze: <?echo $no_all." ". $sum?></SPAN>
		<br>
<? if ($pagesize==100000)
{ ?>
	    <a href="institution_list.php?pagesize=50&field=<?echo $field?>">geteilt anzeigen</a>
<?} else { ?>
	    <a href="institution_list.php?pagesize=100000&field=<?echo $field?>">alle anzeigen</a>
<?}
?>
	</td></tr>
</table>

<BR>

<form action="<? echo $PHP_SELF ?>" method="post" name="institution">
	Suchbegriff: <input type="text" name="field" value="<?echo $field?>">
	<input type="button" onclick="javascript:window.document.institution.submit()" name="search_button" value="Suchen">
	<br>
	(% als Platzhalter; | als UND; , als ODER)
	 
</form>

<a target='_blank' href='institution_form.php'ONMOUSEOVER=\"window.status='Neuer Eintrag'; return true;\" ONMOUSEOUT=\"window.status='';return true\"><img src='../images/buttons/neuereintrag.gif' border=no></a><br><br>
<?
HtmlTableFromSQLGlobalsInit(3);

$links[0] = new stdClass();
$colformat[4] = new stdClass();
$links[0]->url = "../admin/institution_form.php?id=";
$links[0]->column = "ID";
$links[0]->param = "";
$links[0]->target = "_blank";

$tableparam = "";

$colformat[4]->nosortlink=true;

HtmlTableFromSQL ($sql_inst_select, $field, $links, $sortcol, true,$tableparam,$rowpos,$pagesize);
?>
</CENTER>
<? echo session_id()."-".$_SESSION['session_diff_time'];?>
</BODY>

</HTML>

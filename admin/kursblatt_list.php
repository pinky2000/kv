<?
require_once("../include/session.php");
require_once("../include/autotable.php");
isAllow(isAdmin() || isSecretary() || isEmployee());

/* Dateiname: kursblatt_list.php
*  Zweck: ￿ersichtsliste ￿ie Kursbl￿er mit Filter- und Sortierfunktion
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

if ((isAdmin()) || (isSecretary()))
{
	$sql_kursblatt_select = "select
                        a.id                AS 'ID',
                        CONCAT(a.products_id,a.year,a.timeperiods_id,a.institutions_id,a.locations_id,'-',a.info) AS 'Kurs',
                        b.name                                AS 'Produkt',
                        a.year                                AS 'Jahr',
                        c.name                                AS 'Zeitperiode',
                        d.name                                AS 'Institution',
                        e.name                                AS 'Ort',
                        a.info                                AS 'Information',
                        a.price                         AS 'Preis'
                from
                        courses a,
                        products b,
                        timeperiods c,
                        institutions d,
                        institutions e
                where
                        a.products_id=b.id
                and
                        a.timeperiods_id=c.id
                and
                        a.institutions_id=d.id
                and
                        a.locations_id=e.id
                and
                        a.status='Aktiv'";

	if ($field!="")
	{
        $sql_kursblatt_select.=" and (";
        $ff=explode(",",$field);
        for ($i=0;$i<sizeof($ff);$i++)
        {
                $ff1=explode("|",$ff[$i]);
                for ($ii=0;$ii<sizeof($ff1);$ii++)
                {
                        $sql_kursblatt_select.="(a.id LIKE '".$ff1[$ii]."%'
                                   or b.name LIKE '".$ff1[$ii]."%'
                                   or a.year LIKE '".$ff1[$ii]."%'
                                   or c.name LIKE '".$ff1[$ii]."%'
                                   or d.name LIKE '".$ff1[$ii]."%'
                                   or a.info LIKE '".$ff1[$ii]."%'
                                   or a.price LIKE '".$ff1[$ii]."%'
                                   or CONCAT(a.products_id,a.year,a.timeperiods_id,a.institutions_id,a.locations_id,'-',a.info) LIKE '".$ff1[$ii]."%'
                                   or e.name LIKE '".$ff1[$ii]."%'";
                        if ($ii==(sizeof($ff1)-1))
                        {
                                $sql_kursblatt_select.=")";
                        } else
                        {
                                $sql_kursblatt_select.=") and ";
                        }
                }
                if ($i==(sizeof($ff)-1))
                {
                        $sql_kursblatt_select.=")";
                } else
                {
                        $sql_kursblatt_select.=" or ";
                }
        }
	}
}	

// Mitarbeiter sehen nur "Ihre" Kurse, wo sie als Mitarbeiter eingetragen sind. Da der 4.Kursleiter extra behandelt wird, wird dieser ￿ie UNION Funktion eingebunden
if (isEmployee())
{
   $base_year = idate("Y")-2;
	$sql_kursblatt_select = "(SELECT
                        a.id as 'ID',
                        CONCAT(a.products_id,a.year,a.timeperiods_id,a.institutions_id,a.locations_id,'-',a.info) as 'Kurs',
                        e.name                                AS 'Produkt',
                        a.year                                AS 'Jahr',
                        f.name                                AS 'Zeitperiode',
                        g.name                                AS 'Institution',
                        h.name                                AS 'Ort',
                        a.info                                AS 'Information',
                        d.lastname                         as 'Mitarbeiter'
                FROM
                        courses a,
                        coursetimes b,
                        coursetimes_employees c,
                        employees d,
                        products e,
                        timeperiods f,
                        institutions g,
                        institutions h
                WHERE
                        a.status='Aktiv'
                        and a.id = b.courses_id
                        and d.status in ('Aktiv','Neu')
						and b.id = c.coursetimes_id
						and c.employees_id = d.id
						and c.employees_id = '".$_SESSION['userid']."'
                        and a.products_id = e.id
                        and a.timeperiods_id = f.id
                        and a.institutions_id = g.id
                        and a.locations_id = h.id
                        and a.year > ".$base_year." 
                GROUP BY a.id  order by courses.year desc, f.id asc)
	UNION
	(SELECT
                        a.id as 'ID',
                        CONCAT(a.products_id,a.year,a.timeperiods_id,a.institutions_id,a.locations_id,'-',a.info) as 'Kurs',
                        e.name                                AS 'Produkt',
                        a.year                                AS 'Jahr',
                        f.name                                AS 'Zeitperiode',
                        g.name                                AS 'Institution',
                        h.name                                AS 'Ort',
                        a.info                                AS 'Information',
                        d.lastname                         as 'Mitarbeiter'
                FROM
                        courses a,
                        coursetimes b,
                        employees d,
                        products e,
                        timeperiods f,
                        institutions g,
                        institutions h
                WHERE
                        a.status='Aktiv'
                        and a.id = b.courses_id
                        and d.status in ('Aktiv','Neu')
                        and b.employee4_id = '".$_SESSION['userid']."'
                        and b.employee4_id = d.id
                        and a.products_id = e.id
                        and a.timeperiods_id = f.id
                        and a.institutions_id = g.id
                        and a.locations_id = h.id
                        and a.year > ".$base_year." 
	GROUP BY a.id order by courses.year desc, f.id asc)";
}

$rs_kursblatt_select = getrs($sql_kursblatt_select,$print_debug,$print_error);

$sum=$rs_kursblatt_select -> num_rows;

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
		<SPAN class="headline">Kursbl&auml;tter</SPAN><br>
	</td></tr>
	<tr><td height=5></td></tr>
	<tr><td width=200 height=27 align=center valign=top >
<?
if (isEmployee())
{ ?>
	<SPAN><b>Hinweis:</b> Es sind nur Kurse nach <?echo $base_year?> sichtbar!</SPAN><br>
<? } ?>
	<SPAN>Anzahl der sichtbaren Datens&auml;tze: <?echo $no_all." ". $sum?></SPAN>
		<br>
<? if ($pagesize==100000)
{ ?>
	    <a href="kursblatt_list.php?pagesize=50">geteilt anzeigen</a>
<?} else { ?>
    	<a href="kursblatt_list.php?pagesize=100000">alle anzeigen</a>
<?}
?>

	</td></tr>
</table>

<BR>

<form action="<? echo $PHP_SELF ?>" method="post" name="kunden">
	Suchbegriff: <input type="text" name="field" value="<?echo $field?>">
	<input type="button" onclick="javascript:window.document.kurs.submit()" name="search_button" value="Suchen">
	<br>
	(% als Platzhalter; | als UND; , als ODER) 

</form>

<br>
<?

HtmlTableFromSQLGlobalsInit(-4);

$links[0] = new stdClass();
$colformat[4] = new stdClass();
$links[0]->url = "../admin/kursblatt_form.php?id=";
$links[0]->column = "Kurs";
$links[0]->param = "";
$links[0]->target = "_blank";
$tableparam = "";
$colformat[4]->nosortlink=true;

HtmlTableFromSQL ($sql_kursblatt_select, $field, $links, $sortcol, true,$tableparam,$rowpos,$pagesize);

if ((isAdmin()) || (isSecretary()))
{
	echo "<br><a target=_blank href='kursblatt_form.php' ONMOUSEOVER=\"window.status='Neuer Eintrag'; return true;\" ONMOUSEOUT=\"window.status='';return true\"><img src='../images/buttons/neuereintrag.gif' border=no></a>";
}

?>
</CENTER>
</BODY>

</HTML>

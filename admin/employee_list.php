<?
require_once("../include/session.php");
require_once("../include/autotable.php");

/* Dateiname: employee_list.php
*  Zweck: Übersichtsliste über die Mitarbeiter mit Filter- und Sortierfunktion
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
  $sql_employee_select  = "SELECT
                  a.id,
				  a.id			  AS 'ID',
                  a.lastname    AS 'Nachname',
                  a.firstname   AS 'Vorname',
                  a.phone1   	AS 'Telefon1',
                  a.phone2      AS 'Telefon2',
                  a.email      AS 'Email',
                  a.zip         AS 'Plz',
                  a.city        AS 'Ort',
                  b.name        AS 'Rolle',
                  a.status  AS 'Status'
         FROM
                 employees a, roles b
         WHERE
                  a.status in ('Aktiv','Inaktiv') and
                  a.roles_id=b.id and a.id >0";

if ($field!="")
{
	$sql_employee_select.=" and (";
	$ff=explode(",",$field);
	for ($i=0;$i<sizeof($ff);$i++)
	{
			$ff1=explode("|",$ff[$i]);
			for ($ii=0;$ii<sizeof($ff1);$ii++)
			{
				$sql_employee_select.="(a.lastname LIKE '".$ff1[$ii]."%'
										or a.firstname LIKE '".$ff1[$ii]."%'
										or a.sv_number LIKE '".$ff1[$ii]."%'
										or a.phone1 LIKE '".$ff1[$ii]."%'
										or a.email LIKE '".$ff1[$ii]."%'
										or a.zip LIKE '".$ff1[$ii]."%'
										or a.city LIKE '".$ff1[$ii]."%'
										or b.name LIKE '".$ff1[$ii]."%'
										or a.address LIKE '".$ff1[$ii]."%'
										or a.status LIKE '".$ff1[$ii]."%'";
				if ($ii==(sizeof($ff1)-1))
				{
					$sql_employee_select.=")";
				} else
				{
					$sql_employee_select.=") and ";
				}
			}
		if ($i==(sizeof($ff)-1))
		{
			$sql_employee_select.=")";
		} else
		{
			$sql_employee_select.=" or ";
		}
	}
}
else if ($field_nn!="")
{
	$sql_employee_select.="	and ( a.lastname LIKE '".strtoupper($field_nn)."')";
}
else if ($field_vn!="")
{
	$sql_employee_select.="	and ( a.firstname LIKE '".$field_vn."')";
}

$rs_employee_select = getrs($sql_employee_select,$print_debug,$print_error);

$sum=$rs_employee_select -> num_rows;

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
<?
if ($print_debug) 
{
	echo "Diff: ".$_SESSION['session_diff_time']."<br>";
	echo "Login: ".$_SESSION['session_lastedit_time']."<br>";
}
?>		
		
	<table border=0 cellspacing=0 cellpadding=0>
		<tr><td height=5></td></tr>
		<tr><td width=200 align=center valign=top>
			<SPAN class="headline">Mitarbeiter</SPAN><br>
		</td></tr>
		<tr><td height=5></td></tr>
		<tr><td width=200 height=27 align=center valign=top >
			<SPAN>Anzahl der sichtbaren Datensätze: <?echo $no_all." ". $sum?></SPAN>
			<br>
<? if ($pagesize==100000)
{ ?>
			<a href="employee_list.php?pagesize=50&field=<?echo $field?>&field_vn=<?echo $field_vn?>&field_nn=<?echo $field_nn?>">geteilt anzeigen</a>
<?} else { ?>
    		<a href="employee_list.php?pagesize=100000&field=<?echo $field?>&field_vn=<?echo $field_vn?>&field_nn=<?echo $field_nn?>">alle anzeigen</a>
<?}?>
		</td></tr>
	</table>
	<BR>
	
	<form action="<? echo $PHP_SELF ?>" method="post" name="mitarbeiter">
		Suchbegriff: <input type="text" name="field" value="<?echo $field?>"> &nbsp;&nbsp;&nbsp;
		Vorname: <input type="text" name="field_vn" value="<?echo $field_vn?>"> &nbsp;&nbsp;&nbsp;
		Nachname: <input type="text" name="field_nn" value="<?echo $field_nn?>"> &nbsp;&nbsp;&nbsp;
		<input type="button" onclick="javascript:window.document.mitarbeiter.submit()" name="search_button" value="Suchen">
		<br>
		(% als Platzhalter; | als UND; , als ODER)
	</form>

	<a target='_blank' href='employee_form.php'ONMOUSEOVER=\"window.status='Neuer Eintrag'; return true;\" ONMOUSEOUT=\"window.status='';return true\"><img src='../images/buttons/neuereintrag.gif' border=no></a>
	<br><br>
	<a href='../gen.php'>Benutzerkontos aktivieren</a>
	<br><br>

<?
HtmlTableFromSQLGlobalsInit(3);

$links[0] = new stdClass();
$colformat[4] = new stdClass();

$links[0]->url = "../admin/employee_form.php?id=";
$links[0]->column = "ID";
$links[0]->param = "";
$links[0]->target = "_blank";

$tableparam = "";
$colformat[4]->nosortlink=true;

HtmlTableFromSQL ($sql_employee_select ,$field, $links, $sortcol, true,$tableparam,$rowpos,$pagesize);
?>

	</CENTER>
<? echo session_id()."-".$_SESSION['session_diff_time'];?>
</BODY>
</HTML>
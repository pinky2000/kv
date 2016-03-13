<?
require_once("../include/session.php");
require_once("../include/server.php");
require_once("../include/html.php");	
require_once("../include/checkfunction.php");
$first_time=microtime();
/* Dateiname: kurs_form.php
*  Zweck: Formular zur Eingabe der Kursdaten
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
isAllow(isAdmin() || isSecretary());

/* _POST einlesen */
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$kopie = $_POST['kopie'];
$confirm = $_POST['confirm'];
$update = $_POST['update'];
$anzahl_termine = $_POST['anzahl_termine'];
$change_date = date('Y-m-d H:m:s');
$change_user = $_SESSION['username'];

/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }

$back_url="../admin/kurs_form.php?id=";
if (isset($back_x))
{
  header("Location: $back_url");
}

/* Falls Formular neu geladen werden soll, ohne Daten aus DB zu holen, dann wird update auf 1 gesetzt ! */
if (($update==1) || (isset($save_x)))
{
	$kurstermine = $_POST['kurstermine'];
	$products_id = $_POST['products_id'];
	$jahr = $_POST['jahr'];
	$time_id = $_POST['time_id'];
	$ort_id = $_POST['ort_id'];
	$auftrag_id = $_POST['auftrag_id'];
	$info = $_POST['info'];
	$preis = $_POST['preis'];
	$uhrzeit_0 = $_POST['uhrzeit_0'];
	$dauer_0 = $_POST['dauer_0'];
	$standard_mitarbeiter0 = $_POST['standard_mitarbeiter0'];
	$standard_mitarbeiter1 = $_POST['standard_mitarbeiter1'];
	$standard_mitarbeiter2 = $_POST['standard_mitarbeiter2'];
	$standard_mitarbeiter3 = $_POST['standard_mitarbeiter3'];
	$st_hc = $_POST['st_hc'];
	$minteil = $_POST['minteil'];
	$remarks = $_POST['remarks'];
	$code = $_POST['code'];
	$durance_desc = $_POST['durance_desc'];
	$tag = $_POST['tag'];
	$intern_remarks = $_POST['intern_remarks'];
	$jbetrag = $_POST['jbetrag'];
	$semrabatt = $_POST['semrabatt'];
	$jahrrabatt = $_POST['jahrrabatt'];
	$kursinfo = $_POST['kursinfo'];
	$status = $_POST['status'];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
        <title>Kursverwaltung</title>
		<link href="../css/ta.css" type="text/css" rel="stylesheet">
	
	<script src="codebase/dhtmlxcommon.js"></script>
	<script src="codebase/dhtmlxcombo.js"></script>
	<link rel="STYLESHEET" type="text/css" href="codebase/dhtmlxcombo.css">

	<script language="JavaScript" type="text/javascript">
	window.dhx_globalImgPath="codebase/imgs/";

function show_message(){
	document.getElementById('light').style.display='block';
	document.getElementById('fade').style.display='block';
}

function delete_form()
{
  document.kurse.confirm.value = confirm("Wollen Sie diesen Eintrag wirklich löschen?");
}

/* Datumchecker */
function isDatum(feld,anz)
{
	var dat = feld.value;
    if (dat!="")
    {
    	if (!dat.match(/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/))
        {
        	alert("Das Datum beim "+anz+". Termin ist nicht korrekt!\nFormat: dd.mm.yyyy");
            this.focus();
        }
    }
}

/* Uhrzeitchecker */
function isZeit(feld,anz)
{
	var dat = feld.value;
    if (dat!="")
    {
    	if (!dat.match(/^[0-9]{1,2}\:[0-9]{2}$/))
        {
        	alert("Die Uhrzeit beim "+anz+". Termin ist nicht korrekt!\nFormat: Stunde:Minute");
            this.focus();
        }
    }
}

/* Uhrzeitchecker */
function isZahl(feld,anz,bereich)
{
	var dat = feld.value;
    if (dat!="")
    {
    	if (!dat.match(/^[0-9]{1,2}\.?[0-9]{0,2}$/))
        {
        	alert(bereich+" beim "+anz+". Termin ist nicht korrekt!\nFormat z.B.: 1.0 oder 1.5 ");
            this.focus();
        }
    }
}

/* Formular neu aufbauen, aber ohne Daten aus DB zu laden */
function loadNew()
{
        window.document.kurse.update.value=1;
        window.document.kurse.submit();
}

</script>
</head>

<?

if (isset($save_x))
{
	if ($print_debug == 1) 
	{ 
		var_dump($_POST);
		print("<br>");
		foreach ($kurstermine as $k)
		{
			var_dump($k[mitarbeiter]); 
			print("<br>");
		}
		print("<br>");
	}

    $no_error = true && CheckEmptyCombo($products_id,$error_produkt) &&
    					CheckEmptyCombo($time_id,$error_zeit) &&
						CheckEmptyCombo($ort_id,$error_ort) &&
    					CheckEmptyCombo($auftrag_id,$error_auftraggeber) &&
    					CheckEmpty($jahr,$error_jahr) &&
    					CheckEmpty($preis,$error_preis) &&
    					CheckEmpty($minteil,$error_minteil) &&
    					CheckEmpty($tag,$error_tag);

if ($no_error) $command="show_message();";
?>	

<BODY onload="<?print($command)?>">
<!--  Div für messagebox und fade des Hintergrundes -->		
		<div id="light" class="white_content">
			<center>
			<b>Änderungen erfolgreich gespeichert!</b>
			<br>
			<a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
			</center>
		</div>
		<div id="fade" class="black_overlay"></div>
<?
    if ($no_error)
    {
		// Nach dem Speichern, sollen die Daten wieder direkt aus der DB geladen werden... //
		$update=0;
		// Kursdaten (oberer Bereich des Formulars) abspeichern //
        if ($id=="")
        {
            $sql_kurs_insert="insert into courses (products_id,year,timeperiods_id,institutions_id,locations_id,info,price,status,standard_time,standard_durance,standard_employee,standard_employee1,standard_employee2,standard_employee3,min_clients,remarks,durance_desc,weekday,intern_remarks,jbetrag,semrabatt,jahrrabatt,type,change_date,change_user) values ($products_id,$jahr,$time_id,$ort_id,$auftrag_id,'$info','$preis','$status','$uhrzeit_0','$dauer_0','$standard_mitarbeiter[0]','$standard_mitarbeiter[1]','$standard_mitarbeiter[2]','$standard_mitarbeiter[3]','$minteil','$remarks','$durance_desc','$tag','$intern_remarks','$jbetrag','$semrabatt','$jahrrabatt','$kursinfo','$change_date','$change_user')";
            $rs_kurs_insert=getrs($sql_kurs_insert,$print_debug,$print_error);
            $id=mysqli_insert_id($DB_TA_CONNECT);
        } else	
        {
            $sql_kurs_update="update courses set products_id=$products_id,year=$jahr,timeperiods_id=$time_id,institutions_id=$ort_id,locations_id=$auftrag_id,info='$info',price='$preis',status='$status',standard_time='$uhrzeit_0',standard_durance='$dauer_0',standard_employee='$standard_mitarbeiter0',standard_employee1='$standard_mitarbeiter1',standard_employee2='$standard_mitarbeiter2',standard_employee3='$standard_mitarbeiter3',min_clients='$minteil',remarks='$remarks',durance_desc='$durance_desc',weekday='$tag',intern_remarks='$intern_remarks',jbetrag='$jbetrag',semrabatt='$semrabatt',jahrrabatt='$jahrrabatt',type='$kursinfo',change_date='$change_date',change_user='$change_user' where id=$id";
            $rs_kurs_update=getrs($sql_kurs_update,$print_debug,$print_error);
        }

		// Kurstermine abspeichern //

        for ($zeile=0;$zeile<=$anzahl_termine;$zeile++)
        {
			if ($print_debug) { print("<br>Termin:$zeile<br>");}
			// Verfügbare Felder: kurstermine[x] [del] [id] [datum] [zeit] [dauer]
            if (($kurstermine[$zeile][del]=="0") || (!isset($kurstermine[$zeile][del])))
            {
			    // Datumsangaben formatieren //
				$date = new DateTime($kurstermine[$zeile][datum]);

                if ($kurstermine[$zeile][id]==0)
                {
                    $sql_coursetimes_insert="insert into coursetimes (date,time,courses_id,durance,employee4_id,employee4_hc,change_date,change_user) 
							   values 
							   ('".$date->format('Y-m-d')."','".$kurstermine[$zeile][zeit]."',$id,'".$kurstermine[$zeile][dauer]."','".$kurstermine[$zeile][mitarbeiter][3][mitarbeiter_id]."','".$kurstermine[$zeile][mitarbeiter][$spalte][hourcost]."','$change_date','$change_user')";

                    $rs_coursetimes_insert=getrs($sql_coursetimes_insert,$print_debug,$print_error);
                    $kurstermine[$zeile][id]=mysqli_insert_id($DB_TA_CONNECT);
					if ($print_debug) { print("neue Kurstermin-id: ".$kurstermine[$zeile][id]."-".$rs_coursetimes_insert->insert_id); }
	            }
                else
                {
                    $sql_coursetimes_update="update coursetimes set date='".$date->format('Y-m-d')."',time='".$kurstermine[$zeile][zeit]."',courses_id='$id',durance='".$kurstermine[$zeile][dauer]."',employee4_id='".$kurstermine[$zeile][mitarbeiter][3][mitarbeiter_id]."',employee4_hc='".$kurstermine[$zeile][mitarbeiter][3][hourcost]."',change_date='$change_date',change_user='$change_user' where id='".$kurstermine[$zeile][id]."'";
                    $rs_coursetimes_update=getrs($sql_coursetimes_update,$print_debug,$print_error);

					$sql_coursetimes_employees_delete="delete from coursetimes_employees where coursetimes_id='".$kurstermine[$zeile][id]."'";
                    $rs_coursetimes_employees_delete=getrs($sql_coursetimes_employees_delete,$print_debug,$print_error);
                }
				// Zuordnung Kurstermin mit Kursleitern neu in die Tabelle coursetimes_employees eintragen //	
				// Verfügbare Felder: kurstermine[x] [mitarbeiter] [y] [mitarbeiter_id]  [hourcost]  [table_id]

                for ($spalte=0;$spalte<=2;$spalte++) // 0,1,2 weil 3 schon in Tabelle coursetimes abgespeichert wird //
				{
					$sql_coursetimes_employees_insert="insert into coursetimes_employees (employees_id,coursetimes_id,hourcost,change_date,change_user) values ('".$kurstermine[$zeile][mitarbeiter][$spalte][mitarbeiter_id]."','".$kurstermine[$zeile][id]."','".$kurstermine[$zeile][mitarbeiter][$spalte][hourcost]."','".$change_date."','".$change_user."')";
                    $rs_coursetimes_employees_insert=getrs($sql_coursetimes_employees_insert,$print_debug,$print_error);
				}
			}
			else { 
				// Termine und Mitarbeiterzuordnung löschen
	            if ($kurstermine[$zeile][id] != "") 
				{
					$sql_coursetimes_employees_delete="delete from coursetimes_employees where coursetimes_id='".$kurstermine[$zeile][id]."'";
                    $rs_coursetimes_employees_delete=getrs($sql_coursetimes_employees_delete,$print_debug,$print_error);

					$sql_coursetimes_delete="delete from coursetimes where id='".$kurstermine[$zeile][id]."'";
                	$rs_coursetimes_delete=getrs($sql_coursetimes_delete,$print_debug,$print_error);
				}
			}
		}
	}
}
else
{	
?>
<BODY>
<?
}
// Kurskopie erstellen ...
if ($kopie)
{
	// Kopiere "Kopfdaten" vom Kurs und hole die neue ID und aktualisiere die Kopie mit dem Infoeintrag KOPIE ...
	$sql_kopie_kurs = "INSERT INTO courses
			  ( products_id,year,timeperiods_id,institutions_id,locations_id,info,price,status,standard_time,standard_durance,standard_employee,standard_employee1,standard_employee2,min_clients,remarks,durance_desc,weekday,intern_remarks,jbetrag,semrabatt,jahrrabatt,type, standard_employee3 )
			  ( SELECT
				  products_id,year,timeperiods_id,institutions_id,locations_id,info,price,status,standard_time,standard_durance,standard_employee,standard_employee1,standard_employee2,min_clients,remarks,durance_desc,weekday,intern_remarks,jbetrag,semrabatt,jahrrabatt,type, standard_employee3
			    FROM courses
			    WHERE id = '$id'
			  )";
    $rs_kopie_kurs=getrs($sql_kopie_kurs,$print_debug,$print_error);
	$kopie_id=mysqli_insert_id($DB_TA_CONNECT);
	$rs_s_info = getrs("select info from courses where id = '$id';",0);
	List($info) = $rs_s_info -> fetch_row();
	$sql_kurszeiten_update = "UPDATE courses SET info='".$info."-KOPIE', change_date='$change_date',change_user='$change_user' where id='$kopie_id'";
	$rs_kurszeiten_update = getrs($sql_kurszeiten_update,$print_debug,$print_error);
	if ($print_debug) { echo "neue ID: ".$kopie_id."<br>"; }

	// Kopieren der Kurszeiten ...
	$sql_s_kurszeiten = "select id from coursetimes where courses_id='$id'";
	$rs_s_kurszeiten = getrs($sql_s_kurszeiten,$print_debug,$print_error);
    while ($rs_s_kurszeiten>0 && List($kid) = $rs_s_kurszeiten -> fetch_row())
	{
		$sql_kopie_kurszeiten = "INSERT INTO coursetimes
			  ( date,time,durance,employee4_id,employee4_hc, courses_id )
			  ( SELECT
				  date, time, durance, employee4_id, employee4_hc, $kopie_id
			    FROM coursetimes
			    WHERE id = '$kid'
			  )";
		$rs_kopie_kurszeiten=getrs($sql_kopie_kurszeiten,$print_debug,$print_error);
		$kz_id = mysqli_insert_id($DB_TA_CONNECT);
		$sql_kurszeiten_update = "UPDATE coursetimes SET courses_id='$kopie_id', change_date='$change_date',change_user='$change_user' where id='$kz_id'";
		$rs_kurszeiten_update = getrs($sql_kurszeiten_update,$print_debug,$print_error);
		
		// Kopieren der Kursleiter pro Kurstermin ...
		$sql_s_kursmitarbeiter = "select id from coursetimes_employees where coursetimes_id='$kid'";
		$rs_s_kursmitarbeiter = getrs($sql_s_kursmitarbeiter,$print_debug,$print_error);
	    while ($rs_s_kursmitarbeiter>0 && List($kmid) = $rs_s_kursmitarbeiter -> fetch_row())
		{
			$sql_kopie_kursmitarbeiter = "INSERT INTO coursetimes_employees
				  ( employees_id,hourcost, coursetimes_id )
				  ( SELECT
					  employees_id,hourcost,$kz_id
				    FROM coursetimes_employees
				    WHERE id = '$kmid'
				  )";
			$rs_kopie_kursmitarbeiter=getrs($sql_kopie_kursmitarbeiter,$print_debug,$print_error);
			$km_id = mysqli_insert_id($DB_TA_CONNECT);
			$sql_kursmitarbeiter_update = "UPDATE coursetimes_employees SET coursetimes_id='$kz_id', change_date='$change_date',change_user='$change_user' where id='$km_id'";
			$rs_kursmitarbeiter_update = getrs($sql_kursmitarbeiter_update,$print_debug,$print_error);
		}
	}
	echo "<div align=center><font size=3 color=red align=center>Kopie war erfolgreich! - ID: $kopie_id</font></div>";
// Kopie wird geladen ...
$id=$kopie_id;
}
// ENDE kopie-----------------------------------------------------------------------------------

// Datensatz laden ...
if (($id > 0) && ($update==0))
{
        $sql_kurs_select="select
                courses.products_id,
                courses.year,
                courses.timeperiods_id,
                courses.institutions_id,
                courses.locations_id,
                courses.info,
                courses.price,
                courses.standard_time,
                courses.standard_durance,
                courses.standard_employee,
                courses.standard_employee1,
                courses.standard_employee2,
                courses.standard_employee3,
                products.standard_hourcost,
                courses.min_clients,
                courses.remarks,
                CONCAT(courses.products_id,courses.year,courses.timeperiods_id,courses.institutions_id,courses.locations_id,'-',courses.info),
                courses.durance_desc,
                courses.weekday,
                courses.intern_remarks,
                courses.jbetrag,
                courses.semrabatt,
                courses.jahrrabatt,
				courses.type
                        from
                courses,
                products
                        where
                $id=courses.id and courses.products_id=products.id";
        $rs_kurs_select = getrs($sql_kurs_select,$print_debug,$print_error);
      List($products_id,$jahr,$time_id,$ort_id,$auftrag_id,$info,$preis,$uhrzeit_0,$dauer_0,$standard_mitarbeiter[0],$standard_mitarbeiter[1],$standard_mitarbeiter[2],$standard_mitarbeiter[3],$st_hc,$minteil,$remarks,$code,$durance_desc,$tag,$intern_remarks,$jbetrag,$semrabatt,$jahrrabatt,$kursinfo) = $rs_kurs_select -> fetch_row();

        $sql_kurszeiten_select="select 
        		coursetimes.id,
        		coursetimes.date,
        		coursetimes.time,
        		coursetimes.durance,
				coursetimes.employee4_id,
				coursetimes.employee4_hc
			from 
        		coursetimes 
        		where 
        		courses_id=$id 
        		order by id asc";
        $rs_kurszeiten_select = getrs($sql_kurszeiten_select,$print_debug,$print_error);
        $anzahl_termine=0;
        while ($rs_kurszeiten_select>0 && List($kurstermine[$anzahl_termine][id],$kurstermine[$anzahl_termine][datum],$kurstermine[$anzahl_termine][zeit],$kurstermine[$anzahl_termine][dauer],$mitarbeiter3_id_temp,$mitarbeiter3_hc_temp) = $rs_kurszeiten_select -> fetch_row())
        {
                $sql_kurstermin_mitarbeiter_select="select a.id, a.employees_id,a.hourcost,b.status from coursetimes_employees a left join employees b on a.employees_id=b.id where a.coursetimes_id=".$kurstermine[$anzahl_termine][id]." and a.employees_id<>'' group by a.id order by a.id asc";
                $rs_kurstermin_mitarbeiter_select = getrs($sql_kurstermin_mitarbeiter_select,$print_debug,$print_error);
				$count_mitarbeiter=0;
		        while ($rs_kurstermin_mitarbeiter_select>0 && List($kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][table_id],$kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][mitarbeiter_id],$kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][hourcost],$status) = $rs_kurstermin_mitarbeiter_select -> fetch_row())
			    {
					if ($status<>'Aktiv') $empl_string.=",'".$kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][mitarbeiter_id]."'";
					$count_mitarbeiter++;
	            }

				// 4.Mitarbeiter laden
				$kurstermine[$anzahl_termine][mitarbeiter][3][mitarbeiter_id] = $mitarbeiter3_id_temp;
				$kurstermine[$anzahl_termine][mitarbeiter][3][hourcost] = $mitarbeiter3_hc_temp;
if ($print_debug==1) { print("Termin: ".$anzahl_termine."-".$kurstermine[$anzahl_termine][id]);var_dump($kurstermine[$anzahl_termine][mitarbeiter]);print("<br>"); }	
				
				// Häckchen bez. Löschen von Terminen zurücksetzen
				$kurstermine[$anzahl_termine][del]="0";

                $anzahl_termine++; // Nächster Kurstermin
        }
	$anzahl_termine--;
$fifth_time=microtime();
// Mitarbeiter-Liste // 
/*    $count=0;
    $sql_mitarbeiter_select="select a.id,a.firstname,a.lastname,'15',a.status from employees a where a.status1=1";
    $rs_mitarbeiter_select = getrs($sql_mitarbeiter_select,1,1);
    While ($rs_mitarbeiter_select>0 && List($mitarbeiter_liste[$count][id],$mitarbeiter_liste[$count][firstname],$mitarbeiter_liste[$count][lastname],$mitarbeiter_liste[$count][hourcost],$mitarbeiter_liste[$count][status]) = $rs_mitarbeiter_select -> fetch_row())
    {
		$count++;
	}
	print($count."<br>");
*/

}

?>
<script type="text/javascript">
// Mitarbeiter der Kurstermine mit den Standard-Mitarbeitern bei Änderungen dieser üebrschreiben //
<? for ($zeile1=0;$zeile1<=3;$zeile1++) { ?>
function refreshLeader<?print($zeile1)?>(mitarbeiter_id)
{
	var temp = mitarbeiter_id.options[mitarbeiter_id.options.selectedIndex].text;
	var stundensatz = temp.slice(temp.indexOf("|")+2,temp.indexOf(">"));
<? for ($zeile=0;$zeile<=$anzahl_termine;$zeile++) 
{ ?>
	window.document.kurse.elements['kurstermine[<?print($zeile)?>][mitarbeiter][<?print($zeile1)?>][mitarbeiter_id]'].value=mitarbeiter_id.value;
	window.document.kurse.elements['kurstermine[<?print($zeile)?>][mitarbeiter][<?print($zeile1)?>][hourcost]'].value=stundensatz;
<? } ?>
}
<?}?>

//Uhrzeit bei allen Kursterminen überschreiben, wenn Standardwert geändert wird //
function refreshTime(Time)
{
<? for ($zeile=0;$zeile<=$anzahl_termine;$zeile++) { ?>
	window.document.kurse.elements['kurstermine[<?print($zeile)?>][zeit]'].value=Time.value;
<? } ?>
}

//Dauer bei allen Kursterminen überschreiben, wenn Standardwert geändert wird //
function refreshDuration(Dauer)
{
<? for ($zeile=0;$zeile<=$anzahl_termine;$zeile++) { ?>
	window.document.kurse.elements['kurstermine[<?print($zeile)?>][dauer]'].value=Dauer.value;
<? } ?>
}

<? for ($zeile1=0;$zeile1<=3;$zeile1++) { ?>
function refresh_hourcost<?print($zeile1)?>(Zeile, Wert)
{
	var temp = Wert.options[Wert.options.selectedIndex].text;
	var stundensatz = temp.slice(temp.indexOf("|")+2,temp.indexOf(">"));
	switch(Zeile)
	{
<? 
for ($zeile=0;$zeile<=$anzahl_termine;$zeile++) 
{
	print("case ".$zeile.": window.document.kurse.elements['kurstermine[".$zeile."][mitarbeiter][".$zeile1."][hourcost]'].value=stundensatz;break;");
}
?>
	}
}
<?}?>

/* weitere Termine hinzufügen */
function newDate()
{
        window.document.kurse.anzahl_termine.value=<?print($anzahl_termine)?>+(window.document.kurse.newdates.value*1);
        window.document.kurse.update.value=1;
        window.document.kurse.submit();
}
</script>

	<center>
	<table border=0 cellspacing=0 cellpadding=0 width=100%>
	<tr><td height=12 align=center>
		<SPAN class="headline">Kursverwaltung</SPAN><br>
	</td></tr>
	<tr><td height=10></td></tr>
	</table>
	<BR><BR>
	
	<form name="kurse" method="post" action="<?print($PHP_SELF)?>">
	<input type="hidden" name="anzahl_termine" value="<?print($anzahl_termine)?>">
	<input type="hidden" name="update" value="<?print($update)?>">
	<input type="hidden" name="id" value="<?print($id)?>">
	
	<input type="submit" name="kopie" value="Kurs Kopie erstellen">   <!-- SUBMIT -->
	<input type="button" value="Kursblatt oeffnen" onclick="javascript:window.open('kursblatt_form.php?id=<?print($id)?>')" target=_blank>
	<br><br>

	<table border=0 cellspacing=0 cellpadding=0 width="100%">
    <tr>
		<td rowspan=100 WIDTH=2 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
        <td colspan=12 HEIGHT=2 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
        <td rowspan=100 WIDTH=2 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
    </TR>
    <tr>
        <td colspan=12>
	        <table width=100% border=0 cellspacing=0 cellpadding=0>
	        <tr height=30>
                <td class=form_header width=10></td>
                <td class=form_header> ID:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td><?echo $id?></td>
	        </tr>
	        <tr height=30>
                <td class=form_header width=10></td>
                <td class=form_header> Kurscode:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td><?echo $code?></td>
	        </tr>
	        <tr height=30>
                <td class=form_header width=10></td>
                <td class=form_header> Produkt:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
		            <select name="products_id" class="input_text">
                        <option value=-1></option>
<?
	$sql_produkte_select="select id,name from products where status='Aktiv' order by name asc";
	$rs_produkte_select = getrs($sql_produkte_select,$print_debug,$print_error);
    While ($rs_produkte_select>0 && List($prod_id,$product_name) = $rs_produkte_select -> fetch_row())
    {
	    if ($prod_id==$products_id)
    	{ ?>
                        <option class="input_text" value=<?print($prod_id)?> selected><?print($product_name)?></option>
<?      } else {?>
                        <option class="input_text" value=<?print($prod_id)?>><?print($product_name)?></option>
<?      } ?>
<?  } ?>
			        </select>
            		<?echo display_error($error_produkt);?>
                </td>
        	</tr>
	        <tr height=30>
	            <td class=form_header></td>
                <td class=form_header>Jahr:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	                <input type="text" size="10" name="jahr" maxlength="4" class="input_text" value="<?print($jahr)?>">
            		<?echo display_error($error_jahr);?>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Zeitperiode:</td>
                <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
		            <select name="time_id" class="input_text">
                        <option value=-1></option>
<?
	$sql_time_select="select id,name from timeperiods where status='Aktiv' order by name asc";
    $rs_time_select = getrs($sql_time_select,$print_debug,$print_error);
    While ($rs_time_select>0 && List($t_id,$time_name) = $rs_time_select -> fetch_row())
    {
	    if ($t_id==$time_id)
        { ?>
                        <option class="input_text" value=<?print($t_id)?> selected><?print($time_name)?></option>
<?      } else {?>
                        <option class="input_text" value=<?print($t_id)?>><?print($time_name)?></option>
<?      } ?>
<?  } ?>
                    </select>
		            <?echo display_error($error_zeit);?>
                </td>
        	</tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Institution:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <select name="ort_id" class="input_text" onblur="javascript:loadNew()">
	                <option value=-1></option>
<?
	$count=0;
	$sql_institut_select="select id,name,LEFT(address,40) from institutions where status='Aktiv' order by address asc";
	$rs_institut_select = getrs($sql_institut_select,$print_debug,$print_error);
    While ($rs_institut_select>0 && List($institut_id[$count],$institut_name[$count],$institut_address[$count]) = $rs_institut_select -> fetch_row())
    {
		if ($institut_id[$count]==$ort_id)
        { ?>
                    <option class="input_text" value=<?print($institut_id[$count])?> selected><?print($institut_address[$count])?></option>                    
<?					$current_institut_name = $institut_name[$count];
        } else {?>
                    <option class="input_text" value=<?print($institut_id[$count])?>><?print($institut_address[$count])?></option>
<?      }
	    $count++;
	} ?>
	            </select>
	            <?echo display_error($error_ort);?>
                <input type="button" value="->" onclick="javascript:window.open('institution_form.php?id=<?print($ort_id)?>')" target=_blank>
<?
    print($current_institut_name);
	$current_institut_name = "";
?>
                </td>
	        </tr>
		    <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Austragungsort:
<? // Austragungsort = $auftrag_id ?>
                </td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	            <td width=10></td>
                <td>
	            <select class="input_text" name="auftrag_id" class="" onblur="javascript:loadNew()">
	                <option value=-1></option>
<?
	for ($count_again=0;$count_again<=$count;$count_again++)
	{
	    if ($institut_id[$count_again]==$auftrag_id)
	    { ?>
	                <option class="input_text" value=<?print($institut_id[$count_again])?> selected><?print($institut_address[$count_again])?></option>
<?					$current_institut_name = $institut_name[$count_again];
      } else {?>
                    <option class="input_text" value=<?print($institut_id[$count_again])?>><?print($institut_address[$count_again])?></option>
<?      } ?>
<?	} ?>
                </select>
                <input type="button" value="->" onclick="javascript:window.open('institution_form.php?id=<?print($auftrag_id)?>')" target=_blank>
                <?echo display_error($error_auftraggeber);?>
<?
    print($current_institut_name);
?>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>zus. Information fuer Kursblatt:
	            </td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
		            <input type="text" size="59" id="info" name="info" maxlength="250" class="input_text" value="<?print($info)?>">
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Preis:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <input type="text" size="10" name="preis" maxlength="10" class="input_text" value="<?print($preis)?>">
                <?echo display_error($error_preis);?>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Jahresbetrag:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <input type="text" size="10" name="jbetrag" maxlength="10" class="input_text" value="<?print($jbetrag)?>">
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Semester- und Jahresrabatt:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>Semesterrabatt:<input type="text" size="5" name="semrabatt" maxlength="10" class="input_text" value="<?print($semrabatt)?>">
	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jahresrabatt:<input type="text" size="5" name="jahrrabatt" maxlength="10" class="input_text" value="<?print($jahrrabatt)?>">
	            </td>
	        </tr>
	        <tr height=30>
	           <td class=form_header></td>
	           <td class=form_header>Status:</td>
	           <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	           <td width=10></td>
	           <td>
               <select class="input_text" name="status">
<? if ($status=="Aktiv") { ?>
	               <option class="input_text" value="Aktiv" selected>Aktiv</option>
<? } else { ?>
                   <option class="input_text" value="Aktiv">Aktiv</option>
<? } ?>
<? if ($status=="Inaktiv") { ?>
                   <option class="input_text" value="Inaktiv" selected>Nicht Aktiv</option>
<? } else { ?>
                   <option class="input_text" value="Inaktiv">Nicht Aktiv</option>
<? } ?>
               </select>
	           </td>
		    </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Mindestteilnehmerzahl:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <input type="text" size="10" name="minteil" maxlength="10" class="input_text" value="<?print($minteil)?>">
                <?echo display_error($error_minteil);?>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Bemerkungen fuer Kursblatt:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <textarea name="remarks" cols=60 rows=3><?print($remarks)?></textarea>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>interne Bemerkungen:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <textarea name="intern_remarks" cols=60 rows=3><?print($intern_remarks)?></textarea>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>1 Einheit entsprechen:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <input type="text" size="10" name="durance_desc" maxlength="10" class="input_text" value="<?print($durance_desc)?>"> min
 		        <?echo display_error($error_minteil);?>
	            </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>Wochentag des Kurses:
                </td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <select name="tag" class="input_text">
<?
	$sql_tag_select="select id,weekday from weekdays";
    $rs_tag_select = getrs($sql_tag_select,$print_debug,$print_error);
    While ($rs_tag_select>0 && List($tag_id,$tag_liste) = $rs_tag_select -> fetch_row())
    {
	    if ($tag==$tag_id)
        { ?>
	                 <option class="input_text" value=<?print($tag_id)?> selected><?print($tag_liste)?></option>
<?      } else {?>
                     <option class="input_text" value=<?print($tag_id)?>><?print($tag_liste)?></option>
<?      } ?>
<?  } ?>
                </select>
                <?echo display_error($error_tag);?>
                </td>
	        </tr>
	        <tr height=30>
	            <td class=form_header></td>
                <td class=form_header>Kurstyp:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
		            <input type="radio" name="kursinfo" value="5" <?if ($kursinfo==5) echo "checked"?>> Grippe
		            <input type="radio" name="kursinfo" value="1" <?if ($kursinfo==1) echo "checked"?>> KG
		            <input type="radio" name="kursinfo" value="2" <?if ($kursinfo==2) echo "checked"?>> VS 1./ 2. Kl
		            <input type="radio" name="kursinfo" value="3" <?if ($kursinfo==3) echo "checked"?>> VS 3./ 4. Kl
		            <input type="radio" name="kursinfo" value="4" <?if ($kursinfo==4) echo "checked"?>> VS 1.bis 4. Kl
		            <input type="radio" name="kursinfo" value="6" <?if ($kursinfo==6) echo "checked"?>> >10 Jahre
                </td>
	        </tr>
        </table>
	</td>
    </tr>
	<tr height=30>
	    <td height=1 colspan=12 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</tr>
    <tr height=30>
	    <td height=30 colspan=12 class=form_header align="center">
		<input type="text" size=3 name="newdates" class="input_text" value="1">
	    <a href="javascript:newDate()" class="text_link">-> weiterer Termin <-</a>
<!--        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="javascript:newEmployee()" class="text_link">-> weiterer Mitarbeiter <-</a> -->
	    </td>
	</TR>
    <tr>
		<td colspan=100 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
    </TR>
	<tr>
        <td colspan=100 align=middle>
			
		</TD>
	</TR>

<? // Standardwerte // ?>
    <tr>
       <td colspan=12 align="center">
       <table width=98% cellpadding="0" cellspacing="0" border=0>
       <tr height=30>
	       <td colspan=3 width=100>Standardwerte:</td>
           <td>
<?
    // Zeitangaben formatieren //
	$time = new DateTime($uhrzeit_0);
?>
	          <input type="text" size=5 class="input_text" name=uhrzeit_0 onchange="isZeit(this,0);refreshTime(this)" value="<?print($time->format('H:i'))?>">
           </td>
           <td>
 	          <input type="text" size=5 class="input_text" name=dauer_0 onchange="isZahl(this,0,'Die Dauer');refreshDuration(this)" value="<?print(number_format($dauer_0,2))?>">
		   </td>
<?

// Standard-Mitarbeiter //
for ($i=0;$i<=3;$i++) 
{?>
           <td>
<?
	$sql_mitarbeiter_select="select lastname,firstname from employees where id=".$standard_mitarbeiter[$i];
	$rs_mitarbeiter_select=getrs($sql_mitarbeiter_select,$print_debug,$print_error);
	LIST($std_mitarbeiter[$i][lastname],$std_mitarbeiter[$i][firstname])=$rs_mitarbeiter_select -> fetch_row();
?>	
			<select style='width:200px;'  id="<? print("combo_zone_std".$i) ?>" name="standard_mitarbeiter<?print($i)?>">
				<option selected value="<? print($standard_mitarbeiter[$i])?>"><? print($std_mitarbeiter[$i][lastname].",".$std_mitarbeiter[$i][firstname])?></option>
			</select>

			<script>
				var z=new dhtmlXCombo("<? print("combo_zone_std".$i) ?>","standard_mitarbeiter<?print($i)?>",1);
				z.enableFilteringMode(true,"codebase/loadCombo.php?table='m'&prod='<?print($products_id)?>'",true,true);
				z.onBlur="alert()";
			</script>
		  </td>
<?
} //Ende for ?>
		</tr>
        <tr>
            <td colspan=100 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
        </TR>

<? // Überschriften // ?>

		<tr>
	        <td colspan=100 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
        </TR>
        <tr>
	        <td width=25>del?</td>
	        <td width=5>#</td>
            <td>Kurstermine</td>
            <td>Zeit</td>
            <td>Einheit</td>
            <td colspan=20>Mitarbeiter</td>
        </tr>

<? // Daten des Kurses // ?>
<?
$count=0; 
$sql_mitarbeiter_select="select a.id,a.firstname,a.lastname,'15',a.status from employees a where a.status1=1 order by a.lastname";
    $rs_mitarbeiter_select = getrs($sql_mitarbeiter_select,$print_debug,$print_error);
    While ($rs_mitarbeiter_select>0 && List($mitarbeiter_liste[$count][id],$mitarbeiter_liste[$count][firstname],$mitarbeiter_liste[$count][lastname],$mitarbeiter_liste[$count][hourcost],$mitarbeiter_liste[$count][status]) = $rs_mitarbeiter_select -> fetch_row())
	{
		$count++;
	}
		
	for ($zeile=0;$zeile<=$anzahl_termine;$zeile++)
    { 
		// bei neuen Terminen, müssen die Felder mit Werten gefüllt werden
		if ($kurstermine[$zeile][zeit]=="") { $kurstermine[$zeile][zeit]="00:00"; }
		if ($kurstermine[$zeile][dauer]=="") { $kurstermine[$zeile][dauer]="0.00"; }

	    // Datumsangaben formatieren //
		if ($kurstermine[$zeile][datum] == "0000-00-00") { $kurstermine[$zeile][datum]="";}
		$date = new DateTime($kurstermine[$zeile][datum]);
		$time = new DateTime($kurstermine[$zeile][zeit]);
?>
        <tr>
        	<td>
<?
	if ($kurstermine[$zeile][del]=="1") 
	{ ?>
            <input type="checkbox" name="kurstermine[<?print($zeile)?>][del]" value="1" checked>
<? } else { ?>
            <input type="checkbox" name="kurstermine[<?print($zeile)?>][del]" value="1">
<? } ?>
         </td>
         <td>
 	        <?echo ($zeile+1)."."?>
            <input type="hidden" size=4 name="kurstermine[<?print($zeile)?>][id]" value="<?print($kurstermine[$zeile][id])?>">
         </td>
         <td>
            <input type="text" size=10 class="input_text" onchange="isDatum(this,<?print($zeile+1)?>)" name="kurstermine[<?print($zeile)?>][datum]" value="<?print($date->format('d.m.Y'))?>">
         </td>
         <td>
            <input type="text" size=5 class="input_text" onchange="isZeit(this,<?print($zeile+1)?>)" name="kurstermine[<?print($zeile)?>][zeit]" value="<?print($time->format('H:i'))?>">
         </td>
         <td>
	        <input type="text" size=5 class="input_text" onchange="isZahl(this,<?print($zeile+1)?>,'Die Dauer')" name="kurstermine[<?print($zeile)?>][dauer]" value="<?print($kurstermine[$zeile][dauer])?>">
		</td>

<? for ($i=0;$i<=3;$i++) 
{ ?>
		<td>
 	    	<input type="hidden" size=3 name="kurstermine[<?print($zeile)?>][mitarbeiter][<?print($i)?>][table_id]" value="<?print($kurstermine[$zeile][mitarbeiter][$i][table_id])?>">
            <select class="input_text" onchange="refresh_hourcost<?print($i)?>(<?print($zeile)?>,this)" name="kurstermine[<?print($zeile)?>][mitarbeiter][<?print($i)?>][mitarbeiter_id]">
	            <option class="input_text" value=0></option>
<?
    $found=0;
	for ($count=0;$count<=sizeof($mitarbeiter_liste);$count++)
	{ 	   
 	   if ($mitarbeiter_liste[$count][id]== $kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id])
       { ?>
	               <option class="input_text" value=<?print($mitarbeiter_liste[$count][id])?> selected><?print($mitarbeiter_liste[$count][lastname]." ".$mitarbeiter_liste[$count][firstname]." | ".$mitarbeiter_liste[$count][hourcost]." > ".$mitarbeiter_liste[$count][status])?></option>
<?		   $found=1;
     } else {?>
	               <option class="input_text" value=<?print($mitarbeiter_liste[$count][id])?>><?print($mitarbeiter_liste[$count][lastname]." ".$mitarbeiter_liste[$count][firstname]." | ".$mitarbeiter_liste[$count][hourcost]." > ".$mitarbeiter_liste[$count][status])?></option>
<?     } ?>
<?  } 
	if ($found==0) 
	{
		$sql_suchen_select="select a.firstname,a.lastname,'15',a.status from employees a where a.id='".$kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id]."'";
		$rs_suchen_select = getrs($sql_suchen_select,$print_debug,$print_error);
		List($suchen_first,$suchen_last,$suchen_hourcost,$suchen_status) = $rs_suchen_select -> fetch_row();
?>
					<option class="input_text" selected value=<?print($kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id])?>><?print($suchen_last." ".$suchen_first." | ".$suchen_hourcost." > ".$suchen_status)?></option>
<?				
	}	

?>
            </select>
<?
	if (isAdmin() || isSecretary())
	{ ?>
            &nbsp;Stundensatz: <input type="text" size=7 onchange="isZahl(this,<?print($zeile+1)?>,'Der Stundensatz')" name="kurstermine[<?print($zeile)?>][mitarbeiter][<?print($i)?>][hourcost]" value="<?print($kurstermine[$zeile][mitarbeiter][$i][hourcost])?>">
<?
	}?>
            <input type="button" value="->" onclick="javascript:window.open('employee_form.php?id=<?print($kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id])?>')" target=_blank>
        </td>
<?  
} // Ende for für Mitarbeiter?>
     </tr>
<? 
} // Ende for für Termine?>
	</table>
	</td>
	</tr>
	<tr height=5>
	    <td colspan=12 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
    </TR>
    <tr height=30>
	    <td colspan=12 class=form_header align="center">
        <INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif">
        &nbsp;&nbsp;&nbsp;
        <INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
        <INPUT TYPE="HIDDEN" NAME="confirm" value=0>
        </td>
    </TR>
    <tr height=5>
	    <td colspan=12 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
    </TR>
</table>

<br><br>
</form>
</center>

<? 
		$seventh_time=microtime();
		print ($first_time."<br>");		
		print ($second_time."<br>");		
		print ($third_time."<br>");		
		print ($fourth_time."<br>");		
		print ($fifth_time."<br>");		
		print ($sixt_time."<br>");		
		print ($seventh_time."<br>");
?>						
</body>
</html>

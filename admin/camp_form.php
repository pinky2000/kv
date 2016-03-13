<?
/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }
require_once("../include/session.php");
require_once("../include/html.php");	
require_once("../include/checkfunction.php");
$first_time=microtime();
/* Dateiname: camp_form.php
*  Zweck: Formular zur Eingabe der Kursdaten
*/
date_default_timezone_set('Europe/Berlin');

?>
<html>
	<head>
        <title>Campverwaltung <?print($save_x);?></title>
		<link href="../css/ta.css" type="text/css" rel="stylesheet">

<?
/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
isAllow(isAdmin() || isSecretary());

/* DEFINE */
$maxcount_mitarbeiter=8; // 10 Mitarbeiter, bei 0 beginnend

/* _POST einlesen */
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$kopie = $_POST['kopie'];
$confirm = $_POST['confirm'];
$update = $_POST['update'];
$autocount = $_POST['autocount'];
$anzahl_termine = $_POST['anzahl_termine'];
if ($anzahl_termine>20) $anzahl_termine=20;
$change_date = date('Y-m-d H:m:s');
$change_user = $_SESSION['username'];
$code = $_POST['code'];
$used_db = $_SESSION['used_db'];
$alte_anzahl= $_POST['alte_anzahl'];


$back_url="../admin/camp_form.php?id=";
if (isset($back_x))
{
  header("Location: $back_url");
}

if (isset($delete_x))
{
	if ($confirm=="true" && $id>0)
	{
		$sql_delete="update courses set status='Entfernt' where id=".$id;
		$rs_del=getrs($sql_delete,$print_debug,$print_error);
		echo "<BODY><div><center><b>Camp wurde gel&ouml;scht!</b><br></center></div></BODY>";
		die;
	}
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
	$campinfo = $_POST['campinfo'];
	$status = $_POST['status'];
	$std_mitarbeiter = $_POST['std_mitarbeiter'];
	$rab_earlybook = $_POST['rab_earlybook'];
	$rab_lastminute = $_POST['rab_lastminute'];
	$rab_stammkunde = $_POST['rab_stammkunde'];
	$rab_geschwister = $_POST['rab_geschwister'];
	$rab_kombi1 = $_POST['rab_kombi1'];
	$rab_kombi2 = $_POST['rab_kombi2'];
	$rab_verlaengerung = $_POST['rab_verlaengerung'];
	$rab_halbtag = $_POST['rab_halbtag'];
	$rab_firmen = $_POST['rab_firmen'];
	$rab_sonder = $_POST['rab_sonder'];
	$kommission = $_POST['kommission'];
	$opt_verpflegung = $_POST['opt_verpflegung'];
	$opt_nachmittag = $_POST['opt_nachmittag'];
	$opt_lernmodul = $_POST['opt_lernmodul'];
	$opt_modul3 = $_POST['opt_modul3'];
	$opt_modul4 = $_POST['opt_modul4'];
	$opt_transfer_flughafen_hin = $_POST['opt_transfer_flughafen_hin'];
	$opt_transfer_flughafen_minor_hin = $_POST['opt_transfer_flughafen_minor_hin'];
	$opt_transfer_bahnhof_hin = $_POST['opt_transfer_bahnhof_hin'];
	$opt_transfer_flughafen_retour = $_POST['opt_transfer_flughafen_retour'];
	$opt_transfer_flughafen_minor_retour = $_POST['opt_transfer_flughafen_minor_retour'];
	$opt_transfer_bahnhof_retour = $_POST['opt_transfer_bahnhof_retour'];
	$opt_zertifikat = $_POST['opt_zertifikat'];
	$opt_nachmittag_auswahl = $_POST['opt_nachmittag_auswahl'];
	$opt_lernmodul_auswahl = $_POST['opt_lernmodul_auswahl'];
	$opt_modul3_auswahl = $_POST['opt_modul3_auswahl'];
	$opt_modul4_auswahl = $_POST['opt_modul4_auswahl'];
	$opt_tennis = $_POST['opt_tennis'];
	$opt_extra_day = $_POST['opt_extra_day'];
	$campleiter = $_POST['campleiter'];
	$campdays = $_POST['campdays'];
        $online_visible=$_POST['online_visible'];
}
	
	for ($zeile=0;$zeile<=$anzahl_termine;$zeile++)
	{
		for ($i=0;$i<=$maxcount_mitarbeiter;$i++)
		{
			$kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id] = intval($_POST['kurstermine_mitarbeiter_'.$zeile.'_'.$i]);
			if(($_POST['kurstermine_mitarbeiter_'.$zeile.'_'.$i.'_new_value']=="true") && (intval($_POST['kurstermine_mitarbeiter_'.$zeile.'_'.$i])=="0"))
			{
				$name_temp = explode(",",$_POST['kurstermine_mitarbeiter_'.$zeile.'_'.$i]);
				$kurstermine[$zeile][mitarbeiter][$i][lastname] = ltrim(strtoupper($name_temp[0]));
				$kurstermine[$zeile][mitarbeiter][$i][firstname] = ltrim(ucfirst($name_temp[1]));

				$sql_mitarbeiter_select="select id from employees where lastname='".$kurstermine[$zeile][mitarbeiter][$i][lastname]."' and firstname='".$kurstermine[$zeile][mitarbeiter][$i][firstname]."'";
				$rs_mitarbeiter_select=getrs($sql_mitarbeiter_select,$print_debug,$print_error);
				LIST($kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id])=$rs_mitarbeiter_select -> fetch_row();
			}	
		}
	}	
	for ($i=0;$i<=$maxcount_mitarbeiter;$i++)
	{
		${"standard_mitarbeiter".$i} = $_POST['standard_mitarbeiter'.$i];
		$standard_mitarbeiter[$i]=intval(${"standard_mitarbeiter".$i});
	}
?>
	<script src="codebase/dhtmlxcommon.js"></script>
	<script src="codebase/dhtmlxcombo.js"></script>
	<link rel="STYLESHEET" type="text/css" href="codebase/dhtmlxcombo.css">

	<script language="JavaScript" type="text/javascript">
	window.dhx_globalImgPath="codebase/imgs/";

function show_message()
{
	document.getElementById('light').style.display='block';
	document.getElementById('fade').style.display='block';
}

function delete_form()
{
  document.kurse.confirm.value = confirm("Wollen Sie diesen Eintrag wirklich l&ouml;schen?");
}

// Datumchecker 
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

// Uhrzeitchecker 
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

// Zahlchecker
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
 
// Formular neu aufbauen, aber ohne Daten aus DB zu laden 
function loadNew()
{
        window.document.kurse.update.value=1;
        window.document.kurse.submit();
}


// weitere Termine hinzuf￿/
function newDate()
{
		window.document.kurse.alte_anzahl.value=window.document.kurse.anzahl_termine.value;
        window.document.kurse.anzahl_termine.value=<?print($anzahl_termine)?>+(window.document.kurse.newdates.value*1);
        window.document.kurse.update.value=1;
        window.document.kurse.autocount.value=1;
        window.document.kurse.submit();
}

</script>
</head>

<?

if (isset($save_x))
{
    $no_error = true && CheckEmptyCombo($products_id,$error_produkt) &&
    					CheckEmptyCombo($time_id,$error_zeit) &&
						CheckEmptyCombo($auftrag_id,$error_auftraggeber) &&
    					CheckEmpty($jahr,$error_jahr) &&
    					CheckEmpty($preis,$error_preis) &&
    					CheckEmpty($minteil,$error_minteil);
						
if ($no_error) $command="show_message();";
?>	

<BODY onload="<?print($command)?>">
<!--  Div f￿sagebox und fade des Hintergrundes -->		
		<div id="light" class="white_content">
			<center>
			<b>&Auml;nderungen erfolgreich gespeichert!</b>
			<br>
			<a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
			</center>
		</div>
		<div id="fade" class="black_overlay"></div>
<?
    if ($no_error)
    {
		if ($print_debug) 
		{ 
			print("Debuginfos-Dump:<br><pre>");
			var_dump($_POST);
			print("</pre><br>Ende<br>");
			foreach ($kurstermine as $k)
			{
				print("<pre>");
				var_dump($k[mitarbeiter]); 
				print("</pre><br>");
			}
			print("<br>");
		}

		// Nach dem Speichern, sollen die Daten wieder direkt aus der DB geladen werden... //
		$update=0;
		// Kursdaten (oberer Bereich des Formulars) abspeichern //
        if ($id=="")
        {
            $sql_kurs_insert="insert into courses 
								(products_id,year,timeperiods_id,locations_id,info,price,status,standard_time,standard_durance,standard_employee,standard_employee1,standard_employee2,standard_employee3,min_clients,remarks,durance_desc,intern_remarks,type,change_date,change_user,rab_earlybook,rab_lastminute,rab_stammkunde,rab_geschwister,rab_kombi1,rab_kombi2,rab_verlaengerung,rab_halbtag,rab_firmen,rab_sonder,kommission,opt_verpflegung,opt_nachmittag,opt_lernmodul,opt_modul3,opt_modul4,opt_transfer_flughafen_hin,opt_transfer_flughafen_minor_hin,opt_transfer_bahnhof_hin,opt_transfer_flughafen_retour,opt_transfer_flughafen_minor_retour,opt_transfer_bahnhof_retour,opt_zertifikat,opt_lernmodul_auswahl,opt_modul3_auswahl,opt_modul4_auswahl,opt_nachmittag_auswahl,opt_tennis,opt_extra_day,campleiter,campdays,online_visible) 
						 values ($products_id,$jahr,$time_id,$auftrag_id,'$info','$preis','$status','$uhrzeit_0','$dauer_0','$standard_mitarbeiter[0]','$standard_mitarbeiter[1]','$standard_mitarbeiter[2]','$standard_mitarbeiter[3]','$minteil','$remarks','$durance_desc','$intern_remarks','$campinfo','$change_date','$change_user','$rab_earlybook','$rab_lastminute','$rab_stammkunde','$rab_geschwister','$rab_kombi1','$rab_kombi2','$rab_verlaengerung','$rab_halbtag','$rab_firmen','$rab_sonder','$kommission','$opt_verpflegung','$opt_nachmittag','$opt_lernmodul','$opt_modul3','$opt_modul4','$opt_transfer_flughafen_hin','$opt_transfer_flughafen_minor_hin','$opt_transfer_bahnhof_hin','$opt_transfer_flughafen_retour','$opt_transfer_flughafen_minor_retour','$opt_transfer_bahnhof_retour','$opt_zertifikat','$opt_lernmodul_auswahl','$opt_modul3_auswahl','$opt_modul4_auswahl','$opt_nachmittag_auswahl','$opt_tennis','$opt_extra_day','$campleiter','$campdays','$online_visible')";
            $rs_kurs_insert=getrs($sql_kurs_insert,$print_debug,$print_error);
            $id=mysqli_insert_id($DB_TA_CONNECT);
        } else	
        {
            $sql_kurs_update="update courses set products_id=$products_id,year=$jahr,timeperiods_id=$time_id,locations_id=$auftrag_id,info='$info',price='$preis',status='$status',standard_time='$uhrzeit_0',standard_durance='$dauer_0',standard_employee='$standard_mitarbeiter[0]',standard_employee1='$standard_mitarbeiter[1]',standard_employee2='$standard_mitarbeiter[2]',standard_employee3='$standard_mitarbeiter[3]',min_clients='$minteil',remarks='$remarks',durance_desc='$durance_desc',intern_remarks='$intern_remarks',type='$campinfo',change_date='$change_date',change_user='$change_user',rab_earlybook='$rab_earlybook',rab_lastminute='$rab_lastminute',rab_stammkunde='$rab_stammkunde',rab_geschwister='$rab_geschwister',rab_kombi1='$rab_kombi1',rab_kombi2='$rab_kombi2',rab_verlaengerung='$rab_verlaengerung',rab_halbtag='$rab_halbtag',rab_firmen='$rab_firmen',rab_sonder='$rab_sonder',kommission='$kommission',opt_verpflegung='$opt_verpflegung',opt_nachmittag='$opt_nachmittag',opt_lernmodul='$opt_lernmodul',opt_modul3='$opt_modul3',opt_modul4='$opt_modul4',opt_transfer_flughafen_hin='$opt_transfer_flughafen_hin',opt_transfer_flughafen_minor_hin='$opt_transfer_flughafen_minor_hin',opt_transfer_bahnhof_hin='$opt_transfer_bahnhof_hin',opt_transfer_flughafen_retour='$opt_transfer_flughafen_retour',opt_transfer_flughafen_minor_retour='$opt_transfer_flughafen_minor_retour',opt_transfer_bahnhof_retour='$opt_transfer_bahnhof_retour',opt_zertifikat='$opt_zertifikat',opt_nachmittag_auswahl='$opt_nachmittag_auswahl',opt_lernmodul_auswahl='$opt_lernmodul_auswahl',opt_modul3_auswahl='$opt_modul3_auswahl',opt_modul4_auswahl='$opt_modul4_auswahl',opt_tennis='$opt_tennis',opt_extra_day='$opt_extra_day',campleiter='$campleiter',campdays='$campdays',online_visible='$online_visible' where id=$id";
            $rs_kurs_update=getrs($sql_kurs_update,$print_debug,$print_error);
        }

		// Kurstermine abspeichern //

        for ($zeile=0;$zeile<=$anzahl_termine;$zeile++)
        {
			if ($print_debug) { print("<br>Termin:$zeile<br>");}
			// Verf￿ Felder: kurstermine[x] [del] [id] [datum] [zeit] [dauer]
            if (($kurstermine[$zeile][del]=="0") || (!isset($kurstermine[$zeile][del])))
            {
			    // Datumsangaben formatieren //
				$date = new DateTime($kurstermine[$zeile][datum]);

                if ($kurstermine[$zeile][id]==0)
                {
                    $sql_coursetimes_insert="insert into coursetimes (date,time,time_end,courses_id,durance,employee4_id,employee4_hc,change_date,change_user) 
							   values 
							   ('".$date->format('Y-m-d')."','".$kurstermine[$zeile][zeit]."','".$kurstermine[$zeile][zeit_ende]."',$id,'".$kurstermine[$zeile][dauer]."','".$kurstermine[$zeile][mitarbeiter][3][mitarbeiter_id]."','".$kurstermine[$zeile][mitarbeiter][$spalte][hourcost]."','$change_date','$change_user')";

                    $rs_coursetimes_insert=getrs($sql_coursetimes_insert,$print_debug,$print_error);
                    $kurstermine[$zeile][id]=mysqli_insert_id($DB_TA_CONNECT);
					if ($print_debug) { print("neue Kurstermin-id: ".$kurstermine[$zeile][id]."-".$rs_coursetimes_insert->insert_id); }
	            }
                else
                {
                    $sql_coursetimes_update="update coursetimes set date='".$date->format('Y-m-d')."',time='".$kurstermine[$zeile][zeit]."',time_end='".$kurstermine[$zeile][zeit_ende]."',courses_id='$id',durance='".$kurstermine[$zeile][dauer]."',employee4_id='".$kurstermine[$zeile][mitarbeiter][3][mitarbeiter_id]."',employee4_hc='".$kurstermine[$zeile][mitarbeiter][3][hourcost]."',change_date='$change_date',change_user='$change_user' where id='".$kurstermine[$zeile][id]."'";
                    $rs_coursetimes_update=getrs($sql_coursetimes_update,$print_debug,$print_error);

//					$sql_coursetimes_employees_delete="delete from coursetimes_employees where coursetimes_id='".$kurstermine[$zeile][id]."'";
//                    $rs_coursetimes_employees_delete=getrs($sql_coursetimes_employees_delete,$print_debug,$print_error);
                }
				// Zuordnung Kurstermin mit Kursleitern neu in die Tabelle coursetimes_employees eintragen //	
				// Verf￿ Felder: kurstermine[x] [mitarbeiter] [y] [mitarbeiter_id]  [hourcost]  [table_id]

                for ($spalte=0;$spalte<=$maxcount_mitarbeiter;$spalte++) // 0,1,2 weil 3 schon in Tabelle coursetimes abgespeichert wird //
				{
				if (($kurstermine[$zeile][mitarbeiter][$spalte][mitarbeiter_id]<>0) || ($kurstermine[$zeile][mitarbeiter][$spalte][mitarbeiter_id]<> ""))
				{		
					if (($kurstermine[$zeile][mitarbeiter][$spalte][hourcost]=="") || (intval($kurstermine[$zeile][mitarbeiter][$spalte][hourcost])==0)) 
					{
						$sql_mitarbeiter_select="select b.value from employees a left join hourcosts b on a.id=b.employee_id and b.products_id='$products_id' where a.id=".$kurstermine[$zeile][mitarbeiter][$spalte][mitarbeiter_id];
						$rs_mitarbeiter_select=getrs($sql_mitarbeiter_select,$print_debug,$print_error);
						LIST($kurstermine[$zeile][mitarbeiter][$spalte][hourcost])=$rs_mitarbeiter_select -> fetch_row();
					}
				}

						if ($kurstermine[$zeile][mitarbeiter][$spalte][table_id]=="")
						{	
							$sql_coursetimes_employees_insert="insert into coursetimes_employees (employees_id,coursetimes_id,hourcost,change_date,change_user) values ('".$kurstermine[$zeile][mitarbeiter][$spalte][mitarbeiter_id]."','".$kurstermine[$zeile][id]."','".$kurstermine[$zeile][mitarbeiter][$spalte][hourcost]."','".$change_date."','".$change_user."')";
                    		$rs_coursetimes_employees_insert=getrs($sql_coursetimes_employees_insert,$print_debug,$print_error);
						} else
						{
							$sql_coursetimes_employees_update="update coursetimes_employees set employees_id='".$kurstermine[$zeile][mitarbeiter][$spalte][mitarbeiter_id]."',coursetimes_id='".$kurstermine[$zeile][id]."',hourcost='".$kurstermine[$zeile][mitarbeiter][$spalte][hourcost]."',change_date='".$change_date."',change_user='".$change_user."' where id='".$kurstermine[$zeile][mitarbeiter][$spalte][table_id]."'";
	                    	$rs_coursetimes_employees_update=getrs($sql_coursetimes_employees_update,$print_debug,$print_error);
							
							
						}	
				}
			}
			else { 
				// Termine und Mitarbeiterzuordnung l￿en
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
if ($autocount=="1") { $command="autocount();"; } else { $command="void();"; }
?>
<BODY onload="<?print($command);?>">
<?
}

// Kurskopie erstellen ...
if ($kopie)
{
	// Kopiere "Kopfdaten" vom Kurs und hole die neue ID und aktualisiere die Kopie mit dem Infoeintrag KOPIE ...
	$sql_kopie_kurs = "INSERT INTO courses
			  ( products_id,year,timeperiods_id,locations_id,info,price,status,standard_time,standard_durance,standard_employee,standard_employee1,standard_employee2,min_clients,remarks,durance_desc,weekday,intern_remarks,jbetrag,semrabatt,jahrrabatt,type, standard_employee3,
                rab_earlybook,
				rab_lastminute,
				rab_stammkunde,
				rab_geschwister,
				rab_kombi1,
				rab_kombi2,
				rab_verlaengerung,
				rab_halbtag,
				rab_firmen,
				rab_sonder,
				kommission,
				opt_verpflegung,
				opt_nachmittag,
				opt_lernmodul,
				opt_modul3,
				opt_modul4,
				opt_transfer_flughafen_hin,
				opt_transfer_flughafen_minor_hin,
				opt_transfer_bahnhof_hin,
				opt_transfer_flughafen_retour,
				opt_transfer_flughafen_minor_retour,
				opt_transfer_bahnhof_retour,
				opt_zertifikat,
				opt_nachmittag_auswahl,
				opt_lernmodul_auswahl,
				opt_modul3_auswahl,
				opt_modul4_auswahl,
				opt_extra_day,
				opt_tennis,
				campleiter,
				campdays,
                                online_visible
			  )
			  ( SELECT
				  products_id,year,timeperiods_id,locations_id,info,price,status,standard_time,standard_durance,standard_employee,standard_employee1,standard_employee2,min_clients,remarks,durance_desc,weekday,intern_remarks,jbetrag,semrabatt,jahrrabatt,type, standard_employee3,
  				  rab_earlybook,
				  rab_lastminute,
				  rab_stammkunde,
				  rab_geschwister,
				  rab_kombi1,
				  rab_kombi2,
				  rab_verlaengerung,
				  rab_halbtag,
				  rab_firmen,
				  rab_sonder,
				  kommission,
				  opt_verpflegung,
				  opt_nachmittag,
				  opt_lernmodul,
				  opt_modul3,
				  opt_modul4,
				  opt_transfer_flughafen_hin,
				  opt_transfer_flughafen_minor_hin,
				  opt_transfer_bahnhof_hin,
				  opt_transfer_flughafen_retour,
				  opt_transfer_flughafen_minor_retour,
				  opt_transfer_bahnhof_retour,
				  opt_zertifikat,
				  opt_nachmittag_auswahl,
				  opt_lernmodul_auswahl,
				  opt_modul3_auswahl,
				  opt_modul4_auswahl,
				  opt_extra_day,
				  opt_tennis,
				  campleiter,
				  campdays,
                                  online_visible
			    FROM courses
			    WHERE id = '$id'
			  )";
    $rs_kopie_kurs=getrs($sql_kopie_kurs,$print_debug,$print_error);
	$kopie_id=mysqli_insert_id($DB_TA_CONNECT);
	$rs_s_info = getrs("select info,intern_remarks from courses where id = '$id';",0);
	List($info,$intern) = $rs_s_info -> fetch_row();
	$sql_kurszeiten_update = "UPDATE courses SET info='".$info."-KOPIE', intern_remarks='".$intern."\nKopie von Kurs ".$id.", erstellt am ".$change_date." von ".$change_user."',change_date='$change_date',change_user='$change_user' where id='$kopie_id'";
	$rs_kurszeiten_update = getrs($sql_kurszeiten_update,$print_debug,$print_error);
	if ($print_debug) { echo "neue ID: ".$kopie_id."<br>"; }

	// Kopieren der Kurszeiten ...
	$sql_s_kurszeiten = "select id from coursetimes where courses_id='$id'";
	$rs_s_kurszeiten = getrs($sql_s_kurszeiten,$print_debug,$print_error);
    while ($rs_s_kurszeiten>0 && List($kid) = $rs_s_kurszeiten -> fetch_row())
	{
		$sql_kopie_kurszeiten = "INSERT INTO coursetimes
			  ( date,time,time_end,durance,courses_id )
			  ( SELECT
				  date, time,time_end, durance, $kopie_id
			    FROM coursetimes
			    WHERE id = '$kid'
			  )";
		$rs_kopie_kurszeiten=getrs($sql_kopie_kurszeiten,$print_debug,$print_error);
		$kz_id = mysqli_insert_id($DB_TA_CONNECT);
		$sql_kurszeiten_update = "UPDATE coursetimes SET courses_id='$kopie_id', change_date='$change_date',change_user='$change_user' where id='$kz_id'";
		$rs_kurszeiten_update = getrs($sql_kurszeiten_update,$print_debug,$print_error);
		
		// Kopieren der Kursleiter pro Kurstermin ...
/* Anforderung Pisi mit 6.11.2015: Kursleiter sollen nicht mitkopiert werden
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
*/		
	}
	echo "<div align=center><font size=3 color=red align=center>Kopie war erfolgreich! - ID: $kopie_id - <a href='camp_form.php?id=$kopie_id'>Link</a></font></div>";die;
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
				courses.type,
				courses.rab_earlybook,
				courses.rab_lastminute,
				courses.rab_stammkunde,
				courses.rab_geschwister,
				courses.rab_kombi1,
				courses.rab_kombi2,
				courses.rab_verlaengerung,
				courses.rab_halbtag,
				courses.rab_firmen,
				courses.rab_sonder,
				courses.kommission,
				courses.opt_verpflegung,
				courses.opt_nachmittag,
				courses.opt_lernmodul,
				courses.opt_modul3,
				courses.opt_modul4,
				courses.opt_transfer_flughafen_hin,
				courses.opt_transfer_flughafen_minor_hin,
				courses.opt_transfer_bahnhof_hin,
				courses.opt_transfer_flughafen_retour,
				courses.opt_transfer_flughafen_minor_retour,
				courses.opt_transfer_bahnhof_retour,
				courses.opt_zertifikat,
				courses.opt_nachmittag_auswahl,
				courses.opt_lernmodul_auswahl,
				courses.opt_modul3_auswahl,
				courses.opt_modul4_auswahl,
				courses.opt_extra_day,
				courses.opt_tennis,
				courses.campleiter,
				courses.campdays,
                                courses.online_visible
					from
                courses,
                products
                        where
                $id=courses.id and courses.products_id=products.id";
        $rs_kurs_select = getrs($sql_kurs_select,$print_debug,$print_error);
      List($products_id,$jahr,$time_id,$auftrag_id,$info,$preis,$uhrzeit_0,$dauer_0,$standard_mitarbeiter[0],$standard_mitarbeiter[1],$standard_mitarbeiter[2],$standard_mitarbeiter[3],$st_hc,$minteil,$remarks,$code,$durance_desc,$tag,$intern_remarks,$jbetrag,$semrabatt,$jahrrabatt,$campinfo,$rab_earlybook,$rab_lastminute,$rab_stammkunde,$rab_geschwister,$rab_kombi1,$rab_kombi2,$rab_verlaengerung,$rab_halbtag,$rab_firmen,$rab_sonder,$kommission,$opt_verpflegung,$opt_nachmittag,$opt_lernmodul,$opt_modul3,$opt_modul4,$opt_transfer_flughafen_hin,$opt_transfer_flughafen_minor_hin,$opt_transfer_bahnhof_hin,$opt_transfer_flughafen_retour,$opt_transfer_flughafen_minor_retour,$opt_transfer_bahnhof_retour,$opt_zertifikat,$opt_nachmittag_auswahl,$opt_lernmodul_auswahl,$opt_modul3_auswahl,$opt_modul4_auswahl,$opt_extra_day,$opt_tennis,$campleiter,$campdays,$online_visible) = $rs_kurs_select -> fetch_row();

/* Kurszeiten laden */
        $sql_kurszeiten_select="select 
        		coursetimes.id,
        		coursetimes.date,
        		coursetimes.time,
        		coursetimes.time_end,
        		coursetimes.durance,
				coursetimes.employee4_id,
				coursetimes.employee4_hc
			from 
        		coursetimes 
        		where 
        		courses_id=$id 
        		order by date asc";
        $rs_kurszeiten_select = getrs($sql_kurszeiten_select,$print_debug,$print_error);
        $anzahl_termine=0;
        while ($rs_kurszeiten_select>0 && List($kurstermine[$anzahl_termine][id],$kurstermine[$anzahl_termine][datum],$kurstermine[$anzahl_termine][zeit],$kurstermine[$anzahl_termine][zeit_ende],$kurstermine[$anzahl_termine][dauer],$mitarbeiter3_id_temp,$mitarbeiter3_hc_temp) = $rs_kurszeiten_select -> fetch_row())
        {
/* zu jeder Kurszeit werden die Mitarbeiter geladen */
                $sql_kurstermin_mitarbeiter_select="select a.id, a.employees_id,a.hourcost,b.lastname,b.firstname,b.status,c.value from coursetimes_employees a left join employees b on a.employees_id=b.id left join hourcosts c on c.employee_id=b.id and c.products_id=$products_id where a.coursetimes_id=".$kurstermine[$anzahl_termine][id]." and a.employees_id<>''";
                $rs_kurstermin_mitarbeiter_select = getrs($sql_kurstermin_mitarbeiter_select,$print_debug,$print_error);
				$count_mitarbeiter=0;
		        while ($rs_kurstermin_mitarbeiter_select>0 && List($kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][table_id],$kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][mitarbeiter_id],$kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][hourcost],$kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][lastname],$kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][firstname],$status,$kurstermine[$anzahl_termine][mitarbeiter][$count_mitarbeiter][std_hourcost]) = $rs_kurstermin_mitarbeiter_select -> fetch_row())
			    {
					$count_mitarbeiter++;
					
	            }

				// 4.Mitarbeiter laden
				$kurstermine[$anzahl_termine][mitarbeiter][3][mitarbeiter_id] = $mitarbeiter3_id_temp;
				$kurstermine[$anzahl_termine][mitarbeiter][3][hourcost] = $mitarbeiter3_hc_temp;
if ($print_debug==1) { print("Termin: ".$anzahl_termine."-".$kurstermine[$anzahl_termine][id]);var_dump($kurstermine[$anzahl_termine][mitarbeiter]);print("<br>"); }	
				
				// H￿chen bez. L￿en von Terminen zur￿zen
				$kurstermine[$anzahl_termine][del]="0";

                $anzahl_termine++; // N￿ster Kurstermin
        }
	$anzahl_termine--;

}

if (!isset($alte_anzahl))
{
	$alte_anzahl=$anzahl_termine;
}
?>
<script type="text/javascript">
// Mitarbeiter und die Stundens￿e der Kurstermine mit den Standard-Mitarbeitern und Standard-Stundensatz bei ￿derungen dieser ￿hreiben //

<? for ($zeile1=0;$zeile1<=$maxcount_mitarbeiter;$zeile1++) { ?>
function refreshLeader<?print($zeile1)?>(mitarbeiter_id)
{
	<? for ($zeile=0;$zeile<=$anzahl_termine;$zeile++) 
	{ ?>
		window.document.kurse.elements['kurstermine_mitarbeiter_<?print($zeile)?>_<?print($zeile1)?>'].value=mitarbeiter_id.value;
		window.document.kurse.elements['kurstermine[<?print($zeile)?>][mitarbeiter][<?print($zeile1)?>][hourcost]'].value="";
<?  } ?>
    window.document.kurse.update.value=1;
    window.document.kurse.submit();
}
<?}?>


//Uhrzeit bei allen Kursterminen ￿hreiben, wenn Standardwert ge￿ert wird //
function refreshTime(Time)
{
<? for ($zeile=0;$zeile<=$anzahl_termine;$zeile++) { ?>
	window.document.kurse.elements['kurstermine[<?print($zeile)?>][zeit]'].value=Time.value;
<? } ?>
}

//Uhrzeit bei allen Kursterminen ￿hreiben, wenn Standardwert ge￿ert wird //
function refreshTimeEnd(Time)
{
<? for ($zeile=0;$zeile<=$anzahl_termine;$zeile++) { ?>
	window.document.kurse.elements['kurstermine[<?print($zeile)?>][zeit_ende]'].value=Time.value;
<? } ?>
}

//Dauer bei allen Kursterminen ￿hreiben, wenn Standardwert ge￿ert wird //
function refreshDuration(Dauer)
{
<? for ($zeile=0;$zeile<=$anzahl_termine;$zeile++) { ?>
	window.document.kurse.elements['kurstermine[<?print($zeile)?>][dauer]'].value=Dauer.value;
<? } ?>
}

<? for ($zeile1=0;$zeile1<=$maxcount_mitarbeiter;$zeile1++) { ?>
function refresh_hourcost<?print($zeile1)?>(Zeile, Wert)
{
//	var t = document.getElementById(Wert);
	var t= document.getElementByName("kurstermine[1][mitarbeiter][0][mitarbeiter_id]");
	var temp = t.textContent;
	alert (temp);
//	var stundensatz = temp.slice(temp.indexOf("|")+2,temp.indexOf(">"));
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

function newEmployee()
{
        window.document.kurse.anzahl_termine.value=<?print($anzahl_termine)?>+(window.document.kurse.newdates.value*1);
        window.document.kurse.update.value=1;
        window.document.kurse.submit();
}

function autocount()
{
	var firstdate = window.document.kurse.elements['kurstermine[0][datum]'].value;	
    var split = firstdate.split(".",3);
	var datum = new Date(split[2],split[1]-1,split[0]);
    var firsttime = datum.getTime();

    for (loop=1;loop<=<?print($anzahl_termine)?>;loop++)
	{	
	var nexttime = firsttime + (24 * 60 * 60 * 1000);
    datum.setTime(nexttime);
    var Jahr = datum.getFullYear();
    var Monat = datum.getMonth()+1;
    var Tag = datum.getDate();
	if (datum.getHours()==23) 
	{
		nexttime = nexttime +(60*60*1000);
	    datum.setTime(nexttime);
    	var Tag = datum.getDate();    
	}
	if (datum.getHours()==1) 
	{
		nexttime = nexttime-(60*60*1000);
	    datum.setTime(nexttime);
    	var Tag = datum.getDate();    
	}
	window.document.kurse.elements['kurstermine['+loop+'][datum]'].value=Tag+"."+Monat+"."+Jahr;
	firsttime=nexttime;
	}
}

</script>
	<center>
	<table border=0 cellspacing=0 cellpadding=0 width=100%>
	<tr><td height=12 align=center>
		<SPAN class="headline">Campverwaltung</SPAN><br>
	</td></tr>
	<tr><td height=10></td></tr>
	</table>
	<BR><BR>
	
	<form name="kurse" method="post" action="<?print($PHP_SELF)?>">
	<input type="hidden" name="anzahl_termine" value="<?print($anzahl_termine)?>">
	<input type="hidden" name="update" value="<?print($update)?>">
	<input type="hidden" name="autocount" value="<?print($autocount)?>">
	<input type="hidden" name="id" value="<?print($id)?>">
	
	<input type="submit" name="kopie" value="Camp Kopie erstellen">   <!-- SUBMIT -->
	<input type="button" value="Campblatt &ouml;ffnen" onclick="javascript:window.open('campblatt_form.php?id=<?print($id)?>')" target=_blank>
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
                <td class=form_header width=100> ID:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td><?echo $id?></td>
	        </tr>
	        <tr height=30>
                <td class=form_header width=10></td>
                <td class=form_header width=100> Campcode:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
				<input type="hidden" name="code" value="<?print($code)?>">
	            <td><?echo $code?></td>
	        </tr>
	        <tr height=30>
                <td class=form_header width=10></td>
                <td class=form_header width=100> Produkt:</td>
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
                <td class=form_header width=100>Jahr:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	                <input type="text" size="10" name="jahr" maxlength="4" class="input_text" value="<?print($jahr)?>">
            		<?echo display_error($error_jahr);?>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>Zeitperiode:</td>
                <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
		            <select name="time_id" class="input_text">
                        <option value=-1></option>
<?
	$sql_time_select="select id,name from timeperiods where status='Aktiv' and camp='1' order by name asc";
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
                <td class=form_header width=100>Austragungsort:
<? // Austragungsort = $auftrag_id ?>
                </td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	            <td width=10></td>
                <td>
	            <select class="input_text" name="auftrag_id" class="" onblur="javascript:loadNew()">
	                <option value=-1></option>
<?
	$count=0;
	$sql_institut_select="select id,name,LEFT(address,40) from institutions where status='Aktiv' order by address asc";
	$rs_institut_select = getrs($sql_institut_select,$print_debug,$print_error);
    While ($rs_institut_select>0 && List($institut_id[$count],$institut_name[$count],$institut_address[$count]) = $rs_institut_select -> fetch_row())
    {
		if ($institut_id[$count]==$auftrag_id)
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
                <input type="button" value="->" onclick="javascript:window.open('institution_form.php?id=<?print($auftrag_id)?>')" target=_blank>
<?
    print($current_institut_name);
	$current_institut_name = "";
?>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>zus. Information f&uuml;r Campblatt:
	            </td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
		            <input type="text" size="59" id="info" name="info" maxlength="250" class="input_text" value="<?print($info)?>">
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>Basispreis:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <input type="text" size="10" name="preis" maxlength="10" class="input_text" value="<?print($preis)?>">
                <?echo display_error($error_preis);?>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>Mindestteilnehmerzahl:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <input type="text" size="10" name="minteil" maxlength="10" class="input_text" value="<?print($minteil)?>">
                <?echo display_error($error_minteil);?>
                </td>
	        </tr>
<? if (!isset($id))
{ ?>
			<tr height=30>
                <td class=form_header></td>
                <td class=form_header colspan=10><b><font color=red>Bitte bis hierher Daten eingeben und erstmalig speichern !</font></b></td>
	        </tr>
<? } ?>	
			<tr height=30>
                <td class=form_header></td>
                <td class=form_header colspan=10><b>Rabatte:</b></td>
	        </tr>
			<tr height=30>
                <td class=form_header></td>
                <td class=form_header></td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
				<table><tr><td width=180>
	            Fr&uuml;hbucherbonus:</td><td><input type="text" size="5" name="rab_earlybook" maxlength="10" class="input_text" value="<?print($rab_earlybook)?>">
				</td><td width=180>
	             Last-Minute Rabatt:</td><td><input type="text" size="5" name="rab_lastminute" maxlength="10" class="input_text" value="<?print($rab_lastminute)?>">
                </td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header></td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
				<td>
				<table><tr><td width=180>
                Stammkundenrabatt:</td><td><input type="text" size="5" name="rab_stammkunde" maxlength="10" class="input_text" value="<?print($rab_stammkunde)?>">
				</td><td width=180>
	             Geschwisterrabatt:</td><td><input type="text" size="5" name="rab_geschwister" maxlength="10" class="input_text" value="<?print($rab_geschwister)?>">
	            </td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header></td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
   				<td>
				<table><tr><td width=180>
				Kombi-Package 1:</td><td><input type="text" size="5" name="rab_kombi1" maxlength="10" class="input_text" value="<?print($rab_kombi1)?>">
	            </td><td width=180>
				 Kombi-Package 2:</td><td><input type="text" size="5" name="rab_kombi2" maxlength="10" class="input_text" value="<?print($rab_kombi2)?>">
	            </td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header></td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
   				<td>
				<table><tr><td width=180>
                Verl&auml;ngerungswochenrabatt:</td><td><input type="text" size="5" name="rab_verlaengerung" maxlength="10" class="input_text" value="<?print($rab_verlaengerung)?>">
	            </td><td width=180>
				 Halbtagesrabatt:</td><td><input type="text" size="5" name="rab_halbtag" maxlength="10" class="input_text" value="<?print($rab_halbtag)?>">
	            </td></tr></table>
	            </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header></td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
				<td>
   				<table><tr><td width=180>
                Firmenrabatt (in %):</td><td><input type="text" size="5" name="rab_firmen" maxlength="10" class="input_text" value="<?print($rab_firmen)?>"> %
	            </td><td width=180>
				 Sonderrabatt (in %):</td><td><input type="text" size="5" name="rab_sonder" maxlength="10" class="input_text" value="<?print($rab_sonder)?>"> %
	            </td></tr></table>
	            </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>Firmenname:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td><input type="text" size="50" name="kommission" maxlength="100" class="input_text" value="<?print($kommission)?>">
	            </td>
	        </tr>

			<tr height=30>
                <td class=form_header></td>
                <td class=form_header colspan=10><b>Optionen und Zusatzleistungen:</b></td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header></td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
   				<table><tr><td width=180>
				Verpflegung:</td><td><input type="text" size="5" name="opt_verpflegung" maxlength="10" class="input_text" value="<?print($opt_verpflegung)?>">
	            </td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
   				<table><tr><td width=180>
				Modul 1 (mehrfach):</td><td><input type="text" size="5" name="opt_nachmittag" maxlength="10" class="input_text" value="<?print($opt_nachmittag)?>">
	            </td><td width=180>
				 M&ouml;glichkeiten:</td><td><input type="text" size="70" name="opt_nachmittag_auswahl" maxlength="300" class="input_text" value="<?print($opt_nachmittag_auswahl)?>">
				(mit ; trennen)
				</td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
				<table><tr><td width=180>
				Modul 2 (alternativ):</td><td><input type="text" size="5" name="opt_lernmodul" maxlength="10" class="input_text" value="<?print($opt_lernmodul)?>">
	            </td><td width=180>
				 M&ouml;glichkeiten:</td><td><input type="text" size="70" name="opt_lernmodul_auswahl" maxlength="300" class="input_text" value="<?print($opt_lernmodul_auswahl)?>">
				(mit ; trennen)
				</td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
				<table><tr><td width=180>
				Modul 3 (mehrfach):</td><td><input type="text" size="5" name="opt_modul3" maxlength="10" class="input_text" value="<?print($opt_modul3)?>">
	            </td><td width=180>
				 M&ouml;glichkeiten:</td><td><input type="text" size="70" name="opt_modul3_auswahl" maxlength="300" class="input_text" value="<?print($opt_modul3_auswahl)?>">
				(mit ; trennen)
				</td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
				<table><tr><td width=180>
				Modul 4 (alternativ):</td><td><input type="text" size="5" name="opt_modul4" maxlength="10" class="input_text" value="<?print($opt_modul4)?>">
	            </td><td width=180>
				 M&ouml;glichkeiten:</td><td><input type="text" size="70" name="opt_modul4_auswahl" maxlength="300" class="input_text" value="<?print($opt_modul4_auswahl)?>">
				(mit ; trennen)
				</td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
				<table><tr><td width=180>
				Aufpreis Sprachzertifikat:</td><td><input type="text" size="5" name="opt_zertifikat" maxlength="10" class="input_text" value="<?print($opt_zertifikat)?>">
	            </td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
				<td>
				<table><tr><td width=180>
				Aufpreis Transfer Flughafen Hinfahrt:</td><td><input type="text" size="5" name="opt_transfer_flughafen_hin" maxlength="10" class="input_text" value="<?print($opt_transfer_flughafen_hin)?>">
	            </td><td width=180>
				 Aufpreis Transfer Flughafen <br> R&uuml;ckfahrt:</td><td><input type="text" size="5" name="opt_transfer_flughafen_retour" maxlength="10" class="input_text" value="<?print($opt_transfer_flughafen_retour)?>">
				</td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
				<td>
				<table><tr><td width=180>
				Transfer Flughafen Hinfahrt <br>"unaccompanied minor":</td><td><input type="text" size="5" name="opt_transfer_flughafen_minor_hin" maxlength="10" class="input_text" value="<?print($opt_transfer_flughafen_minor_hin)?>">
				</td><td width=180>
				 Transfer Flughafen R&uuml;ckfahrt <br> "unaccompanied minor":</td><td><input type="text" size="5" name="opt_transfer_flughafen_minor_retour" maxlength="10" class="input_text" value="<?print($opt_transfer_flughafen_minor_retour)?>">			
	            </td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
				<table><tr><td width=180>
				Aufpreis Transfer Bahnhof Hinfahrt:</td><td><input type="text" size="5" name="opt_transfer_bahnhof_hin" maxlength="10" class="input_text" value="<?print($opt_transfer_bahnhof_hin)?>">
	            </td><td width=180>
				 Aufpreis Transfer Bahnhof R&uuml;ckfahrt:</td><td><input type="text" size="5" name="opt_transfer_bahnhof_retour" maxlength="10" class="input_text" value="<?print($opt_transfer_bahnhof_retour)?>">
				</td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
				<table><tr><td width=180>
				Aufpreis Extra-Tag:</td><td><input type="text" size="5" name="opt_extra_day" maxlength="10" class="input_text" value="<?print($opt_extra_day)?>">
	            </td></tr></table>
				</td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
				<table><tr><td width=180>
				Aufpreis Tennis F&uuml;nferBlock:</td><td><input type="text" size="5" name="opt_tennis" maxlength="10" class="input_text" value="<?print($opt_tennis)?>">
	            </td></tr></table>
				</td>
	        </tr>

	        <tr height=30>
	           <td class=form_header></td>
	           <td class=form_header width=100>Status:</td>
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
                <td class=form_header width=100>Bemerkungen f&uuml;r Campblatt:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <textarea name="remarks" cols=60 rows=3><?print($remarks)?></textarea>
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>interne Bemerkungen:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
	            <textarea name="intern_remarks" cols=60 rows=3><?print($intern_remarks)?></textarea>
                </td>
	        </tr>
	        <tr height=30>
	            <td class=form_header></td>
                <td class=form_header width=100>Camptyp:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
		            <input type="radio" name="campinfo" value="7" <?if ($campinfo==7) echo "checked"?>> Tagescamp
		            <input type="radio" name="campinfo" value="8" <?if ($campinfo==8) echo "checked"?>> Halbtagescamp
		            <input type="radio" name="campinfo" value="9" <?if ($campinfo==9) echo "checked"?>> N&auml;chtigungscamp
                </td>
	        </tr>
	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>Wochentage des Camps:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
					<input type="text" size="100" name="campdays" maxlength="200" class="input_text" value="<?print($campdays)?>">
                </td>
	        </tr>
	        <tr height=30>
	            <td class=form_header></td>
                <td class=form_header width=100>Standortleiter:</td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
<?
		if (isset($id) || ($id>0))
		{
			$sql_mitarbeiter_select="select a.lastname,a.firstname from employees a where a.id='$campleiter'";
			$rs_mitarbeiter_select=getrs($sql_mitarbeiter_select,$print_debug,$print_error);
			LIST($campleiter_lastname,$campleiter_firstname)=$rs_mitarbeiter_select -> fetch_row();
		}
?>
					<input type="hidden" name="<? print("campleiter".$i."_new_value") ?>" value="true">  
					<select style='width:200px;'  id="<? print("combo_zone_std".$i) ?>" name="campleiter">
						<option selected value="<? print($campleiter)?>"><? print($campleiter_lastname.",".$campleiter_firstname)?></option>
					</select>
		
					<script>
						var z=new dhtmlXCombo("<? print("combo_zone_std".$i) ?>","campleiter",1);
						z.enableFilteringMode(true,"codebase/loadCombo.php?table=1&db=<?print($used_db)?>",true,true);
						z.onBlur="alert()";
					</script>
                </td>
	        </tr>

	        <tr height=30>
                <td class=form_header></td>
                <td class=form_header width=100>Online-Anmeldung verf&uuml;gbar: </td>
	            <td height=30 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
                <td width=10></td>
                <td>
<?
        if (!isset($id)) $online_visible=1;
	if ($online_visible=="1") 
	{ ?>
            <input type="checkbox" name="online_visible" value="1" checked>
<? } else { ?>
            <input type="checkbox" name="online_visible" value="1">
<? } ?>
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
		<input type="text" size=3 name="newdates" class="input_text" value="0">
		<input type="hidden" name="alte_anzahl" value="<?print($anzahl_termine)?>">
	    <a href="javascript:newDate()" class="text_link">Camptage hinzuf&uuml;gen und neu z&auml;hlen</a> 
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
       <table width=98% cellpadding="0" cellspacing="0" border=1>
       <tr>
	       <td colspan=3 width=100>Standardwerte:
<!--				<input type="button" onclick="autocount()" name="autocount_button" value="Autocount">-->
		   </td>
           <td>
<?
    // Zeitangaben formatieren //
	$time = new DateTime($uhrzeit_0);
?>
	          <input type="text" size=5 class="input_text" name=uhrzeit_0 onchange="isZeit(this,0);refreshTime(this)" value="<?print($time->format('H:i'))?>">
           </td>
           <td>
<?
    // Zeitangaben formatieren //
	$time_end = new DateTime($uhrzeit_ende_0);
?>
	          <input type="text" size=5 class="input_text" name=uhrzeit_ende_0 onchange="isZeit(this,0);refreshTimeEnd(this)" value="<?print($time_end->format('H:i'))?>">
           </td>
           <td>
 	          <input type="text" size=5 class="input_text" name=dauer_0 onchange="isZahl(this,0,'Die Dauer');refreshDuration(this)" value="<?print(number_format($dauer_0,2))?>">
		   </td>
<?

// Standard-Mitarbeiter //
for ($i=0;$i<=$maxcount_mitarbeiter;$i++) 
{
	if (($i==0) || ($i % 2 <>0))
	{
?>
           <td>
<?
	}
	if ($i==0) { print("Haupt-Campleiter:"); }
	if (($standard_mitarbeiter[$i]<>0) || ($standard_mitarbeiter[$i]<> ""))
	{
		$sql_mitarbeiter_select="select a.lastname,a.firstname,b.value from employees a left join hourcosts b on a.id=b.employee_id and b.products_id='$products_id' where a.id=".$standard_mitarbeiter[$i];
		$rs_mitarbeiter_select=getrs($sql_mitarbeiter_select,$print_debug,$print_error);
		LIST($std_mitarbeiter[$i][lastname],$std_mitarbeiter[$i][firstname],$std_mitarbeiter[$i][hourcost])=$rs_mitarbeiter_select -> fetch_row();
	}
?>	
			<table border=0>
			<tr><td>
				<input type="hidden" name="<? print("standard_mitarbeiter".$i."_new_value") ?>" value="true">  
				<select style='width:200px;'  id="<? print("combo_zone_std".$i) ?>" name="standard_mitarbeiter<?print($i)?>">
					<option selected value="<? print($standard_mitarbeiter[$i])?>"><? print($std_mitarbeiter[$i][lastname].",".$std_mitarbeiter[$i][firstname]." | ".$std_mitarbeiter[$i][hourcost])?></option>
				</select>
	
				<script>
					var z=new dhtmlXCombo("<? print("combo_zone_std".$i) ?>","standard_mitarbeiter[<?print($i)?>]",1);
					z.enableFilteringMode(true,"codebase/loadCombo.php?table=1&product=<?print($products_id)?>&db=<?print($used_db)?>",true,true);
					z.onBlur="alert()";
				</script>
			</td>
			<td>
				<input type="button" value="change" onclick="refreshLeader<?print($i)?>(standard_mitarbeiter<?print($i)?>)">
			</td>
			</tr></table>			
<?
	if ($i % 2 == 0)
	{
?>
		  </td>
<?
	} else
	{ print ("<br>"); }
} //Ende for ?>
		</tr>
        <tr>
            <td colspan=100 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
        </TR>

<? // ￿erschriften // ?>

		<tr>
	        <td colspan=100 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
        </TR>
        <tr>
	        <td width=25>del?</td>
	        <td width=5>#</td>
            <td>Camptermine</td>
            <td>Beginn</td>
            <td>Ende</td>
            <td>Einheit</td>
            <td colspan=20><table><tr><td>Mitarbeiter / </td><td>Stundensatz</td></tr></table>
        </tr>

<? // Daten des Kurses // ?>
<?
	for ($zeile=0;$zeile<=$anzahl_termine;$zeile++)
    { 
		// bei neuen n, m￿die Felder mit Werten gef￿erden
		if ($kurstermine[$zeile][zeit]=="") { $kurstermine[$zeile][zeit]="00:00"; }
		if ($kurstermine[$zeile][zeit_ende]=="") { $kurstermine[$zeile][zeit_ende]="00:00"; }
		if ($kurstermine[$zeile][dauer]=="") { $kurstermine[$zeile][dauer]="0.00"; }

	    // Datumsangaben formatieren //
		if ($kurstermine[$zeile][datum] == "0000-00-00") { $kurstermine[$zeile][datum]="";}

		if (($anzahl_termine>$alte_anzahl) && ($zeile>$alte_anzahl))
		{
			$kurstermine[$zeile][zeit] = $uhrzeit_0;
			$kurstermine[$zeile][zeit_ende] = $uhrzeit_ende_0;
			$kurstermine[$zeile][dauer] = $dauer_0;
		}
		$date = new DateTime($kurstermine[$zeile][datum]);
		$time = new DateTime($kurstermine[$zeile][zeit]);
		$time_end = new DateTime($kurstermine[$zeile][zeit_ende]);
?>
        <tr height=10>
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
            <input type="<?if ($print_debug) {print("input");} else {print("hidden");}?>" size=4 name="kurstermine[<?print($zeile)?>][id]" value="<?print($kurstermine[$zeile][id])?>">
         </td>
         <td>
            <input type="text" size=10 class="input_text" onchange="isDatum(this,<?print($zeile+1)?>)" name="kurstermine[<?print($zeile)?>][datum]" value="<?print($date->format('d.m.Y'))?>">
         </td>
         <td>
            <input type="text" size=5 class="input_text" onchange="isZeit(this,<?print($zeile+1)?>)" name="kurstermine[<?print($zeile)?>][zeit]" value="<?print($time->format('H:i'))?>">
         </td>
         <td>
            <input type="text" size=5 class="input_text" onchange="isZeit(this,<?print($zeile+1)?>)" name="kurstermine[<?print($zeile)?>][zeit_ende]" value="<?print($time_end->format('H:i'))?>">
         </td>
         <td>
	        <input type="text" size=5 class="input_text" onchange="isZahl(this,<?print($zeile+1)?>,'Die Dauer')" name="kurstermine[<?print($zeile)?>][dauer]" value="<?print($kurstermine[$zeile][dauer])?>">
		</td>
<?
for ($i=0;$i<=$maxcount_mitarbeiter;$i++) 
{
?>
<?
	if (($i==0) || ($i % 2 <>0))
	{
?>
           <td>
<?
	}
	if (($kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id]<>0) || ($kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id]<> ""))
	{
		if ($update==1)
		{
			$sql_mitarbeiter_select="select a.lastname,a.firstname,b.value from employees a left join hourcosts b on a.id=b.employee_id and b.products_id='$products_id' where a.id=".$kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id];
			$rs_mitarbeiter_select=getrs($sql_mitarbeiter_select,$print_debug,$print_error);
			LIST($kurstermine[$zeile][mitarbeiter][$i][lastname],$kurstermine[$zeile][mitarbeiter][$i][firstname],$kurstermine[$zeile][mitarbeiter][$i][std_hourcost])=$rs_mitarbeiter_select -> fetch_row();
			if ($kurstermine[$zeile][mitarbeiter][$i][hourcost]=="") {$kurstermine[$zeile][mitarbeiter][$i][hourcost] = $kurstermine[$zeile][mitarbeiter][$i][std_hourcost];}
			if ($kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id]=="0") {$kurstermine[$zeile][mitarbeiter][$i][hourcost]="";}
		}
/*		if (($kurstermine[$zeile][mitarbeiter][$i][hourcost]=="") || (intval($kurstermine[$zeile][mitarbeiter][$i][hourcost])==0)) 
		{
			$sql_mitarbeiter_select="select b.value from employees a left join hourcosts b on a.id=b.employee_id and b.products_id='$products_id' where a.id=".$kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id];
			$rs_mitarbeiter_select=getrs($sql_mitarbeiter_select,$print_debug,$print_error);
			LIST($kurstermine[$zeile][mitarbeiter][$i][hourcost])=$rs_mitarbeiter_select -> fetch_row();
		}
*/	}
		if (($anzahl_termine>$alte_anzahl) && ($zeile>$alte_anzahl))
		{
			$kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id]=$standard_mitarbeiter[$i];
			$kurstermine[$zeile][mitarbeiter][$i][lastname]=$std_mitarbeiter[$i][lastname];
			$kurstermine[$zeile][mitarbeiter][$i][firstname]=$std_mitarbeiter[$i][firstname];
//			echo ($anzahl_termine."-".$alte_anzahl."--".$zeile);
		}
?>
	    	<input type="hidden" size=3 name="kurstermine[<?print($zeile)?>][mitarbeiter][<?print($i)?>][table_id]" value="<?print($kurstermine[$zeile][mitarbeiter][$i][table_id])?>">
			<table border=0>
				<tr><td>
					<input type="hidden" name="kurstermine_mitarbeiter_<?print($zeile)?>_<?print($i)?>_new_value" value="true">  
					<select style='width:200px;'  id="<? print("combo_zone_".$zeile."_mitarbeiter".$i) ?>" name="kurstermine_mitarbeiter_<?print($zeile)?>_<?print($i)?>">
						<option selected value="<? print($kurstermine[$zeile][mitarbeiter][$i][mitarbeiter_id])?>"><? print($kurstermine[$zeile][mitarbeiter][$i][lastname].",".$kurstermine[$zeile][mitarbeiter][$i][firstname]." | ".$kurstermine[$zeile][mitarbeiter][$i][std_hourcost])?></option>
					</select>

				<script>
					var z=new dhtmlXCombo("<? print("combo_zone_".$zeile."_mitarbeiter".$i) ?>","kurstermine[<?print($zeile)?>][mitarbeiter][<?print($i)?>][mitarbeiter_id]",1);
					z.enableFilteringMode(true,"codebase/loadCombo.php?table=1&product=<?print($products_id)?>&db=<?print($used_db)?>",true,true);
					z.onBlur="alert();";
				</script>
				</td>
<?
	if (isAdmin() || isSecretary())
	{ ?>
            <td>
            <input type="text" size=7 onchange="isZahl(this,<?print($zeile+1)?>,'Der Stundensatz')" name="kurstermine[<?print($zeile)?>][mitarbeiter][<?print($i)?>][hourcost]" value="<?if ($kurstermine[$zeile][mitarbeiter][$i][hourcost]>0) {print($kurstermine[$zeile][mitarbeiter][$i][hourcost]);}?>">
			</td>
<?
	}
?>
			</tr></table>
<?
	if ($i % 2 == 0)
	{
?>
		  </td>
<?
	} else
	{ print ("<br>"); }
  
} // Ende for f￿arbeiter?>
     </tr>
<? 
} // Ende for f￿mine?>
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
if ($print_debug)
{
		print ($first_time."<br>");		
		print ($seventh_time."<br>");
}
?>						
</body>
</html>
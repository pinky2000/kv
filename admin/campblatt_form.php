<?
/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/* Dateiname: campblatt_form.php
*  Zweck: Formular zum Ausf�llen des campblattes
*/
date_default_timezone_set('Europe/Berlin');

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];

if ($print_debug==1) { $field_value="text"; } else { $field_value="hidden"; }

isAllow(isAdmin() || isSecretary() || isEmployee());

/* _POST einlesen */
$load_new=TRUE;
$save_x = $_POST['save_x'];
$update = $_POST['update'];

//var_dump($_POST);

$anzahl_termine = $_POST['anzahl_termine'];
$anzahl_kunden = $_POST['anzahl_kunden'];
$change_date = date('Y-m-d H:m:s');
$change_user = $_SESSION['username'];
$used_db = $_SESSION['used_db'];

$back_url="../admin/campblatt_list.php?sortcol=-4";


if ($update==1)
{
	$load_new=FALSE;
	$kurstermine=$_POST["kurstermine"];
	$kurskunden=$_POST["kurskunden"];
	$selected_termine = $_POST['selected_termine'];
	for ($count=0;$count<=$anzahl_kunden;$count++)
	{
		${"client_name".$count}=$_POST["client_name".$count];
	}
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<link href="../css/ta.css" type="text/css" rel="stylesheet">
	<title>Campblatt</title>
<style> 
.td_top {
    /* Rotate div */
    -ms-transform: rotate(90deg); /* IE 9 */
    -webkit-transform: rotate(90deg); /* Chrome, Safari, Opera */
    transform: rotate(90deg);
}
</style>
	
	
	<script src="codebase/dhtmlxcommon.js"></script>
	<script src="codebase/dhtmlxcombo.js"></script>
	<link rel="STYLESHEET" type="text/css" href="codebase/dhtmlxcombo.css">


	<script language="JavaScript" type="text/javascript">
	window.dhx_globalImgPath="codebase/imgs/";

	document.onkeydown = function(event) 
	{
		if (event.keyCode == 27) 
		{
			event.cancelBubble = true;
			event.returnValue = false;
			alert("Sie haben ESC gedrückt, bitte das Formular neu laden");
		}
		return event.returnValue;
	}
	
	function newClient()
	{
	    neu = window.document.campblatt.nc_anzahl.value;
		window.document.campblatt.anzahl_kunden.value=(neu*1)+(window.document.campblatt.anzahl_kunden.value*1);
		window.document.campblatt.update.value=1;
		window.document.campblatt.submit();
	}

	function changeDetail(wert)
	{
		window.document.campblatt.selected_termine.value=wert;
		window.document.campblatt.update.value=1;
		window.document.campblatt.submit();
	}

	function showDetails()
	{
		window.open("details_camp.php?kurs=<?print($id)?>","Fenster1","");
	}

	function show_message(){
		document.getElementById('light').style.display='block';
		document.getElementById('fade').style.display='block';
	}
	
	</script>
</head>

<?

if ((isset($save_x)) && (!$update))
{
	$load_new=FALSE;
	$marked_col=$_POST['marked_col'];
	$kurstermine=$_POST["kurstermine"];
	$kurskunden=$_POST["kurskunden"];
	$selected_termine = $_POST['selected_termine'];
	${"client_name".$count_kunden}=$_POST["client_name".$count_kunden];
	${"client_name".$count_kunden."_new_value"}=$_POST["client_name".$count_kunden."_new_value"];

/*	if (isEmployee()) 
	{
	$no_error = true && CheckEmpty($kurstermine[$marked_col][content],$error_content) &&
				CheckEmpty($kurstermine[$marked_col][remarks],$error_remarks) &&
				CheckEmpty($kurstermine[$marked_col][used_items],$error_useditems);
	}
	else
	{
*/
		$no_error=true;
//	}
//	$name_temp = explode(",",${"client_name".$count_kunden});
//	if (($name_temp[0]=="") || ($name_temp[1] == "")) $no_error=false;

?>

<?if (isset($save_x) && $no_error) $command="show_message();";?>
<?
if ((!$no_error)) { $command="alert('Bitte korrekte Eingabe der Felder beachten - Daten wurden NICHT gespeichert!');";}
if ($load_new) { $command="alert('Bitte Beachten bei der Eingabe: \n Die ESC Taste darf nicht gedrückt werden! \n Die Namen der Kinder IMMER mit NACHNAME,Vorname eingeben - auf den Beistrich achten !');";}
?>

<BODY onload="<?print($command)?>">
<!--  Div f�r messagebox und fade des Hintergrundes -->		
		<div id="light" class="white_content">
			<center>
			<b>&Auml;nderungen erfolgreich gespeichert!</b>
			<br>
			<a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
			</center>
		</div>
		<div id="fade" class="black_overlay"></div>

<?
  if (($no_error) || (($no_error) && isAdmin()))
  {
	if ($print_debug==1) {print("Termine:");var_dump($kurstermine);print("<br>Kunden:");var_dump($kurskunden);}

	for ($count_kunden=0;$count_kunden<=$anzahl_kunden;$count_kunden++)
	{
		${"client_name".$count_kunden}=$_POST["client_name".$count_kunden];
		${"client_name".$count_kunden."_new_value"}=$_POST["client_name".$count_kunden."_new_value"];

		if ($kurskunden[$count_kunden][del]=="")
		{		
//		  print("<br>".${"client_name".$count_kunden}."-".strlen(${"client_name".$count_kunden})."-".intval(${"client_name".$count_kunden})."<br>");
		  if (((strlen(${"client_name".$count_kunden})>1)) && (${"client_name".$count_kunden}!="NACHNAME,Vorname"))
		  {	
			/* ob Admin oder Mitarbeiter, wenn kunden_id=client_nameX, dann wurde der Kunde nicht ver�ndert */ 
			if ((intval($kurskunden[$count_kunden][kunden_id])==intval(${"client_name".$count_kunden})) && /*(${"client_name".$count_kunden."_new_value"}=="false") &&*/ (intval(${"client_name".$count_kunden})>0)) 
			{
				/* keine �nderung des Kunden...*/
				if ($print_debug==1) {print($count_kunden." - keine �nderung des Kunden...ID: ".$kurskunden[$count_kunden][kunden_id]."=".${"client_name".$count_kunden}."<br>");}
				$sql_payment_update="update payments set change_date='$change_date',change_user='$change_user' where clients_id=".$kurskunden[$count_kunden][kunden_id]." and courses_id=".$id;
               	$rs_payment_update=getrs($sql_payment_update,$print_debug,$print_error);

				if (!isEmployee())
				{
					$sql_payment_update="update payments set register='".$kurskunden[$count_kunden][register]."' where clients_id=".$kurskunden[$count_kunden][kunden_id]." and courses_id=".$id;
					$rs_payment_update=getrs($sql_payment_update,$print_debug,$print_error);
				}
			}
			else
			{
				/* Dieser Zweig kann nur von einem Admin erreicht werden, da beim Mitarbeiter, bei einer neuen Eingabe die 
			   	Variable client_nameX_new_value auf TRUE ist */
				if (($kurskunden[$count_kunden][kunden_id]=="") && (${"client_name".$count_kunden."_new_value"}=="false"))
				{
					/* neuer Kunde zum Kurs ...*/
					if ($print_debug==1) {print($count_kunden." - neuer Kunde zum Kurs...ID:".$kurskunden[$count_kunden][kunden_id]."!=".${"client_name".$count_kunden}."<br>");}
					$sql_last_element="select rechnung_id from payments order by rechnung_id desc limit 1";
					$rs_last_element=getrs($sql_last_element,$print_debug,$print_error);
					list($last_id)=$rs_last_element->fetch_row();
					$new_id=$last_id+1;
					$sql_payment_insert="insert into payments (rechnung_id,courses_id,clients_id,status,register,reg_date,reg_user,change_date,change_user) 
										 values ('$new_id','$id','${"client_name".$count_kunden}','F','".$kurskunden[$count_kunden][register]."','$change_date','$change_user','$change_date','$change_user')";
                	$rs_payment_insert=getrs($sql_payment_insert,$print_debug,$print_error);
                	$kurskunden[$count_kunden][payment_id]=$new_id;
					$sql_payment_opt_insert="insert into payments_opt_camps (rechnung_id) values ('$new_id')";
					$rs_payment_opt_insert=getrs($sql_payment_opt_insert,$print_debug,$print_error);
 
					if ($print_debug==1) {print("neue Zahlungen-id: ".$kurstermine[$count_kunden][payment_id]."-".$rs_payment_insert->insert_id);}
				}
				elseif (${"client_name".$count_kunden."_new_value"}=="true")
				{
					/* neuer Kunde in DB ...*/
					$name_temp = explode(",",${"client_name".$count_kunden});
					$kurskunden[$count_kunden][lastname] = ltrim(strtoupper($name_temp[0]));
					$kurskunden[$count_kunden][firstname] = ltrim(ucfirst($name_temp[1]));
					$current_kunden_id=$kurskunden[$count_kunden][kunden_id];
					
					/* Abfrage ob der eingegebene Kunde nicht doch schon in der DB vorhanden ist. */
					$sql_check_client_select="select id from clients where status='Aktiv' and lastname='".$kurskunden[$count_kunden][lastname]."' and firstname='".$kurskunden[$count_kunden][firstname]."'";
                	$rs_check_client_select=getrs($sql_check_client_select,$print_debug,$print_error);
					if ($rs_check_client_select->num_rows)
					{
						/* Wenn es einen Kunden mit dem Namen schon gibt, dann soll dieser verwendet werden. */ 
						LIST($checked_client_id)=$rs_check_client_select -> fetch_row();
						if ($print_debug==1) {print("Kunde gibts doch...".$checked_client_id);}
						$kurskunden[$count_kunden][kunden_id] = $checked_client_id; 					
					}
					else
					{
						/* Wenn es keinen Eintrag in der DB gibt, dann wird neuer Kunde abgespeicher */
						if ($print_debug==1) {print($count_kunden." - neuer Kunde in DB...ID:".$kurskunden[$count_kunden][kunden_id]."!=".${"client_name".$count_kunden}."<br>");}
						$sql_new_client_insert="insert into clients (firstname,lastname,status,school_id,birthdate) values ('".$kurskunden[$count_kunden][firstname]."','".$kurskunden[$count_kunden][lastname]."','Aktiv',341,'2000-01-01')";
                		$rs_new_client_insert=getrs($sql_new_client_insert,$print_debug,$print_error);
                		$kurskunden[$count_kunden][kunden_id]=mysqli_insert_id($DB_TA_CONNECT);
						if ($print_debug==1) {print("neue Kunden-id: ".$kurskunden[$count_kunden][kunden_id]."-".$rs_new_client_insert->insert_id."<br>");}
					}
					if (($current_kunden_id=="") || ($current_kunden_id==0))
					{
						if ($print_debug==1) {print($count_kunden." - neuer Kunde zum Kurs...ID:".$kurskunden[$count_kunden][kunden_id]."!=".${"client_name".$count_kunden}."<br>");}
						$sql_last_element="select rechnung_id from payments order by rechnung_id desc limit 1";
						$rs_last_element=getrs($sql_last_element,$print_debug,$print_error);
						list($last_id)=$rs_last_element->fetch_row();
						$new_id=$last_id+1;
						$sql_payment_insert="insert into payments (rechnung_id,courses_id,clients_id,status,register,reg_date,reg_user,change_date,change_user) 
											 values ('$new_id','$id','".$kurskunden[$count_kunden][kunden_id]."','F','".$kurskunden[$count_kunden][register]."','$change_date','$change_user','$change_date','$change_user')";
						$rs_payment_insert=getrs($sql_payment_insert,$print_debug,$print_error);
						$kurskunden[$count_kunden][payment_id]=$new_id;
						$sql_payment_opt_insert="insert into payments_opt_camps (rechnung_id) values ('$new_id')";
						$rs_payment_opt_insert=getrs($sql_payment_opt_insert,$print_debug,$print_error);
						if ($print_debug==1) {print("neue Zahlungen-id: ".$kurskunden[$count_kunden][payment_id]."-".$rs_payment_insert->insert_id);}
					} else
					{
						/* Kunde wurde mit neuem Kunden ausgetauscht ...*/
						if ($print_debug==1) {print($count_kunden." - Kunde wurde durch neuen getauscht...ID:".$kurskunden[$count_kunden][kunden_id]."!=".${"client_name".$count_kunden}."<br>");}
						$sql_payment_update="update payments set clients_id='".$kurskunden[$count_kunden][kunden_id]."',reg_date='$change_date',reg_user='$change_user',change_date='$change_date',change_user='$change_user',register='".$kurskunden[$count_kunden][register]."' where clients_id=".$current_kunden_id." and courses_id=".$id;
						$rs_payment_update=getrs($sql_payment_update,$print_debug,$print_error);
						}
				}
				else
				{
					/* Kunde wurde ge�ndert ...*/
					if ($print_debug==1) {print($count_kunden." - Kunde wurde ge&auml;ndert...ID:".$kurskunden[$count_kunden][kunden_id]."!=".${"client_name".$count_kunden}."<br>");}
					$sql_payment_update="update payments set clients_id='".${"client_name".$count_kunden}."',reg_date='$change_date',reg_user='$change_user',change_date='$change_date',change_user='$change_user',register='".$kurskunden[$count_kunden][register]."' where clients_id=".$kurskunden[$count_kunden][kunden_id]." and courses_id=".$id;
                	$rs_payment_update=getrs($sql_payment_update,$print_debug,$print_error);
					$kurskunden[$count_kunden][kunden_id]=${"client_name".$count_kunden};
				}	
			} // Ende �nderungsschleife
		  } // Falls ein leeres Kundenfeld im campblatt ist oder NACHNAME,Vorname drinnen steht, wird nichts gemacht...
		}
		else
		{
			/* L�schen des Kunden und dessen Eintr�ge */
		  	$sql_payment_del="update payments set status='Entfernt',reg_date='$change_date',reg_user='$change_user' where rechnung_id=".$kurskunden[$count_kunden][payment_id];
           	$rs_payment_del=getrs($sql_payment_del,$print_debug,$print_error);
		}
	} // Ende Kunden Schleife
	
	for ($count_termine=0;$count_termine<=$anzahl_termine;$count_termine++)
	{	
//		$sql_coursetimes_clients_del="delete from coursetimes_clients where coursetimes_id='".$kurstermine[$count_termine][id]."'";
//       	$rs_coursetimes_clients_del=getrs($sql_coursetimes_clients_del,$print_debug,$print_error);

		for ($count_kunden=0;$count_kunden<=$anzahl_kunden;$count_kunden++)
		{
			if (!isset($kurskunden[$count_kunden][del]))
			{
				if ($kurskunden[$count_kunden][checked][$count_termine][id]=="")
				{
					$sql_coursetimes_clients_insert="insert into coursetimes_clients(coursetimes_id,clients_id,value) values ('".$kurstermine[$count_termine][id]."','".$kurskunden[$count_kunden][kunden_id]."','".$kurskunden[$count_kunden][checked][$count_termine][wert]."')";
		       		$rs_coursetimes_clients_insert=getrs($sql_coursetimes_clients_insert,$print_debug,$print_error);
					$kurskunden[$count_kunden][checked][$count_termine][id]==mysqli_insert_id($DB_TA_CONNECT);
				} else
				{		
					$sql_coursetimes_clients_update="update coursetimes_clients set coursetimes_id='".$kurstermine[$count_termine][id]."' ,clients_id='".$kurskunden[$count_kunden][kunden_id]."' ,value='".$kurskunden[$count_kunden][checked][$count_termine][wert]."'  where id=".$kurskunden[$count_kunden][checked][$count_termine][id];
	       			$rs_coursetimes_clients_update=getrs($sql_coursetimes_clients_update,$print_debug,$print_error);
				}
			}	
		}

		$sql_coursetimes_update="update coursetimes set checked='".$kurstermine[$count_termine][checked]."' where id='".$kurstermine[$count_termine][id]."'";
		$rs_coursetimes_update=getrs($sql_coursetimes_update,$print_debug,$print_error);

		if ($marked_col==$count_termine)
		{
			$sql_coursetimes_update="update coursetimes set content='".htmlentities($kurstermine[$count_termine][content])."', remarks='".htmlentities($kurstermine[$count_termine][remarks])."',used_items='".htmlentities($kurstermine[$count_termine][used_items])."' where id='".$kurstermine[$count_termine][id]."'";
			$rs_coursetimes_update=getrs($sql_coursetimes_update,$print_debug,$print_error);
		}
//		print($kurstermine[$count_termine][checked]."<--".$kurstermine[$count_termine][checked_original]."<br>");
		if (($kurstermine[$count_termine][checked]=="on") && ($kurstermine[$count_termine][checked_original]==""))
		{
			$SQL_mail="select
			courses.products_id,
			courses.year,
			courses.timeperiods_id,
			courses.institutions_id,
			courses.locations_id,
			CONCAT(courses.products_id,courses.year,courses.timeperiods_id,courses.institutions_id,courses.locations_id,'-',courses.info)
				from
			courses
				where
			$id=courses.id";
	
			$rs=getrs($SQL_mail,$print_debug,$print_error);
			LIST($pid,$year,$tid,$iid,$lid,$kurs)=@mysql_fetch_row($rs);
			mail("camps@teamactivities.at","Campblatt Freigabe: ".$kurs." vom Mitarbeiter: ".$session_user,"Link zum Campblatt: http://kv.teamactivities.at/admin/campblatt_form.php?id=$id","From: db@teamactivities.at");
		}
	} // Ende Termin Schleife
	/* Ende "Save" */
	$update = 0;
  }
  else 
  { //Wenns beim Eingabecheck einen Fehler gab, dann soll das Formular mit den alten Werten geladen werden...//
	$update=1;
	for ($count=0;$count<=$anzahl_kunden;$count++)
	{
		${"client_name".$count}=$_POST["client_name".$count];
		if ($print_debug==1) var_dump(${"client_name".$count});
	}
  }
  $selected_termine=$marked_col;
}

if ($load_new) 
{ 
	$command="alert('Bitte um Beachtung vom Eingabeformat NACHNAME,Vorname, keine Leerzeichen und das Komma nicht vergessen!');";
?>

	<BODY onload="<?print($command)?>">
<? 
}?>


<form action="<?echo $PHP_SELF?>" method="post" name="campblatt">

<input type="hidden" name="id" value="<?print($id)?>">
<input type="hidden" name="update" value="0">

<table width="100%" cellspacing="1" cellpadding="1" border="0">
	<tr>
	    <td width="100%"></td>
	</tr>

<?
/* Die Grunddaten sollen immer geladen werden */	
$sql_campblatt_select = "SELECT
		a.name,
		e.address,
		e.city,
		e.zip,
		e.contactperson,
		e.phone1,
		e.phone2,
		b.standard_time,
		b.standard_durance,
		c.name,
		e.name,
		d.firstname,
		d.lastname,
		e.bild,
		e.link,
		b.remarks,
		b.locations_id,
		b.institutions_id,
		b.info AS 'Kurs',
		b.durance_desc,
		f.weekday,
		b.jbetrag,
		b.price,
		b.year,
		g.name,
		b.opt_nachmittag_auswahl,
		b.opt_lernmodul_auswahl,
		b.opt_verpflegung,
		b.opt_nachmittag,
		b.type
	FROM
		products a,
		institutions c,
		institutions e,
		weekdays f,
		timeperiods g, 
		courses b
	LEFT JOIN employees d on (b.standard_employee=d.id)
	WHERE
		b.id=$id and
		f.id=b.weekday and 
		b.products_id=a.id and
		b.locations_id=e.id and
		b.institutions_id=c.id and
		g.id=b.timeperiods_id";

$rs_campblatt_select=getrs($sql_campblatt_select,$print_debug,$print_error);
LIST($name,$address,$city,$zip,$kontakt,$tel1,$tel2,$time,$dauer,$schule,$austragung,$firstname,$lastname,$bild,$link,$remarks,$iid,$aid,$cinfo,$durance_desc,$tag,$jbetrag,$price,$course_year,$course_timeperiod,$opt_nachmittag,$opt_lernmodul,$verpflegung_preis,$nachmittag_preis,$type)=$rs_campblatt_select -> fetch_row();

$summe_verpflegung=0;
$summe_nachmittag=0;
$summe_bezahlt=0;
$summe_register=0;
$summe_flughafen_hin=0;
$summe_flughafen_ret=0;
$summe_flughafen_hin_minor=0;
$summe_flughafen_ret_minor=0;
$summe_bahnhof_hin=0;
$summe_bahnhof_ret=0;

if ($opt_nachmittag!="") { $is_nachmittag=1; }
if ($opt_lernmodul!="") { $is_lernmodul=1; }

/* Direktes Laden aus der Datenbank bei neuem Aufruf des Formulars oder nach Speichern */
// Ermittlung der initialen Anzahl der Termine und der Kunden

if (($id > 0) && ($update==0))
{
	$sql_kurstermine_select = "select id,date,content,remarks,used_items,checked from coursetimes where courses_id='$id' order by date";
	$rs_kurstermine_select=getrs($sql_kurstermine_select,$print_debug,$print_error);

	$anzahl_termine=0;
	while($rs_kurstermine_select>0 && LIST($kurstermine[$anzahl_termine][id],$kurstermine[$anzahl_termine][datum],$kurstermine[$anzahl_termine][content],$kurstermine[$anzahl_termine][remarks],$kurstermine[$anzahl_termine][used_items],$kurstermine[$anzahl_termine][checked])=$rs_kurstermine_select -> fetch_row())
	{
		$kurstermine[$anzahl_termine][checked_original]=$kurstermine[$anzahl_termine][checked];
		$summe_termine[$anzahl_termine]=0;
		$anzahl_termine++;
	}
	$anzahl_termine--;
}
	if ($update==1) $anzahl_kunden_new=$anzahl_kunden;

	$sql_kunden_select="select
		a.rechnung_id,
		a.clients_id,
		b.lastname,
		b.firstname,
		b.phone1,
		b.phone2,
		a.status,
		d.short,
		a.register,
        a.reg_user,
		b.sex,
		b.sv_number,
		b.birthdate,
		e.sel_verpflegung,
		e.sel_nachmittag,
		e.sel_nachmittag_auswahl,
		e.sel_lernmodul,
		e.sel_lernmodul_auswahl,
		e.sel_zertifikat,
		e.sel_zertifikat_auswahl,
		b.campdaten_schwimmen,
		e.sel_flughafen_hin,
		e.sel_flughafen_ret,
		e.sel_flughafen_hin_minor,
		e.sel_flughafen_ret_minor,
		e.sel_bahnhof_hin,
		e.sel_bahnhof_ret
	from
		payments a,
		payments_opt_camps e,
		clients b,
		courses c,
		timeperiods d
	where
		a.courses_id=$id and e.rechnung_id=a.rechnung_id and a.status<>'Entfernt' and
		a.clients_id=b.id and a.courses_id=c.id and c.timeperiods_id=d.id and b.status='Aktiv'
	group by b.id 
	order by b.lastname asc";

	$rs_kunden_select = getrs($sql_kunden_select,$print_debug,$print_error);
	$anzahl_kunden=0;
	while($rs_kunden_select>0 && LIST($kurskunden[$anzahl_kunden][payment_id],$kurskunden[$anzahl_kunden][kunden_id],$kurskunden[$anzahl_kunden][lastname],$kurskunden[$anzahl_kunden][firstname],
		 $kurskunden[$anzahl_kunden][phone1],$kurskunden[$anzahl_kunden][phone2],
		 $kurskunden[$anzahl_kunden][status],
		 $kurskunden[$anzahl_kunden][periode],
		 $kurskunden[$anzahl_kunden][register],
		 $kurskunden[$anzahl_kunden][reg_user],
		 $kurskunden[$anzahl_kunden][sex],
		 $kurskunden[$anzahl_kunden][sv_number],
		 $kurskunden[$anzahl_kunden][birthdate],
		 $kurskunden[$anzahl_kunden][verpflegung],
		 $kurskunden[$anzahl_kunden][nachmittag],
		 $kurskunden[$anzahl_kunden][nachmittag_auswahl],
		 $kurskunden[$anzahl_kunden][lernmodul],
		 $kurskunden[$anzahl_kunden][lernmodul_auswahl],
		 $kurskunden[$anzahl_kunden][zertifikat],
		 $kurskunden[$anzahl_kunden][zertifikat_auswahl],
		 $kurskunden[$anzahl_kunden][schwimmen],
		 $kurskunden[$anzahl_kunden][flughafen_hin],$kurskunden[$anzahl_kunden][flughafen_ret],
		 $kurskunden[$anzahl_kunden][flughafen_hin_minor],$kurskunden[$anzahl_kunden][flughafen_ret_minor],
		 $kurskunden[$anzahl_kunden][bahnhof_hin],$kurskunden[$anzahl_kunden][bahnhof_ret])=$rs_kunden_select -> fetch_row())
	{
		$kurskunden[$anzahl_kunden][lastname] = strtoupper($kurskunden[$anzahl_kunden][lastname]);
		$kurskunden[$anzahl_kunden][firstname] = ucfirst($kurskunden[$anzahl_kunden][firstname]);
		
		if ($kurskunden[$anzahl_kunden][verpflegung]>"0.00") { $summe_verpflegung ++;};
		if ($kurskunden[$anzahl_kunden][flughafen_hin]>"0.00") { $summe_flughafen_hin ++;};
		if ($kurskunden[$anzahl_kunden][flughafen_ret]>"0.00") { $summe_flughafen_ret ++;};
		if ($kurskunden[$anzahl_kunden][bahnhof_hin]>"0.00") { $summe_bahnhof_hin ++;};
		if ($kurskunden[$anzahl_kunden][bahnhof_ret]>"0.00") { $summe_bahnhof_ret ++;};
		if ($kurskunden[$anzahl_kunden][flughafen_hin_minor]>"0.00") { $summe_flughafen_hin_minor ++;};
		if ($kurskunden[$anzahl_kunden][flughafen_ret_minor]>"0.00") { $summe_flughafen_ret_minor ++;};
		if ($kurskunden[$anzahl_kunden][nachmittag]>"0.00") { $summe_nachmittag ++;};
		if ($kurskunden[$anzahl_kunden][status]=="E") { $summe_bezahlt ++;};
		if ($kurskunden[$anzahl_kunden][register]=="on") { $summe_register ++;};
		if ($kurskunden[$anzahl_kunden][zertifikat]=="on") { $summe_zertifikat ++;};
		if ($kurskunden[$anzahl_kunden][schwimmen]>0) { $summe_schwimmer ++;};
		
		$m1_trim=trim($opt_nachmittag,";");
		$m1_split=split(";",$m1_trim);  // Auswahl der Möglichkeiten aus dem Kurs ermitteln //

		$m1_trim_selected=trim($kurskunden[$anzahl_kunden][nachmittag_auswahl],";");     // Ausgewählte Optionen zählen //
		$m1_split_selected=split(";",$m1_trim_selected);
		
		for ($i=0;$i<count($m1_split);$i++)
		{
			$summe_m1[$i]=0;
		}
		for ($i=0;$i<count($m1_split);$i++)
		{
			for ($ii=0;$ii<count($m1_split_selected);$ii++)
			{
				if (strcmp(trim($m1_split_selected[$ii]),trim($m1_split[$i]))==0)
				{
					$summe_m1[$i]++;   // Wenn die ausgewählte Option einer möglichen Option entspricht, dann wird der Zähler um 1 erhöht //
				}
			}
		}

		$m2_trim=trim($opt_lernmodul,";");
		$m2_split=split(";",$m2_trim);  // Auswahl der Möglichkeiten aus dem Kurs ermitteln //

		$m2_trim_selected=trim($kurskunden[$anzahl_kunden][lernmodul_auswahl],";");     // Ausgewählte Optionen zählen //
		$m2_split_selected=split(";",$m2_trim_selected);
		
		for ($i=0;$i<count($m2_split);$i++)
		{
			$summe_m2[$i]=0;
		}
		for ($i=0;$i<count($m2_split);$i++)
		{
			for ($ii=0;$ii<count($m2_split_selected);$ii++)
			{
				if (strcmp(trim($m2_split_selected[$ii]),trim($m2_split[$i]))==0)
				{
					$summe_m2[$i]++;   // Wenn die ausgewählte Option einer möglichen Option entspricht, dann wird der Zähler um 1 erhöht //
				}
			}
		}

	  if (($id > 0) && ($update==0))
	  {
		
		$sql_checked_select="select a.id,a.value,b.date from coursetimes_clients a,coursetimes b where b.courses_id=$id and b.id=a.coursetimes_id and a.clients_id=".$kurskunden[$anzahl_kunden][kunden_id]." group by b.id order by b.date";
		$rs_checked_select = getrs($sql_checked_select,$print_debug,$print_error);
		$anzahl_checked=$rs_checked_select -> num_rows;
		for ($count_termine=0;$count_termine<=$anzahl_termine;$count_termine++)
		{
			list($kurskunden[$anzahl_kunden][checked][$count_termine][id],
				 $kurskunden[$anzahl_kunden][checked][$count_termine][wert],
				 $kurskunden[$anzahl_kunden][checked][$count_termine][datum])=$rs_checked_select -> fetch_row();

  		    if ($kurskunden[$anzahl_kunden][checked][$count_termine][wert]!="") { $summe_termine[$count_termine]++;}
		}
	  }
	  $anzahl_kunden ++;
	}
	$anzahl_kunden --;

	if ($update==1)
	{
		$anzahl_kunden=$anzahl_kunden_new;
	}
	/*
else
{
	$sql_kunden_select="select
		a.rechnung_id,
		a.clients_id,
		b.lastname,
		b.firstname,
		b.phone1,
		b.phone2,
		a.status,
		d.short,
		a.register,
                a.reg_user
	from
		payments a,
		clients b,
		courses c,
		timeperiods d
	where
		a.courses_id=$id and a.status<>'Entfernt' and
		a.clients_id=b.id and a.courses_id=c.id and c.timeperiods_id=d.id and b.status='Aktiv'
	group by b.id 
	order by b.lastname asc";

	$count_kunden=0;
	$rs_kunden_select = getrs($sql_kunden_select,$print_debug,$print_error);
	while($rs_kunden_select>0 && LIST($kurskunden[$count_kunden][payment_id],$kurskunden[$count_kunden][kunden_id],$kurskunden[$count_kunden][lastname],$kurskunden[$count_kunden][firstname],$kurskunden[$count_kunden][phone1],$kurskunden[$count_kunden][phone2],$kurskunden[$count_kunden][status],$kurskunden[$count_kunden][periode],$kurskunden[$count_kunden][register],$kurskunden[$count_kunden][reg_user])=$rs_kunden_select -> fetch_row())
	{
		$kurskunden[$count_kunden][lastname] = strtoupper($kurskunden[$count_kunden][lastname]);
		$kurskunden[$count_kunden][firstname] = ucfirst($kurskunden[$count_kunden][firstname]);

/*		$sql_checked_select="select a.id,a.value,b.date from coursetimes_clients a,coursetimes b where b.courses_id=$id and b.id=a.coursetimes_id and a.clients_id=".$kurskunden[$count_kunden][kunden_id]." group by b.id order by b.date";
		$rs_checked_select = getrs($sql_checked_select,$print_debug,$print_error);
		$count_checked=$rs_checked_select -> num_rows;
		for ($count_termine=0;$count_termine<=anzahl_termine;$count_termine++)
		{
			list($kurskunden[$count_kunden][checked][$count_termine][id],$kurskunden[$count_kunden][checked][$count_termine][wert],$kurskunden[$count_kunden][checked][$count_termine][datum])=$rs_checked_select -> fetch_row();
		}
		$count_kunden ++;
	}
}
*/
?>


<tr>
<td class="form_header">
	<table width=1100 border=0 class="form_header">
	<tr class="form_header">
		<td valign="top" class="form_header" width=20%>
			<div align=left class="top">
				<a class="text"><font size=+1><b>TEAM ACTIVITIES</font> </a></b><br>
				A-1120 Wien, Stachegasse 17/1<br>
				Tel./Fax: (+431) 786 67 39<br>
				e-mail: info@teamactivities.at<br>
				web: www.teamactivities.at<br>
				<br>
				Buero: 0650 / 786 67 39<br>
				Ute: 0664 24 16 174 (psychologischer Rat)<br>
				<br>
			</div>
		</td>
		<td width="15%" class="form_header" align="center">
			<a href="../downloads/Datenbankeinschulung.pdf" target=_blank>Wie benutze ich die DB?</a><br><br>
			<input type="button" value="Tagesinfos" onclick="javascript:window.open('details_camp.php?id=<?print($id)?>&iid=<?print($iid)?>&aid=<?print($aid)?>')" target="_blank"> <br><br>
			<input type="button" value="Personeninfos" onclick="javascript:window.open('personen_camp.php?id=<?print($id)?>')" target="_blank"><br><br>
		</td>
		<td valign="top" class="form_header" width=15%>
			<div align=left class="text">
			<table width="100%">
			<tr><td>
				Camp: <br>
				Campinfo: <br>
				Termin: <br>
			</td></tr>
			</table>
				Campleiter: <br>
				Austragungsort: <br>
				Adresse: <br>
				Ansprechpartner:<br>
				Tel: <br>
		<?if ($link) {?>
			Link:<br>
		<?}?>
		<?if ($bild) {?>
			Bild:
		<?}?>
				Preis:<br>
			</div>
		</td>
		<td valign="top"  class="form_header" width=50%>
			<div align=left class="text">
			<table width="100%">
			<tr><td bgcolor="#ffff00">
				<b>
				<? $tt=explode(":",$time);?>
				<?echo $name." - ".$course_year." - ".$course_timeperiod?> <br>
				<?echo $cinfo?><br>
				<?$date_1 = new DateTime($kurstermine[0][datum]);
				  $date_2 = new DateTime($kurstermine[$anzahl_kunden][datum]);

				echo $date_1->format('d.m.Y')." - ".$date_2->format('d.m.Y')?><br>
			</td></tr>
			</table>
				<?echo $firstname." ".strtoupper($lastname)?><br>
				<?echo $austragung?><br>
				<?echo $address.", ".$zip." ".$city?><br>
				<?echo $kontakt?><br>
				<?echo $tel1." - ".$tel2?><br>
				<?if ($link) { echo "<a href=http://$link target=_blank>$link</a><br>"; }?>
				<?if ($bild) { echo "<a href=../images/$bild target=_blank>$bild.</a><br>"; }?>
				<?echo $price." (Verpflegung: ".$verpflegung_preis." - Nachmittag: ".$nachmittag_preis.")"?><br>
				</b>
			</div>
		</td>
	</tr>
	</table>
</td>
</tr>

<tr>
	<td>
<input type="<?print($field_value)?>" name="anzahl_kunden" value="<?print($anzahl_kunden)?>">
<input type="<?print($field_value)?>" name="anzahl_termine" value="<?print($anzahl_termine)?>">
<? if (!isEmployee())
{
?>
	<input type="button" value="Campverwaltung oeffnen" onclick="javascript:window.open('camp_form.php?id=<?print($id)?>')" target=_blank>
<? } ?>
<?
// �berschriften der Tabelle //
?>
<table border=1 width=100%>
<tr>
<td>
	<table width=200>
	<tr>
		<td width=10>Nr.<br>
		<?if (!IsEmployee()) echo "del?";?></td>
		<td width=190 class="text">Teilnehmer:<br><font size=-1>(Nachname,Vorname)</font></td>
	</tr>
	</table>
</td>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Angemeldet
	</a>
</td>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Bezahlt
	</a>
</td>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Geschlecht
	</a>
</td>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Alter
	</a>
</td>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Schwimmmer
	</a>
</td>
<?
if (($type==9) || ($type==7))    // Nächtigungscamps
{
?>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Verpflegung
	</a>
</td>
<?
}
if($type==9)    // Nächtigungscamps
{
?>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Transfer Flughafen hin/ret
	</a>
</td>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Transfer Flughafen minor hin/ret
	</a>
</td>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		Transfer Bahnhof hin/ret
	</a>
</td>

<?}?>
<?
if($is_nachmittag==1)
{ 		
$m1_trim=trim($opt_nachmittag,";");
$m1_split=split(";",$m1_trim);  // Auswahl der Möglichkeiten aus dem Kurs ermitteln //
for ($i=0;$i<count($m1_split);$i++)   // Zeige alle möglichen Nachmittagsangebote an //
{ ?>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		<?print($m1_split[$i]);?>
	</a>
</td>
<?}
}?>
<? 		
if ($is_lernmodul)
{
$m2_trim=trim($opt_lernmodul,";");
$m2_split=split(";",$m2_trim);  // Auswahl der Möglichkeiten aus dem Kurs ermitteln //
for ($i=0;$i<count($m2_split);$i++)  // Zeige alle möglichen Lernmodule an //
{ ?>
<td width=2%>
	<a style="writing-mode: vertical-rl;">
		<?print($m2_split[$i]);?>
	</a>
</td>
<?}
}?>

<? /// Auflistung des Datums in Spalten
$old_termin_checked=""; // Info, ob vorherige Spalte freigegeben wurde
$marked_col=0; // Marker zum markieren des farbigen Feldes
if (!isset($selected_termine)) {$selected_termine=-1;}
for ($count_termine=0;$count_termine<=$anzahl_termine;$count_termine++)
{
		if (($kurstermine[$count_termine][checked]=="") && (($old_termin_checked == "on") || $count_termine==0)) $marked_col=$count_termine;
		if (($count_termine==$anzahl_termine) && ($kurstermine[$count_termine][checked]=="on")) $marked_col=$count_termine;
		if ($kurstermine[$count_termine][datum] == "0000-00-00") { $kurstermine[$count_termine][datum]="1980-05-02";}
		if (isset($save_x)) $selected_termine=$marked_col;
		$date = new DateTime($kurstermine[$count_termine][datum]);
?>
	<td <?if ($marked_col==$count_termine) { echo" bgcolor=#00ff00 "; } elseif ($selected_termine==$count_termine) { echo" bgcolor=#00ffff "; }?> width="<?echo ((100/($anzahl_termine+1.5))."%")?>" align="center">
	<a style="writing-mode: tb-rl;" onclick="javascript:changeDetail(<?print($count_termine)?>);"><? echo($date->format('d.m.Y'))?></a>
	<input type="<?if($print_debug==1) {print('text');} else {print('hidden');}?>" size=0 name="kurstermine[<?print($count_termine)?>][id]" value="<?echo($kurstermine[$count_termine][id])?>">
	<input type="hidden" size=0 name="kurstermine[<?print($count_termine)?>][datum]" value="<?echo($kurstermine[$count_termine][datum])?>"></td>
<?
		$old_termin_checked = $kurstermine[$count_termine][checked];
}
if ((!$update) &&($selected_termine==0)) {$selected_termine=$marked_col;}

?>
</tr>
<? // Summenzeile einfügen //?>
<tr>
<td>
	<table width=200>
	<tr>
		<td width=10></td>
		<td width=190 class="text">Summen:</td>
	</tr>
	</table>
</td>
<td width=1%>
		<?print($summe_register);?>
</td>
<td width="1%">
		<?print($summe_bezahlt);?>
</td>
<td></td>
<td></td>
<td>
		<?print($summe_schwimmer);?>
</td>
<?
if (($type==9) || ($type==7))    // Nächtigungscamps oder Tagescamps
{
?>
<td width="1%">
		<?print($summe_verpflegung);?>
</td>
<?
}
if($type==9)    // Nächtigungscamps
{
?>
<td width="1%">
		<?print($summe_flughafen_hin."/".$summe_flughafen_ret);?>
</td>
<td width="1%">
		<?print($summe_flughafen_hin_minor."/".$summe_flughafen_ret_minor);?>
</td>
<td width="1%">
		<?print($summe_bahnhof_hin."/".$summe_bahnhof_ret);?>
</td>
<?}?>
<?
if ($is_nachmittag)
{ 		
for ($i=0;$i<count($m1_split);$i++)   // summe der ausgewählten Nachmittagsangebote //
{ ?>
<td width="1%">
		<?print($summe_m1[$i]);?>
</td>
<?}
}?>
<? 		
if ($is_lernmodul)
{
for ($i=0;$i<count($m2_split);$i++)  // Summe der ausgewählten Lernmodule //
{ ?>
<td width="1%">
		<?print($summe_m2[$i]);?>
</td>
<?}
}?>

<?
for ($count_termine=0;$count_termine<=$anzahl_termine;$count_termine++)
{ ?>
	<td align="center">
	<?print($summe_termine[$count_termine]);?>
	</td>
<?
}
?>
</tr>
<?
$cl=1;

$tabindex = 1;

for ($count_kunden=0;$count_kunden<=$anzahl_kunden;$count_kunden++)
{
?>
	<tr>
	  <td>
	  <table>
		<tr>
		<td align=center width=10>
			<?print($count_kunden+1)?><br>
<? 	if (!IsEmployee())
	{ ?>
			<input type="Checkbox" name="kurskunden[<?print($count_kunden)?>][del]">
<?	} else 
	{ // Laden von Tel-Nummern und Status jedesmal bei Formularladen aber NUR als Employee !!
		if ($kurskunden[$count_kunden][kunden_id]>0)
		{	
			$sql_kundendetail_select="select phone1,phone2 	from clients where id='".$kurskunden[$count_kunden][kunden_id]."'";
			$rs_kundendetail_select = getrs($sql_kundendetail_select,$print_debug,$print_error);
			List($kurskunden[$count_kunden][phone1],$kurskunden[$count_kunden][phone2])=$rs_kundendetail_select -> fetch_row();

			}
		
		if ($kurskunden[$count_kunden][payment_id]>0)
		{	
			$sql_kundendetail_select="select status,register 	from payments where rechnung_id='".$kurskunden[$count_kunden][payment_id]."'";
			$rs_kundendetail_select = getrs($sql_kundendetail_select,$print_debug,$print_error);
			List($kurskunden[$count_kunden][status],$kurskunden[$count_kunden][register])=$rs_kundendetail_select -> fetch_row();
		}
	}
	if ($kurskunden[$count_kunden][status]=="") { $kurskunden[$count_kunden][status]=="F"; }
?>
		</td>
<!-- Kundenspalte -->
		<td width=190 <?if (($kurskunden[$count_kunden][status]=="F") && (!IsEmployee())) { echo "bgcolor=red"; }?>>
			<input type="<?print($field_value)?>" name="kurskunden[<?print($count_kunden)?>][payment_id]" value="<? print($kurskunden[$count_kunden][payment_id])?>">
			<input type="<?print($field_value)?>" name="kurskunden[<?print($count_kunden)?>][kunden_id]" value="<? print($kurskunden[$count_kunden][kunden_id])?>">  
<!--			<input type="<?print($field_value)?>" name="kurskunden[<?print($count_kunden)?>][status]" value="<? print($kurskunden[$count_kunden][status])?>">  -->
	
<? 	if (IsEmployee())
	{
		if (($kurskunden[$count_kunden][lastname]=="") || ($kurskunden[$count_kunden][firstname]=="") || ($kurskunden[$count_kunden][lastname]=="NACHNAME"))
		{
			$kurskunden[$count_kunden][lastname]="NACHNAME";
			$kurskunden[$count_kunden][firstname]="Vorname";
			if (!isset(${"client_name".$count_kunden}))
			{
				${"client_name".$count_kunden} = $kurskunden[$count_kunden][lastname].",".$kurskunden[$count_kunden][firstname]; 
			}
?>
			<input type="text" tabindex="<?echo $tabindex?>" name="<? print("client_name".$count_kunden) ?>" value="<?print(${"client_name".$count_kunden})?>">  
			<input type="<?print($field_value)?>" name="<? print("client_name".$count_kunden."_new_value") ?>" value="true">  
<? 			$tabindex++;     ?>
			<input type="<?print($field_value)?>"  name="kurskunden[<?print($count_kunden)?>][lastname]" value="<? print($kurskunden[$count_kunden][lastname])?>">
			<input type="<?print($field_value)?>" name="kurskunden[<?print($count_kunden)?>][firstname]" value="<? print($kurskunden[$count_kunden][firstname])?>">
<?		}
		else
		{		
?>			
			<a class="headline1"><? print($kurskunden[$count_kunden][lastname].",".$kurskunden[$count_kunden][firstname])?></a><br>
<!--			<input type="<?print($field_value)?>" name='<? print("client_name".$count_kunden."_new_value") ?>' value="false">  
			<input type="<?print($field_value)?>"  name="kurskunden[<?print($count_kunden)?>][lastname]" value="<? print($kurskunden[$count_kunden][lastname])?>">
			<input type="<?print($field_value)?>" name="kurskunden[<?print($count_kunden)?>][firstname]" value="<? print($kurskunden[$count_kunden][firstname])?>">
			<input type="<?print($field_value)?>" name='<? print("client_name".$count_kunden) ?>' value="<? print($kurskunden[$count_kunden][kunden_id])?>">  
-->
<?		
		}
		if (($kurskunden[$count_kunden][phone1]=="") && ($kurskunden[$count_kunden][phone2]=="")) 
		{ echo "<font color=blue size=-1>Keine Telnummern!!</font>"; } 
		else {
?>
				<a class="top"><? print($kurskunden[$count_kunden][phone1]."|".$kurskunden[$count_kunden][phone2])?></a>
<!--				<input type="<?print($field_value)?>"  name="kurskunden[<?print($count_kunden)?>][phone1]" value="<? print($kurskunden[$count_kunden][phone1])?>">
				<input type="<?print($field_value)?>" name="kurskunden[<?print($count_kunden)?>][phone2]" value="<? print($kurskunden[$count_kunden][phone2])?>">
-->
				<?
		}
	} 
	else // Administrator und Verwaltungsebene 
	{ 
		if (($update) && ($kurskunden[$count_kunden][kunden_id]=="")) 
		{
			if ((isset(${"client_name".$count_kunden})) && (is_numeric(${"client_name".$count_kunden})))
			{
				$kurskunden[$count_kunden][kunden_id]=${"client_name".$count_kunden};
				$sql_client_select="select lastname,firstname from clients where id=".${"client_name".$count_kunden};
           		$rs_client_select=getrs($sql_client_select,$print_debug,$print_error);
				LIST($kurskunden[$count_kunden][lastname],$kurskunden[$count_kunden][firstname])=$rs_client_select -> fetch_row();
			}
		}
?>
			<select style='width:200px;'  onkeydown="javascript:function(event){if (event.keyCode=27){event.cancelBubble=true;event.returnValue=false;alert('yes');}return event.returnValue;}" id="<? print("combo_zone".$count_kunden) ?>" name="<? print("client_name".$count_kunden) ?>">
					<option selected value="<? print($kurskunden[$count_kunden][kunden_id])?>"><? print($kurskunden[$count_kunden][lastname].",".$kurskunden[$count_kunden][firstname])?></option>
			</select>
			<input type="text" style="width:0px;display:'none';" name="help">
	
	 		<script>
				var z=new dhtmlXCombo("<? print("combo_zone".$count_kunden) ?>","<? print("client_name".$count_kunden) ?>",200);
				z.enableFilteringMode(true,"codebase/loadCombo.php?db=<?print($used_db)?>",true,true);
				z.onBlur="alert()";
				z.onkeydown = function(event) 
				{
					if (event.keyCode == 27) 
					{
						event.cancelBubble = true;
						event.returnValue = false;
						alert("Sie haben ESC gedrückt");
					}
					return event.returnValue;
				}
			</script>

			<input type="<?print($field_value)?>" size=20 name="kurskunden[<?print($count_kunden)?>][lastname]" value="<? print($kurskunden[$count_kunden][lastname])?>">
<? 			$tabindex++;     ?>
			<input type="button" value="-P>" onclick="javascript:window.open('client_form.php?id=<?print($kurskunden[$count_kunden][kunden_id])?>')" target=_blank>
			<input type="<?print($field_value)?>" size=20 name="kurskunden[<?print($count_kunden)?>][firstname]" value="<? print($kurskunden[$count_kunden][firstname])?>">
			<input type="button" value="-Z>" onclick="javascript:window.open('payment_form.php?id=<?print($kurskunden[$count_kunden][payment_id])?>')" target=_blank>
<font size=1>
<? echo "(".$kurskunden[$count_kunden][reg_user].")"; ?>
</font>
			<br>
			<input type="<?print($field_value)?>"  name="kurskunden[<?print($count_kunden)?>][phone1]" value="<? print($kurskunden[$count_kunden][phone1])?>">
			<input type="<?print($field_value)?>" name="kurskunden[<?print($count_kunden)?>][phone2]" value="<? print($kurskunden[$count_kunden][phone2])?>">
<? 
		if (($kurskunden[$count_kunden][phone1]=="") && ($kurskunden[$count_kunden][phone2]=="")) 
		{ 
			echo "<a href='client_form.php?id=".$kurskunden[$count_kunden][kunden_id]."' target=_blank><font color=blue size=-3>Keine Telnummern!!</a></font>";
		} else {?>
				<a class="top"><? print($kurskunden[$count_kunden][phone1]."|".$kurskunden[$count_kunden][phone2])?></a>
<?
		}
 	} //Ende Admin Abfrage?>
		</td>
		</tr>
		</table>
	  </td>
	  <!-- nächste Spalten -->
	  <td>
<? 
	if (IsEmployee()) 
	{
		if ($kurskunden[$count_kunden][register]=="on") 
		{ ?>
				<img src="../images/haken.gif">
<? 		} else { ?>
				<img src="../images/x.jpg">
<? 
		} ?>
<!--			<input type="<?print($field_value)?>" name="kurskunden[<?print($count_kunden)?>][register]" value="<? print($kurskunden[$count_kunden][register])?>">-->
<?
	} else {
	?>
	
	<input type="Checkbox" tabindex="<?echo $tabindex?>" name="kurskunden[<?print($count_kunden)?>][register]" <? if ($kurskunden[$count_kunden][register]=="on") echo "checked"?>>
<? $tabindex++;
	}?>
	  </td>
	  <td>
<? 	if ($kurskunden[$count_kunden][status]=="E") {?><img src="../images/haken.gif"><? } else { echo "&nbsp;"; }?>
	  </td>
	  <td>
<?		if ($kurskunden[$count_kunden][sex]=="0") { $geschlecht="W"; } else { $geschlecht="M"; } ?>
		<a class="top"><? print($geschlecht)?></a>
	  </td>
	  <td>
<?		$bd_split=split("-",$kurskunden[$count_kunden][birthdate]);
		$first_split=split("-",$kurstermine[0][datum]);
		$age_year=abs($bd_split[0]-$first_split[0]);
		$age_month=$first_split[1]-$bd_split[1];
		$age=$age_year+($age_month/12); ?>
		<a class="top"><? printf("%.1f",$age)?></a>
	  </td>
	  <td>
		<a style="writing-mode: tb-rl;">
<?
		switch($kurskunden[$count_kunden][schwimmen])
		{
			case 0: ?><img src="../images/x.jpg"><?;break;
			case 1: ?><img src="../images/haken.gif"><?;break;
			case 2: ?><img src="../images/haken.gif"><?;break;
		}
		?>
		<? print($schwimmen)?></a>
	  </td>
<?
if(($type==9)||($type==7))    // Nächtigungscamps oder Tagescamps
{
?>
	  <td width="1%">
<?		if ($kurskunden[$count_kunden][verpflegung]>"0.00") 
		{ ?>
				<img src="../images/haken.gif">
<? 		} else { ?>
				<img src="../images/x.jpg">
<? 
		} ?>
	   </td>
<?
}
if($type==9)    // Nächtigungscamps
{
?>
	  <td width="1%">
<?		if ($kurskunden[$count_kunden][flughafen_hin]>"0.00") 
		{ ?>
				<img src="../images/haken.gif">
<? 		} else { ?>
				<img src="../images/x.jpg">
<? 
		} ?>
		/
<?		if ($kurskunden[$count_kunden][flughafen_ret]>"0.00") 
		{ ?>
				<img src="../images/haken.gif">
<? 		} else { ?>
				<img src="../images/x.jpg">
<? 
		} ?>
	   </td>
	  <td width="1%">
<?		if ($kurskunden[$count_kunden][flughafen_hin_minor]>"0.00") 
		{ ?>
				<img src="../images/haken.gif">
<? 		} else { ?>
				<img src="../images/x.jpg">
<? 
		} ?>
		/
<?		if ($kurskunden[$count_kunden][flughafen_ret_minor]>"0.00") 
		{ ?>
				<img src="../images/haken.gif">
<? 		} else { ?>
				<img src="../images/x.jpg">
<? 
		} ?>
		</td>
	  <td width="1%">
<?		if ($kurskunden[$count_kunden][bahnhof_hin]>"0.00") 
		{ ?>
				<img src="../images/haken.gif">
<? 		} else { ?>
				<img src="../images/x.jpg">
<? 
		} ?>
		/
<?		if ($kurskunden[$count_kunden][bahnhof_ret]>"0.00") 
		{ ?>
				<img src="../images/haken.gif">
<? 		} else { ?>
				<img src="../images/x.jpg">
<? 
		} ?>
		</td>

<?}?>
<?
if ($is_nachmittag)
{ 		
for ($i=0;$i<count($m1_split);$i++)   // Checkboxen der ausgewählten Nachmittagsangebote //
{ ?>
	   <td width="1%">
	   
<?	
		$m1_trim_selected=trim($kurskunden[$count_kunden][nachmittag_auswahl],";");     // Ausgewählte Optionen zählen //
		$m1_split_selected=split(";",$m1_trim_selected);
for ($ii=0;$ii<count($m1_split_selected);$ii++)
	{
		if (strcmp(trim($m1_split_selected[$ii]),trim($m1_split[$i]))==0)
		{ ?>
			<img src="../images/haken.gif">
<? 		} else { ?>
			<img src="../images/x.jpg">
<?	}
}
?>
	   </td>
<?}
}?>
<?
if ($is_lernmodul)
{ 		
for ($i=0;$i<count($m2_split);$i++)  // Summe der ausgewählten Lernmodule //
{ ?>
<td width="1%">
<?
		$m2_trim_selected=trim($kurskunden[$count_kunden][lernmodul_auswahl],";");     // Ausgewählte Optionen zählen //
		$m2_split_selected=split(";",$m2_trim_selected);
			for ($ii=0;$ii<count($m2_split_selected);$ii++)
			{
				if (strcmp(trim($m2_split_selected[$ii]),trim($m2_split[$i]))==0)
				{ print($m2_split_selected[$ii]);?>
				<img src="../images/haken.gif">
<? 				} else { ?>
				<img src="../images/x.jpg">
<?				}
		}
?>
</td>
<?}
}?>
	  
<? 
// Kurstermine
	for ($count_termine=0;$count_termine<=$anzahl_termine;$count_termine++)
	{  
?>
			<td <?if ($marked_col==$count_termine) { echo" bgcolor=#00ff00 "; }?> align="center">  
			<input type="<?print($field_value)?>" size=3 name="kurskunden[<?print($count_kunden)?>][checked][<?print($count_termine)?>][id]" value="<?print($kurskunden[$count_kunden][checked][$count_termine][id])?>">
<?  
		if (($kurstermine[$count_termine][checked]=="on") && (IsEmployee()))
		{ ?> 
			<input type="hidden" size=3 name="kurskunden[<?print($count_kunden)?>][checked][<?print($count_termine)?>][wert]" value="<? echo $kurskunden[$count_kunden][checked][$count_termine][wert]?>">
<? 		
			if ($kurskunden[$count_kunden][checked][$count_termine][wert]=="on") 
			{?>
				<img src="../images/haken.gif"><? 
			} 
			else 
			{ ?><img src="../images/x.jpg"><?}?>
<?		} else {?>
			<input type="Checkbox"  name="kurskunden[<?print($count_kunden)?>][checked][<?print($count_termine)?>][wert]" <?if ($kurskunden[$count_kunden][checked][$count_termine][wert]=="on") echo"checked"?>>
<? 		} ?>
			</td>
<? 
	} ?>
	</tr>
<?	
}
?>
	<tr>
	<td align="center">
		<input type="text" size=3 value="1" name="nc_anzahl">
		<input type="button" value="Teilnehmer hinzuf&uuml;gen" onclick="javascript:newClient()">
	</td>
	<td colspan=50>
	</td>
	</tr>
	
	<tr>
	<td bgcolor="#ffff00">
		<a class="text_main"><b>Freigabe: </b></a>
	</td>
	<td bgcolor="#ffff00">&nbsp;</td>
	<td bgcolor="#ffff00">&nbsp;</td>
	<td bgcolor="#ffff00">&nbsp;</td>
	<td bgcolor="#ffff00">&nbsp;</td>
	<td bgcolor="#ffff00">&nbsp;</td>
	<td bgcolor="#ffff00">&nbsp;</td>
<?
if($type==9)    // Nächtigungscamps
{
?>
	<td bgcolor="#ffff00">&nbsp;</td>
<?}?>
	<td bgcolor="#ffff00">&nbsp;</td>
<?
if ($is_nachmittag)
{ 		
for ($i=0;$i<count($m1_split);$i++)   // summe der ausgewählten Nachmittagsangebote //
{ ?>
	<td bgcolor="#ffff00">&nbsp;</td>
<?}
}?>
<? 		
if ($is_lernmodul)
{
for ($i=0;$i<count($m2_split);$i++)  // Summe der ausgewählten Lernmodule //
{ ?>
	<td bgcolor="#ffff00">&nbsp;</td>
<?}
}?>
	
<? for ($count_termine=0;$count_termine<=$anzahl_termine;$count_termine++) 
{?>
	<td bgcolor="#ffff00" align="center" <?if ($count_termine==$marked_col) { echo" bgcolor=#00ff00 "; }?> >
<?
	if (($kurstermine[$count_termine][checked]=="on") && (isEmployee()))
	{?>
		<img src="../images/haken.gif">
		<input type="hidden" name="kurstermine[<?print($count_termine)?>][checked]" value="<?echo $kurstermine[$count_termine][checked]?>">
<?	} else
	{?>
		<input type="Checkbox" name="kurstermine[<?print($count_termine)?>][checked]" <?if ($kurstermine[$count_termine][checked]=="on") echo"checked"?>>
<?  } ?>
		<input type="hidden" name="kurstermine[<?print($count_termine)?>][checked_original]" value="<?echo $kurstermine[$count_termine][checked_original]?>">
<!--		<input type="hidden" name="kurstermine[<?print($count_termine)?>][content]" value="<?echo html_entity_decode($kurstermine[$count_termine][content])?>">
		<input type="hidden" name="kurstermine[<?print($count_termine)?>][remarks]" value="<?echo html_entity_decode($kurstermine[$count_termine][remarks])?>">
		<input type="hidden" name="kurstermine[<?print($count_termine)?>][used_items]" value="<?echo html_entity_decode($kurstermine[$count_termine][used_items])?>">-->
	</td>
<?	
}?>
	</tr>

<? if ($selected_termine=="-1") { $selected_termine=$marked_col;}
   $date = new DateTime($kurstermine[$selected_termine][datum]);
   if ($selected_termine==$marked_col) {$farbe="#00ff00";} else {$farbe="#00ffff";}

	$sql_kurstermine_select = "select content,remarks,used_items from coursetimes where id='".$kurstermine[$selected_termine][id]."'";
	$rs_kurstermine_select=getrs($sql_kurstermine_select,$print_debug,$print_error);
	LIST($content,$remarks,$used_items)=$rs_kurstermine_select -> fetch_row();
?>
	<tr valign="top">
	<td valign="middle" align="center" colspan=50 height=30 bgcolor="<?print($farbe)?>">
		<div class="headline">Detaileintr&auml;ge vom <?print($date->format('d.m.Y'))?>: </div>
		<input type="<?print($field_value)?>" name="selected_termine" value="<?print($selected_termine)?>">
	</td>
	</tr>

	<tr valign="top">
	<td valign="top" align="left">
		Tagesinfos:<br><br>Wichtige Infos, insb. besondere Vorkommnisse, vorzeitige Abreisen (inkl. Begr&uuml;ndung + Uhrzeit), sp&auml;ter Ankommende (inkl. Uhrzeit), Nicht Erschienene, etc., evtl. Tagesprogramm, sonstiges, Feedback
		<?echo display_error($error_remarks);?>
	</td>
	<td valign="top" colspan=49>
	<? if (($kurstermine[$selected_termine][checked]=="on") || ($selected_termine<>$marked_col))
	{
		echo html_entity_decode($remarks);?>
		<input type="hidden" name="kurstermine[<?print($marked_col)?>][remarks]" value="<?echo html_entity_decode($kurstermine[$marked_col][remarks])?>">
	<?} else { 
		$kurstermine[$selected_termine][remarks]=$remarks;
	?>
		<textarea cols=100 rows=10 name="kurstermine[<?print($selected_termine)?>][remarks]"><?echo html_entity_decode($kurstermine[$selected_termine][remarks])?></textarea>
	<?}?>
	</td>
	</tr>
	</table>
</td>
</tr>

<tr height=30>
<td colspan=10 class=form_header align="center">
   <INPUT TYPE="submit" NAME="save_x" value="Senden">
<!--   <INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"> -->
   <input type="<?print($field_value)?>" name="marked_col" value="<?print($marked_col)?>">
</td>
</table>
</form>
</body>
</html>



<script language="JavaScript1.2" type="text/javascript">

function check_fields()
{
	alert("hihi");
	fehler=0;
    if (window.document.campblatt.elements['kurstermine[<?print($marked_col)?>][remarks]'].text=="")
	{
		alert("Es muss ein Eintrag im Feld Feedback vorhanden sein!");
		window.document.campblatt.elements['kurstermine[<?print($marked_col)?>][remarks]'].focus();
		fehler=1;
	}
//	if (fehler==0) window.document.campblatt.submit();
}
</script>
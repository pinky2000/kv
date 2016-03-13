<?

require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/* Dateiname: payment_form.php
*  Zweck: Formular zur Eingabe von Zahlungsdaten "virtueller Erlagschein"
*/

date_default_timezone_set('Europe/Berlin');

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
//isAllow(isAdmin() || isSecretary());

$id = $_GET['id'];

/* _POST einlesen */
$confirm = $_POST['confirm'];
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$update = $_POST['update'];
$zahlungen = $_POST['zahlungen'];
$date_month = $_POST['date_month'];
$date_year = $_POST['date_year'];
$date_day = $_POST['date_day'];
$client_name = $_POST['client_name'];
$change_user = $_SESSION['username'];
$change_date = date('Y-m-d H:m:s');
$opt_nachmittag_select=$_POST['opt_nachmittag_select'];
$opt_lernmodul_select=$_POST['opt_lernmodul_select'];
$opt_modul3_select=$_POST['opt_modul3_select'];
$opt_modul4_select=$_POST['opt_modul4_select'];
$camp = $_POST['camp'];

$max_payments = $_POST['max_payments'];
for ($count_payments=0;$count_payments<=$max_payments;$count_payments++)
{
	$datum[$count_payments]=$date_year[$count_payments]."-".$date_month[$count_payments]."-".$date_day[$count_payments];
	$zahlungen[datum][$count_payments]=$datum[$count_payments];
}

$back_url="../admin/payment_list.php";
if (isset($back_x))
{
  header("Location: $back_url");
}

if (!isset($no_error)) $no_error=1;
if (!isset($max_payments)) $max_payments=0;

if (isset($client_name)) { $zahlungen[client_id]=$client_name; }
if ($print_debug) { var_dump($_POST); }

?>
<HTML>
<HEAD>
	<script src="codebase/dhtmlxcommon.js"></script>
	<script src="codebase/dhtmlxcombo.js"></script>
	<link rel="STYLESHEET" type="text/css" href="codebase/dhtmlxcombo.css">

	<script language="JavaScript" type="text/javascript">
	window.dhx_globalImgPath="codebase/imgs/";
	</script>
<title>

</title>
<?

if (isset($delete_x))
{
		if ($confirm=="true" && $id>0)
        {
           
		   $sql_payment_delete="update payments set status='Entfernt',reg_date='$change_date',reg_user='$change_user' where rechnung_id=".$id;
           $rs_payment_delete=getrs($sql_payment_delete,$print_debug,$print_error);

           if (mysql_errno()==0)
                  ok_site($back_url);
           else
                  error_site($back_url);
        }
        else
        {
           error_site($back_url);
        }
}

// Save-Button wird gedrückt

elseif (isset($save_x))
{

  $no_error= CheckEmptyCombo($zahlungen[client_id],$error_client) &&
  			 CheckEmptyCombo($zahlungen[course_id],$error_course);

  if ($status=="")
    $status="Inaktiv";

//  var_dump($opt_nachmittag_select); 

   $zahlungen[is_nach_auswahl]=$opt_nachmittag_select[0];
   for ($i=1;$i<=count($opt_nachmittag_select);$i++)
   {
 	$zahlungen[is_nach_auswahl]=$zahlungen[is_nach_auswahl].";".$opt_nachmittag_select[$i];
   } 
   $zahlungen[is_lern_auswahl]=$opt_lernmodul_select[0];
   for ($i=1;$i<=count($opt_lernmodul_select);$i++)
   {
 	$zahlungen[is_lern_auswahl]=$zahlungen[is_lern_auswahl].";".$opt_lernmodul_select[$i];
   } 
   $zahlungen[is_modul3_auswahl]=$opt_modul3_select[0];
   for ($i=1;$i<=count($opt_modul3_select);$i++)
   {
 	$zahlungen[is_modul3_auswahl]=$zahlungen[is_modul3_auswahl].";".$opt_modul3_select[$i];
   } 
   $zahlungen[is_modul4_auswahl]=$opt_modul4_select[0];
   for ($i=1;$i<=count($opt_modul4_select);$i++)
   {
 	$zahlungen[is_modul4_auswahl]=$zahlungen[is_modul4_auswahl].";".$opt_modul4_select[$i];
   } 
//   print($zahlungen[is_nach_auswahl]);
   
  if (isset($id) && $id!="" && $no_error)
  {
	   $sql_payment_update="update payments set
				 clients_id ='".$zahlungen[client_id]."',
				 courses_id ='".$zahlungen[course_id]."',
				 billdate   ='".$datum[0]."',
				 amount     ='".$zahlungen[payed][0]."',
				 sconto_id     ='".$zahlungen[sconto_id]."',
				 sconto_amount   ='".$zahlungen[sconto_value]."',
				 opt_amount   ='".$zahlungen[opt_value]."',
				 client_price   ='".$zahlungen[client_price]."',
				 remarks   = '".$zahlungen[remark][0]."',
				 status      ='".$zahlungen[status]."',
				 mahnung_sent1 = '".$zahlungen[mahnung_sent1]."',
				 mahnung_sent2 = '".$zahlungen[mahnung_sent2]."',
				 mahnung_sent3 = '".$zahlungen[mahnung_sent3]."',
				 mahnung_comment = '".$zahlungen[mahnung_comment]."',
				 camp_comment = '".$zahlungen[camp_kommentar]."',
				 change_date = '".$change_date."',
				 change_user = '".$change_user."',
				 rab_earlybook = '".$zahlungen[is_early]."',
				 rab_lastminute = '".$zahlungen[is_last]."',
				 rab_stammkunde = '".$zahlungen[is_stamm]."',
				 rab_geschwister = '".$zahlungen[is_geschw]."',
				 rab_kombi1 = '".$zahlungen[is_kombi1]."',
				 rab_kombi2 = '".$zahlungen[is_kombi2]."',
				 rab_verlaengerung = '".$zahlungen[is_verl]."',
				 rab_halbtag = '".$zahlungen[is_halb]."',
				 rab_firmen = '".$zahlungen[is_firma]."',
				 rab_sonder = '".$zahlungen[is_sonder]."'
			 where id=".$zahlungen[beleg_id][0];
   	   $rs_payment_update=getrs($sql_payment_update,$print_debug,$print_error);

         if ($camp==1)
         {
	   $sql_payment_options_update="update payments_opt_camps set
				 sel_verpflegung = '".$zahlungen[is_verpflegung]."',
				 sel_nachmittag = '".$zahlungen[is_nachmittag]."',
				 sel_nachmittag_auswahl = '".$zahlungen[is_nach_auswahl]."',
				 sel_lernmodul = '".$zahlungen[is_lernmodul]."',
				 sel_lernmodul_auswahl = '".$zahlungen[is_lern_auswahl]."',
				 sel_modul3 = '".$zahlungen[is_modul3]."',
				 sel_modul3_auswahl = '".$zahlungen[is_modul3_auswahl]."',
				 sel_modul4 = '".$zahlungen[is_modul4]."',
				 sel_modul4_auswahl = '".$zahlungen[is_modul4_auswahl]."',
				 sel_flughafen_hin = '".$zahlungen[is_flughafen_hin]."',
				 sel_flughafen_ret = '".$zahlungen[is_flughafen_ret]."',
				 sel_flughafen_hin_minor = '".$zahlungen[is_flughafen_hin_minor]."',
				 sel_flughafen_ret_minor = '".$zahlungen[is_flughafen_ret_minor]."',
				 sel_bahnhof_hin = '".$zahlungen[is_bahnhof_hin]."',
				 sel_bahnhof_ret = '".$zahlungen[is_bahnhof_ret]."',
				 sel_zertifikat = '".$zahlungen[is_zertifikat]."',
				 sel_zertifikat_auswahl = '".$zahlungen[is_zertifikat_auswahl]."'
			 where rechnung_id=".$zahlungen[id];
   	   $rs_payment_options_update=getrs($sql_payment_options_update,$print_debug,$print_error);
//	   print("<br>-->".$rs_payment_options_update);
	   if ($rs_payment_options_update->affected_rows == 0)
	   {
		   $sql_payment_options_insert="insert into payments_opt_camps (sel_verpflegung,sel_nachmittag,sel_nachmittag_auswahl,sel_lernmodul,sel_lernmodul_auswahl,sel_modul3,sel_modul3_auswahl,sel_modul4,sel_modul4_auswahl,sel_flughafen_hin,sel_flughafen_ret,sel_flughafen_hin_minor,sel_flughafen_ret_minor,sel_bahnhof_hin,sel_bahnhof_ret,sel_zertifikat,sel_zertifikat_auswahl,rechnung_id) values (
					  '".$zahlungen[is_verpflegung]."',
					  '".$zahlungen[is_nachmittag]."',
					  '".$zahlungen[is_nach_auswahl]."',
					  '".$zahlungen[is_lernmodul]."',
					  '".$zahlungen[is_lern_auswahl]."',
					  '".$zahlungen[is_modul3]."',
					  '".$zahlungen[is_modul3_auswahl]."',
					  '".$zahlungen[is_modul4]."',
					  '".$zahlungen[is_modul4_auswahl]."',
					  '".$zahlungen[is_flughafen_hin]."',
					  '".$zahlungen[is_flughafen_ret]."',
					  '".$zahlungen[is_flughafen_hin_minor]."',
					  '".$zahlungen[is_flughafen_ret_minor]."',
					  '".$zahlungen[is_bahnhof_hin]."',
					  '".$zahlungen[is_bahnhof_ret]."',
					  '".$zahlungen[is_zertifikat]."',
					  '".$zahlungen[is_zertifikat_auswahl]."',
				 	  '".$zahlungen[id]."')";
	   	   $rs_payment_options_insert=getrs($sql_payment_options_insert,$print_debug,$print_error);
//	   	print("inserted");
		
	   }
         }
	   if ($max_payments>0)
	   {
			for ($count_payments=1;$count_payments<=$max_payments;$count_payments++)
			{
			   if (($zahlungen[beleg_id][$count_payments]!="") || ($zahlungen[beleg_id][$count_payments]>0))
			   {
					if ($zahlungen[payed][$count_payments]==0)
					{
						$sql_payment_update="update payments set
								billdate   ='".$datum[$count_payments]."',
								amount     ='".$zahlungen[payed][$count_payments]."',
								remarks   = '".$zahlungen[remark][$count_payments]."',
								status      ='Entfernt',
								change_date = '".$change_date."',
								change_user = '".$change_user."'
							where id=".$zahlungen[beleg_id][$count_payments];
						$rs_payment_update=getrs($sql_payment_update,$print_debug,$print_error);
					} else
					{
						$sql_payment_update="update payments set
						 billdate   ='".$datum[$count_payments]."',
						 amount     ='".$zahlungen[payed][$count_payments]."',
						 remarks   = '".$zahlungen[remark][$count_payments]."',
						 status      ='".$zahlungen[status]."',
						 change_date = '".$change_date."',
						 change_user = '".$change_user."'
						where id=".$zahlungen[beleg_id][$count_payments];
					$rs_payment_update=getrs($sql_payment_update,$print_debug,$print_error);
					}
				} else
				{
					$sql_payment_insert="
						insert into payments
							(rechnung_id,clients_id,courses_id,reg_date,billdate,amount,remarks,status,change_date,change_user)
						values
							('".$zahlungen[id]."','".$zahlungen[client_id]."','".$zahlungen[course_id]."','".$zahlungen[reg_date]."','".$datum[$count_payments]."','".$zahlungen[payed][$count_payments]."','".$zahlungen[remark][$count_payments]."','".$zahlungen[status]."','".$change_date."','$change_user')";
					$rs_payment_insert=getrs($sql_payment_insert,$print_debug,$print_error);
				}
			}
		}
   }
	elseif($no_error)
   {
		$sql_last_element="select rechnung_id from payments order by rechnung_id desc limit 1";
		$rs_last_element=getrs($sql_last_element,$print_debug,$print_error);
		list($last_id)=$rs_last_element->fetch_row();
//		print($last_id);
		$new_id=$last_id+1;
//		print("<br>".$new_id);
		$sql_payment_insert="
				insert into payments
				   (rechnung_id,clients_id,courses_id,billdate,amount,sconto_id,sconto_amount,opt_amount,client_price,remarks,status,mahnung_sent1,mahnung_sent2,mahnung_sent3,mahnung_comment,create_date,reg_date,reg_user,change_date,change_user,rab_earlybook,rab_lastminute,rab_stammkunde,rab_geschwister,rab_kombi1,rab_kombi2,rab_verlaengerung,rab_halbtag,rab_firmen,rab_sonder,camp_comment)
				values
				   ('".$new_id."','".$zahlungen[client_id]."','".$zahlungen[course_id]."','".$datum[0]."','".$zahlungen[payed][0]."','".$zahlungen[sconto_id]."','".$zahlungen[sconto_value]."','".$zahlungen[opt_value]."','".$zahlungen[client_price]."','".$zahlungen[remark][0]."','".$zahlungen[status]."','".$zahlungen[mahnung_sent1]."','".$zahlungen[mahnung_sent2]."','".$zahlungen[mahnung_sent3]."','".$zahlungen[mahnung_comment]."',sysdate(),sysdate(),'".$change_user."','".$change_date."','".$change_user."','".$zahlungen[is_early]."','".$zahlungen[is_last]."','".$zahlungen[is_stamm]."','".$zahlungen[is_geschw]."','".$zahlungen[is_kombi1]."','".$zahlungen[is_kombi2]."','".$zahlungen[is_verl]."','".$zahlungen[is_halb]."','".$zahlungen[is_firma]."','".$zahlungen[is_sonder]."','".$zahlungen[camp_kommentar]."')";
		$rs_payment_insert=getrs($sql_payment_insert,$print_debug,$print_error);
	    if ($max_payments>0)
	    {
			for ($count_payments=1;$count_payments<=$max_payments;$count_payments++)
			{
			   if (($zahlungen[beleg_id][$count_payments]!="") || ($zahlungen[beleg_id][$count_payments]>0))
			   {
					$sql_add_payment_insert="
						insert into payments
							(rechnung_id,clients_id,courses_id,reg_date,billdate,amount,remarks,status,change_date,change_user)
						values
							('".$new_id."','".$zahlungen[client_id]."','".$zahlungen[course_id]."','".$zahlungen[reg_date]."','".$datum[$count_payments]."','".$zahlungen[payed][$count_payments]."','".$zahlungen[remark][$count_payments]."','".$zahlungen[status]."','".$change_date."','$change_user')";
					$rs_add_payment_insert=getrs($sql_add_payment_insert,$print_debug,$print_error);
				}
			}
		}	
		$id=$new_id;
//		print("new_id:".$id);
   }
}


// Back-Button wird gedr�ckt

// Daten aus DB Laden

if (isset($id) && $id!="" && !$update && $no_error)
{
	$sql_payment_select="select a.id,a.rechnung_id,a.courses_id,a.clients_id,a.reg_date,a.reg_user,a.billdate,a.amount,a.sconto_id,a.sconto_amount,a.opt_amount,a.client_price,a.remarks,
a.create_date,a.status,a.mahnung_sent1,a.mahnung_sent2,a.mahnung_sent3,a.mahnung_comment,a.rab_earlybook,a.rab_lastminute,a.rab_stammkunde,
a.rab_geschwister,a.rab_kombi1,a.rab_kombi2,a.rab_verlaengerung,a.rab_halbtag,a.rab_firmen,a.rab_sonder,b.rab_earlybook,b.rab_lastminute,b.rab_stammkunde,b.rab_geschwister,b.rab_kombi1,b.rab_kombi2,b.rab_verlaengerung,b.rab_halbtag,b.rab_firmen,b.rab_sonder,a.web_camp_id,a.camp_comment from payments a,courses b where a.status in ('E','F','A') and a.courses_id=b.id and a.rechnung_id=".$id;
    $rs_payment_select=getrs($sql_payment_select,$print_debug,$print_error);
 // Anzahl an Zahlungen//
	$max_payments=($rs_payment_select->num_rows)-1;
	
//	print($max_payments."<br>");
	LIST($zahlungen[beleg_id][0],$zahlungen[id],$zahlungen[course_id],$zahlungen[client_id],$zahlungen[reg_date],$zahlungen[reg_user],$zahlungen[datum][0],$zahlungen[payed][0],$zahlungen[sconto_id],$zahlungen[sconto_value],$zahlungen[opt_value],$zahlungen[client_price],$zahlungen[remark][0],$zahlungen[create_date],$zahlungen[status],$zahlungen[mahnung_sent1],$zahlungen[mahnung_sent2],$zahlungen[mahnung_sent3],$zahlungen[mahnung_comment],$zahlungen[is_early],$zahlungen[is_last],$zahlungen[is_stamm],$zahlungen[is_geschw],$zahlungen[is_kombi1],$zahlungen[is_kombi2],$zahlungen[is_verl],$zahlungen[is_halb],$zahlungen[is_firma],$zahlungen[is_sonder],$zahlungen[rab_early],$zahlungen[rab_last],$zahlungen[rab_stamm],$zahlungen[rab_geschw],$zahlungen[rab_kombi1],$zahlungen[rab_kombi2],$zahlungen[rab_verl],$zahlungen[rab_halb],$zahlungen[rab_firma],$zahlungen[rab_sonder],$zahlungen[web_camp_id],$zahlungen[camp_kommentar])=$rs_payment_select->fetch_row();

	$sql_payment_opt_select="select a.id, a.sel_verpflegung, a.sel_nachmittag, a.sel_nachmittag_auswahl, a.sel_lernmodul, a.sel_lernmodul_auswahl, a.sel_modul3, a.sel_modul3_auswahl, a.sel_modul4, a.sel_modul4_auswahl, a.sel_flughafen_hin, a.sel_flughafen_ret, a.sel_flughafen_hin_minor, a.sel_flughafen_ret_minor, a.sel_bahnhof_hin, a.sel_bahnhof_ret, a.sel_zertifikat, a.sel_zertifikat_auswahl, a.sel_tennis, b.opt_verpflegung,b.opt_nachmittag,b.opt_lernmodul,b.opt_modul3,b.opt_modul4,b.opt_nachmittag_auswahl,b.opt_lernmodul_auswahl,b.opt_modul3_auswahl,b.opt_modul4_auswahl,b.opt_transfer_flughafen_hin,b.opt_transfer_flughafen_retour,b.opt_transfer_flughafen_minor_hin,b.opt_transfer_flughafen_minor_retour,b.opt_transfer_bahnhof_hin,b.opt_transfer_bahnhof_retour,b.opt_zertifikat,b.opt_tennis FROM payments_opt_camps a,payments c,courses b WHERE a.rechnung_id=c.rechnung_id and c.courses_id=b.id and a.rechnung_id=".$id;
    $rs_payment_opt_select=getrs($sql_payment_opt_select,$print_debug,$print_error);
	LIST($zahlungen[opt_id],$zahlungen[is_verpflegung],$zahlungen[is_nachmittag],$zahlungen[is_nach_auswahl],$zahlungen[is_lernmodul],$zahlungen[is_lern_auswahl],$zahlungen[is_modul3],$zahlungen[is_modul3_auswahl],$zahlungen[is_modul4],$zahlungen[is_modul4_auswahl],$zahlungen[is_flughafen_hin],$zahlungen[is_flughafen_ret],$zahlungen[is_flughafen_hin_minor],$zahlungen[is_flughafen_ret_minor],$zahlungen[is_bahnhof_hin],$zahlungen[is_bahnhof_ret],$zahlungen[is_zertifikat],$zahlungen[is_zertifikat_auswahl],$zahlungen[is_tennis],$zahlungen[opt_verpflegung],$zahlungen[opt_nachmittag],$zahlungen[opt_lernmodul],$zahlungen[opt_modul3],$zahlungen[opt_modul4],$zahlungen[opt_nachmittag_auswahl],$zahlungen[opt_lernmodul_auswahl],$zahlungen[opt_modul3_auswahl],$zahlungen[opt_modul4_auswahl],$zahlungen[opt_flughafen_hin],$zahlungen[opt_flughafen_ret],$zahlungen[opt_flughafen_hin_minor],$zahlungen[opt_flughafen_ret_minor],$zahlungen[opt_bahnhof_hin],$zahlungen[opt_bahnhof_ret],$zahlungen[opt_zertifikat],$zahlungen[opt_tennis])=$rs_payment_opt_select->fetch_row();

	if($rs_payment_opt_select->num_rows == 0)
	{
$zahlungen[is_verpflegung]="0.00";		
$zahlungen[is_nachmittag]="0.00";		
$zahlungen[is_lernmodul]="0.00";
$zahlungen[is_modul3]="0.00";	
$zahlungen[is_modul4]="0.00";
$zahlungen[is_flughafen_hin]="0.00";
$zahlungen[is_flughafen_ret]="0.00";
$zahlungen[is_flughafen_hin_minor]="0.00";
$zahlungen[is_flughafen_ret_minor]="0.00";
$zahlungen[is_bahnhof_hin]="0.00";
$zahlungen[is_bahnhof_ret]="0.00";
$zahlungen[is_zertifikat]="0.00";
$zahlungen[is_tennis]="0.00";
$fehlermeldung="Dateninkonsistenz - Bitte Datensatz zuerst speichern !!";
}
// Bef�llen der weiteren Zahlungsfelder mit Werten //
	if ($max_payments>0)
	{
		$index=1;
		while (($rs_payment_select>0) && LIST($zahlungen[beleg_id][$index],$zahlungen[id_1][$index],$waste,$waste,$waste,$waste,$zahlungen[datum][$index],$zahlungen[payed][$index],$waste,$waste,$waste,$waste,$zahlungen[remark][$index],$waste,$waste,$waste,$waste,$waste,$waste)=$rs_payment_select->fetch_row())
		{
			$index++;		
		}
	}
	// Ermittlung der berechneten Rabatte
/*	$sum_rab=0;
	if ($zahlungen[is_early]>0) {$sum_rab=$sum_rab+$zahlungen[is_early];}
	if ($zahlungen[is_last]>0) {$sum_rab=$sum_rab+$zahlungen[is_last];}
	if ($zahlungen[is_stamm]>0) {$sum_rab=$sum_rab+$zahlungen[is_stamm];}
	if ($zahlungen[is_geschw]>0) {$sum_rab=$sum_rab+$zahlungen[is_geschw];}
	if ($zahlungen[is_kombi1]>0) {$sum_rab=$sum_rab+$zahlungen[is_kombi1];}
	if ($zahlungen[is_kombi2]>0) {$sum_rab=$sum_rab+$zahlungen[is_kombi2];}
	if ($zahlungen[is_verl]>0) {$sum_rab=$sum_rab+$zahlungen[is_verl];}
	if ($zahlungen[is_halb]>0) {$sum_rab=$sum_rab+$zahlungen[is_halb];}
	if ($zahlungen[is_firma]>0) {$sum_rab=$sum_rab+$zahlungen[is_firma];}
	if ($zahlungen[is_sonder]>0) {$sum_rab=$sum_rab+$zahlungen[is_sonder];}
	
	$zahlungen[sconto_value]=$sum_rab;

	// Ermittlung der berechneten Optionen
	
	$m1_split=split(";",$zahlungen[opt_nachmittag_auswahl]);
	$m2_split=split(";",$zahlungen[opt_lernmodul_auswahl]);
	$m3_split=split(";",$zahlungen[opt_modul3_auswahl]);
	$m4_split=split(";",$zahlungen[opt_modul4_auswahl]);

	$sum_opt=0;*/
//	if (($zahlungen[is_verpflegung]=="") || (!isset($zahlungen[is_verpflegung]))) {$zahlungen[is_verpflegung]="1.00";}
/*	if (($zahlungen[is_nachmittag]=="") || (!isset($zahlungen[is_nachmittag])) {$zahlungen[opt_nachmittag]="0.00";}
	if (($zahlungen[is_lernmodul]=="") || (!isset($zahlungen[is_lernmodul]))) {$zahlungen[opt_lernmodul]="0.00";}
	if (($zahlungen[is_modul3]=="") || (!isset($zahlungen[is_modul3]))) {$zahlungen[opt_modul3]="0.00";}
	if (($zahlungen[is_modul4]=="") || (!isset($zahlungen[is_modul4]))) {$zahlungen[opt_modul4]="0.00";}
	if (($zahlungen[is_flughafen_hin]=="") || (!isset($zahlungen[is_flughafen_hin]))) {$zahlungen[opt_flughafen_hin]="0.00";}
	if (($zahlungen[is_flughafen_ret]=="") || (!isset($zahlungen[is_flughafen_ret]))) {$zahlungen[opt_flughafen_ret]="0.00";}
	if (($zahlungen[is_flughafen_hin_minor]=="") || (!isset($zahlungen[is_flughafen_hin_minor]))) {$zahlungen[opt_flughafen_hin_minor]="0.00";}
	if (($zahlungen[is_flughafen_ret_minor]=="") || (!isset($zahlungen[is_flughafen_ret_minor]))) {$zahlungen[opt_flughafen_ret_minor]="0.00";}
	if (($zahlungen[is_bahnhof_hin]=="") || (!isset($zahlungen[is_bahnhof_hin]))) {$zahlungen[opt_bahnhof_hin]="0.00";}
	if (($zahlungen[is_bahnhof_ret]=="") || (!isset($zahlungen[is_bahnhof_ret]))) {$zahlungen[opt_bahnhof_ret]="0.00";}
	if (($zahlungen[is_zertifikat]=="") || (!isset($zahlungen[is_zertifikat]))) {$zahlungen[opt_zertifikat]="0.00";}
	if (($zahlungen[is_tennis]=="") || (!isset($zahlungen[is_tennis]))) {$zahlungen[opt_tennis]="0.00";}
*/
}

if ((!isset($zahlungen[pric]))) 
{ 
	$pric=0; 
}

if (((!isset($id)) || ($id=="")) )
{
	$zahlungen[datum][0]=date("Y-m-d"); 
}

   $sql_camp_select="
   select 
		c.camp
	from 
		courses a,
		products b,
		timeperiods c,
		institutions d,
		institutions e
	where
		a.products_id=b.id and
		a.timeperiods_id=c.id and
		a.institutions_id=d.id and
		a.locations_id=e.id and a.id='".$zahlungen[course_id]."'";

   $rs_camp_select = getrs($sql_camp_select,$print_debug,$print_error);
   List($camp) = $rs_camp_select->fetch_row();

?>
<link rel="stylesheet" href="../css/ta.css">
<script type="text/javascript">
function delete_form()
{
  document.formular.confirm.value = confirm("Wollen Sie diesen Eintrag wirklich l&ouml;schen?");
}

function show_message(){
	calc();
	document.getElementById('light').style.display='block';
	document.getElementById('fade').style.display='block';
}

function status_load()
{
	window.document.formular.update.value=1;
	window.document.formular.submit();
}
function getprice()
{
	window.document.formular.update.value=1;
	window.document.formular.submit();
}

function add_payment()
{
	window.document.formular.max_payments.value=<?print($max_payments+1);?>;
	window.document.formular.update.value=1;
	window.document.formular.submit();
}

function calc(sconto_id)
{
 	sconto=parseFloat(window.document.formular.elements['zahlungen[is_early]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_last]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_stamm]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_geschw]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_kombi1]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_kombi2]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_verl]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_halb]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_firma]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_sonder]'].value);

<? if ($camp==1) { ?>
	options=parseFloat(window.document.formular.elements['zahlungen[is_verpflegung]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_nachmittag]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_lernmodul]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_modul3]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_modul4]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_flughafen_hin]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_flughafen_ret]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_flughafen_hin_minor]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_flughafen_ret_minor]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_bahnhof_hin]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_bahnhof_ret]'].value)+parseFloat(window.document.formular.elements['zahlungen[is_zertifikat]'].value);
<?} else {?>
options = window.document.formular.elements['zahlungen[opt_value]'].value;
<?}?>
//	alert(sconto);
//	alert(options);
	window.document.formular.elements['zahlungen[sconto_value]'].value = sconto;
	window.document.formular.elements['zahlungen[opt_value]'].value = options;

	kundenpreis = parseFloat(window.document.formular.elements['zahlungen[price]'].value)-parseFloat(window.document.formular.elements['zahlungen[sconto_value]'].value)+parseFloat(window.document.formular.elements['zahlungen[opt_value]'].value);
	kundenpreis = kundenpreis.toFixed(2);
	document.getElementById('kundenpreis').innerHTML = kundenpreis + " &euro;";
	window.document.formular.elements['zahlungen[client_price]'].value=kundenpreis;
	if ((window.document.formular.elements['zahlungen[payed][0]'].value != "") && (window.document.formular.elements['zahlungen[payed][0]'].value != 0))
	{ window.document.formular.elements['zahlungen[status]'].value="E"; }
	else
	{ window.document.formular.elements['zahlungen[status]'].value="F"; }
	summe_paid=0;
	for (i=0;i<=<?print($max_payments);?>;i++)
	{
		elem_string="zahlungen[payed][" + i + "]";
		if (window.document.formular.elements[elem_string].value != "")
		{
			summe_paid = summe_paid + parseFloat(window.document.formular.elements['zahlungen[payed][' + i + ']'].value);
		}
//	alert(summe_paid);
	}
	summe_paid = summe_paid.toFixed(2);
	
	differenz = summe_paid - kundenpreis;
	differenz = differenz.toFixed(2);
	document.getElementById('diff').innerHTML = differenz + " &euro;";
	document.getElementById('summe').innerHTML = summe_paid + " &euro;";
	if (differenz>=0) {document.getElementById('diff').style.background="green";} else {document.getElementById('diff').style.background="red";} 
}

function heutedatum(payment_count)
{
	var jetzt = new Date();
	var Tag = jetzt.getDate();
	var Monat = jetzt.getMonth();
	var Jahr = jetzt.getYear();
	Monat = Monat+1;
//	alert(Monat+'_'+Tag+'_'+Jahr)
	if (Monat<10) Monat = '0' + Monat;
	if (Tag<10) Tag = '0' + Tag;
	Jahr=Jahr-100+2000;
//	Jahr = '20' + Jahr;
	window.document.formular.elements['date_day['+payment_count+']'].value=Tag;
	window.document.formular.elements['date_month['+payment_count+']'].value=Monat;
	window.document.formular.elements['date_year['+payment_count+']'].value=Jahr;
}

</script>
</HEAD>

<? $command="calc();";
if (isset($save_x) && $no_error) $command="show_message();";?>

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

	<center>
	<table border=0 cellspacing=0 cellpadding=0>
		<tr><td height=12></td></tr>
			<SPAN class="headline">Zahlungen</SPAN><br>
		</td></tr>
		<tr><td height=10></td></tr>
	</table>
	<BR><BR>
<?
if (($camp=="1") && ($fehlermeldung))
{
	print("<center><font color=red>".$fehlermeldung."</font></center>");
}
?>
	<FORM  action="<? echo $PHP_SELF?>" method="POST" name=formular>
	<TABLE width=850 border=0 CELLPADDING=0 CELLSPACING=0>
		<tr>
   			<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   			<td colspan=5 HEIGHT=1  class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   			<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		<TR>
   			<TD colspan=5 width=500 ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>ID: <?print($zahlungen[id])?></TD></TR></TABLE></TD>
			<input type=hidden name=zahlungen[id] value="<?print($zahlungen[id])?>">
		</TR>
		<tr>
   			<td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		<TR>
   			<TD colspan=5 width=250 ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Art</TD></TR></TABLE></TD>
		</TR>
		<TR>
   			<TD colspan=5 class="form_row" ALIGN=left>
   			<TABLE>
   			<TR>
				<TD class=form_row WIDTH=100% HEIGHT=100% align=left>
					<select name=zahlungen[status] onchange="javascript:status_load()">
						<option value="F" <?if ($zahlungen[status]=='F') print("selected"); ?>>Forderung</option>
						<option value="E" <?if ($zahlungen[status]=='E') print("selected"); ?>>Einnahme</option>
						<option value="A" <?if ($zahlungen[status]=="A") print("selected"); ?>>Ausgabe</option>
<? if (($zahlungen[status]=="Storno") || ($zahlungen[status]=="Entfernt"))
	{ 				?>
						<option value="Storno" selected>Storno</option>	
<?	} ?>
					</select>
				</TD>
   			</TR>
   			</TABLE>
   			</TD>
		</TR>
		<tr>
   			<td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		<TR>
   			<TD colspan=5 width=500 ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Name
<? if ($id) echo "<font color=red>Aenderung nicht mehr moeglich!</font>" ?>
		   </TD></TR></TABLE></TD>
		</TR>
		<TR>
   			<TD width=500 ALIGN=left   class=form_row colspan="5">
   			<TABLE><TR><TD WIDTH=100% HEIGHT=100%>
			   <?echo display_error($error_client);?>
<? if (!$id) {	// Wenn es keine ID gibt, ist der Eintrag neu // ?>
<?
			if (isset($client_name))
			{
				$sql_client_select="select lastname,firstname from clients where id=".$client_name;
           		$rs_client_select=getrs($sql_client_select,$print_debug,$print_error);
				LIST($lastname,$firstname)=$rs_client_select -> fetch_row();
			}
?>			
			<select style='width:200px;'  id="combo_zone" name="client_name">
					<option selected value="<? print($zahlungen[client_id])?>"><? print($lastname.",".$firstname)?></option>
			</select>
	
	 		<script>
				var z=new dhtmlXCombo("combo_zone","<? print("client_name") ?>",200);
				z.enableFilteringMode(true,"codebase/loadCombo.php",true,true);
				z.onBlur="alert()";
			</script>
			<input type=hidden name=zahlungen[client_id] value="<?print($zahlungen[client_id])?>">
<? } 
else
{ ?>
			<input type=hidden name=zahlungen[client_id] value="<?print($zahlungen[client_id])?>">
<?
   $sql_client_select="select id,firstname,lastname from clients where status='Aktiv' and id='".$zahlungen[client_id]."'";
   $rs_client_select = getrs($sql_client_select,$print_debug,$print_error);
   List($nid,$fname,$name) = $rs_client_select->fetch_row();
   print("<b>".$name." ".$fname."</b>"); 
} ?>
  			</TD></TR></TABLE>
   		</TD>
	</TR>
	<tr>
   		<td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
   		<TD colspan=3 width=500 ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Kurs
<? if ($id) echo "<font color=red>Aenderung nicht mehr moeglich!</font>" ?>
		</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   		<TD width=500 ALIGN=LEft   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Betrag&nbsp;&nbsp;</TD></TR></TABLE>
   		</TD></TR>
	</TR>
	<TR>
   		<TD width=250  class=form_row colspan=3>
		<TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   		<?echo display_error($error_course);?>
<? if (!isset($id)) {	?>
   			Suchfilter: <input name=zahlungen[course_filter] type=text value="<?print($zahlungen[course_filter])?>"  onchange="getprice();">
			<select name=zahlungen[course_id] class="input_text" onchange="getprice();">
<?
}
   $sql_courses_select="
   select 
   		a.id,
		a.products_id,
		a.year,
		a.timeperiods_id,
		a.institutions_id,
		a.locations_id,
		a.info,
		b.name,
		c.name,
		d.name,
		d.address,
		e.name,
		e.address,
		a.price, 
		c.camp
	from 
		courses a,
		products b,
		timeperiods c,
		institutions d,
		institutions e
	where
		a.products_id=b.id and
		a.timeperiods_id=c.id and
		a.institutions_id=d.id and
		a.locations_id=e.id ";
   if (isset($id))
   {
	$sql_courses_select.=" and a.id=".$zahlungen[course_id];
   }	
   if ($zahlungen[course_filter]!="")
   {
   	$sql_courses_select.=" and (
		a.id LIKE '$zahlungen[course_filter]' or
		a.products_id LIKE '$zahlungen[course_filter]' or
		a.year LIKE '$zahlungen[course_filter]' or
		a.timeperiods_id LIKE '$zahlungen[course_filter]' or
		a.institutions_id LIKE '$zahlungen[course_filter]' or
		a.locations_id LIKE '$zahlungen[course_filter]' or
		a.info LIKE '$zahlungen[course_filter]' or
		b.name LIKE '$zahlungen[course_filter]' or
		c.name LIKE '$zahlungen[course_filter]' or
		d.name LIKE '$zahlungen[course_filter]' or
		d.address LIKE '$zahlungen[course_filter]' or
		e.name LIKE '$zahlungen[course_filter]' or
		e.address LIKE '$zahlungen[course_filter]')";
   }
   $sql_courses_select.=" order by year desc;";
   $rs_courses_select = getrs($sql_courses_select,$print_debug,$print_error);
   While ($rs_courses_select>0 && List($course_id,$product_id,$year,$time_id,$inst_id,$loc_id,$info,$product_name,$time_name,$inst_name,$inst_adr,$loc_name,$loc_adr,$price,$is_camp) = $rs_courses_select->fetch_row())
   { 
	if (isset($id))
	{
		if ($zahlungen[course_id]==$course_id)
		{
			print("<input type=hidden name=zahlungen[course_id] value=".$course_id.">");
	    	$pname=$product_name;
	    	$tname=$time_name;
			$cyear=$year;
			$iname=$inst_name;
			$lname=$loc_name;
			$zahlungen[price]=$price;
		}			
	}
   	else 
	{
		if ($zahlungen[course_id]==$course_id) 
		{
		/* Falls es eine neue Zahlungseintrag ist, dann gibt es noch keine ID */ ?>
	 	  <option selected value=<?print($course_id)?>><?print($product_id.$year.$time_id.$inst_id.$loc_id."-".$info)?>
		  <?$pname=$product_name;
		    $tname=$time_name;
			$cyear=$year;
			$iname=$inst_name;
			$lname=$loc_name;
			$zahlungen[price]=$price;
    	} else { ?>
		  <option value=<?print($course_id)?>><?print($product_id.$year.$time_id.$inst_id.$loc_id."-".$info)?>
<?   	}	  
	}
	}	
	if (!isset($id)) 
	{?>
   		</select>
<?	}
	print("<b>".$pname."/".$cyear."/".$tname."/".$iname."/".$lname."</b><br>angemeldet am ".$zahlungen[reg_date]." von ".$zahlungen[reg_user]);
	?>
   <input type="hidden" name="zahlungen[reg_date]" value="<?print($zahlungen[reg_date])?>">
   <input type="hidden" name="zahlungen[reg_user]" value="<?print($zahlungen[reg_user])?>">
<?if ($camp=="1")
{ ?> 
   <br><br>
   Ausgew&auml;hlte Optionen:<br>
   <?print($zahlungen[is_nach_auswahl]." ".$zahlungen[is_lern_auswahl]." ".$zahlungen[is_modul3_auswahl]." ".$zahlungen[is_modul4_auswahl]);?>
<?  if (!isset($print_debug)) {$print_debug=0;}
	$link=base64_encode("print_debug=".$print_debug."&id=".$zahlungen[web_camp_id]."&rechnung_id=".$id);?>
   <br><br>
   <a target=_new href='http://www.teamactivities.at/online/booking_confirmation.php?data=<?print($link)?>'>Buchungsbest&auml;tigung</a>  &nbsp;&nbsp;--&nbsp;&nbsp;  
   <a target=_new href='webregister_form.php?id=<?print($zahlungen[web_camp_id])?>'>Online Anmeldung</a><br><br>
   Kommentarfeld f&uuml;r nachtr&auml;gliche &Auml;nderungen <font size=1>(wird in der Buchungsbest&auml;tigung angezeigt)</font><br>
   <textarea cols=40 rows=2 name=zahlungen[camp_kommentar]><?print($zahlungen[camp_kommentar])?></textarea>
<?
}?>   
   </TD></TR></TABLE>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row align=right>

<?if ($zahlungen[status]!="A") { 
	$kundenpreis=$zahlungen[price]-$zahlungen[sconto_value]+$zahlungen[opt_value];
?>	
   <TABLE>
   <TR>
   		<TD WIDTH=50% align=left>
   			<?echo "Original Kurspreis: ";?>
   		</TD>
   		<TD WIDTH=50% align=right><?printf("%.2f",$zahlungen[price])?> &euro;
   			<input type="hidden" name=zahlungen[price] type="text" size=4 value="<?printf("%.2f",$zahlungen[price])?>">
   		</TD>
   </TR>
   <TR>
   		<TD WIDTH=50% align=left>
		   <?echo "Erm&auml;ssigung: ";?>
   		</TD>
   		<TD WIDTH=50% align=right>
		<?if ($zahlungen[sconto_value]=="") { $zahlungen[sconto_value]="0.00";}?>
		   - <INPUT onchange="calc();" TYPE=TEXT MAXLENGTH=10 SIZE=10 NAME=zahlungen[sconto_value] VALUE="<?echo $zahlungen[sconto_value]?>">&nbsp;&nbsp;  
   		</TD>
   </TR>
   <TR>
   		<TD WIDTH=50% align=left>
		   <?echo "Optionen: ";?>
   		</TD>
   		<TD WIDTH=50% align=right>
		<?if ($zahlungen[opt_value]=="") { $zahlungen[opt_value]="0.00";}?>
		  + <INPUT onchange="calc();" TYPE=TEXT MAXLENGTH=10 SIZE=10 NAME=zahlungen[opt_value] VALUE="<?echo $zahlungen[opt_value]?>">&nbsp;&nbsp;  
   		</TD>
   </TR>
   <TR>
   		<TD WIDTH=50% align=left>
		   <?echo "<font size=+1>Kundenpreis: </font>";?>
   		</TD>
   		<TD WIDTH=50% align=right><font size=+1><div id="kundenpreis"><?printf("%.2f",$kundenpreis)?></div></font>
		   <input type=hidden name=zahlungen[client_price] value="<?print($zahlungen[client_price])?>">
 	    </TD>
   </TR>
   <TR>
   		<TD WIDTH=50% align=left>
		   <?echo "<font color=brown>Kundenpreis online: </font>";?>
   		</TD>
   		<TD WIDTH=50% align=right><font color=brown><div id="kundenpreis_online"><?print($zahlungen[client_price])?></div></font>
 	    </TD>
   </TR>

   <TR>
   		<TD WIDTH=50% align=left>
		   <?echo "<font size=+1>Zahlungen: </font>";?>
   		</TD>
   		<TD WIDTH=50% align=right>
			<font size=+1><div id="summe"></div></font>
   		</TD>
   </TR>
   <TR>
   		<TD WIDTH=50% align=left>
		   <?echo "Differenz: ";?>
   		</TD>
   		<TD WIDTH=50% align=right>
			<div id="diff"></div>
   		</TD>
   </TR>
   </TABLE>
<?}?>
   </TD>
</TR>
<tr>
   <td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<tr>
	<td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
	<TD colspan=5 width=500 ALIGN=left   class=form_header>
		<TABLE width=100%><TR><TD WIDTH=150 HEIGHT=100% align=left>
			<INPUT TYPE="BUTTON" NAME="addpayment" value="weiterer Zahlungseingang" onclick="add_payment()" src="../images/buttons/send.gif"> 
		</TD>
		<TD WIDTH=150>&nbsp;</TD>
		<TD>
			<b>Zahlungseing&auml;nge</b>
		</TD></TR></TABLE>
	</TD>
</TR>
<!--<TR>
   <TD width=250 ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%><?if ($zahlungen[status]=="A") { echo "Belegdatum"; } else { echo"Einzahlungsdatum"; }?></TD></TR></TABLE>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1>
   </TD>
   <TD width=500 ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%><?if ($zahlungen[status]=="A") { echo "ausgegebener Betrag"; } else { echo"eingezahlter Betrag"; }?></TD></TR></TABLE>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1>
   </TD>
   <TD ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Bemerkungen</TD></TR></TABLE>
   </TD>
</TR>-->
<? for ($count_payments=0;$count_payments<=$max_payments;$count_payments++)
{
	
	$dat=explode("-",$zahlungen[datum][$count_payments]);
	$date_year[$count_payments]=$dat[0];
	$date_month[$count_payments]=$dat[1];
	$date_day[$count_payments]=$dat[2];
?>
<TR>
	<TD colspan=5 width=500 ALIGN=left   class=form_header><TABLE width=100%><TR><TD WIDTH=100% HEIGHT=100% align=left>Zahlung Nr. <?print($zahlungen[id]."/".($count_payments+1));?>
		<INPUT TYPE="<?if($print_debug) { print("text");} else { print("hidden");}?>" NAME="zahlungen[beleg_id][<?print($count_payments);?>]" VALUE="<?echo $zahlungen[beleg_id][$count_payments]?>">
		</TD></TR></TABLE>
	</TD>
</TR>
<TR>
			<input type=hidden name="zahlungen[datum][<?print($count_payments);?>]" value="<?print($zahlungen[datum][$count_payments])?>">
		<TD width=250 ALIGN=left   class=form_row>
			<input type="Button" value="heute" name="heute[<?print($count_payments);?>]" onclick="javascript:heutedatum(<?print($count_payments);?>);">
			am:
			<TABLE border=0><TR>
				<TD>
			    <SELECT NAME=date_day[<?print($count_payments);?>]>
				<option value=0>00	   
				 <?for($i=1;$i<=31;$i++){
					 if (strlen($i)==1)
					 $i="0".$i;
					 if ($date_day[$count_payments]==$i) { echo"<OPTION SELECTED VALUE=$i>$i</OPTION>"; } else { echo"<OPTION VALUE=$i>$i</OPTION>"; };
				}?>
				</SELECT> 
				</TD>
				<TD>
				<SELECT NAME=date_month[<?print($count_payments);?>]>
				<option value=0>00
				 <?for($i=1;$i<=12;$i++){
					 if (strlen($i)==1)
					 $i="0".$i;
					 if ($date_month[$count_payments]==$i) { echo"<OPTION SELECTED VALUE=$i>$i</OPTION>"; } else { echo"<OPTION VALUE=$i>$i</OPTION>"; };
				}?>
				</SELECT>
				</TD>
				<TD>
				<?$zahl=date('Y');?>
				<SELECT NAME=date_year[<?print($count_payments);?>]>
				<option value=0000>0000
				 <? 
				 for($i=$zahl;$i>=2000;$i--){

					 if (strlen($i)==1)
					 $i="000".$i;
					 if ($date_year[$count_payments]==$i) { echo"<OPTION SELECTED VALUE=$i>$i</OPTION>"; } else { echo"<OPTION VALUE=$i>$i</OPTION>"; };
				}
				?>
				</SELECT>
				</TD>
			</TR></TABLE>
	    </TD>

	    <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	   
	    <TD width=100% class=form_row align=left>
			<table width=100% border=0>
			<tr>
				<td align=left>
					gezahlter Betrag:
				</td>
				<td align=right>
					<INPUT onchange="calc();" TYPE="TEXT" SIZE=5 NAME=zahlungen[payed][<?print($count_payments);?>] VALUE="<?echo $zahlungen[payed][$count_payments]?>"> &euro;  
				</td>
			</tr>
			</table>

	    </TD>
	   
	    <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	   
	    <TD width=50  class=form_row align=right><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
			<?if ($zahlungen[status]!="A") { ?> 
			Bemerkungen:<textarea cols=50 rows=2 name=zahlungen[remark][<?print($count_payments);?>]><?echo $zahlungen[remark][$count_payments]?></textarea>
			<? }?>
			</TD></TR></TABLE>
		</TD>

	</TR>
<!-- weitere Zahlungen -->
<?}?>

<? if ($zahlungen[status]!="A") { ?>
<?
$rs_courses_optionen_select=getrs("select 
	a.opt_verpflegung,
	a.opt_nachmittag,
	a.opt_lernmodul,
	a.opt_modul3,
	a.opt_modul4,
	a.opt_nachmittag_auswahl,
	a.opt_lernmodul_auswahl,
	a.opt_modul3_auswahl,
	a.opt_modul4_auswahl,
	a.opt_transfer_flughafen_hin,
	a.opt_transfer_flughafen_retour,
	a.opt_transfer_flughafen_minor_hin,
	a.opt_transfer_flughafen_minor_retour,
	a.opt_transfer_bahnhof_hin,
	a.opt_transfer_bahnhof_retour,
	a.opt_zertifikat,
	a.rab_earlybook,
	a.rab_lastminute,
	a.rab_stammkunde,
	a.rab_geschwister,
	a.rab_kombi1,
	a.rab_kombi2,
	a.rab_verlaengerung,
	a.rab_halbtag,
	a.rab_firmen,
	a.rab_sonder
	from courses a
	where a.id=".$zahlungen[course_id],$print_debug,$print_error);
	LIST($opt_verpflegung,$opt_nachmittag,$opt_lernmodul,$opt_modul3,$opt_modul4,$opt_nachmittag_auswahl,$opt_lernmodul_auswahl,$opt_modul3_auswahl,$opt_modul4_auswahl,$opt_flg_hin,$opt_flg_ret,$opt_flg_minor_hin,$opt_flg_minor_ret,$opt_bahn_hin,$opt_bahn_ret,$opt_zertifikat,$rab_early,$rab_last,$rab_stamm,$rab_geschw,$rab_kombi1,$rab_kombi2,$rab_verl,$rab_halb,$rab_firmen,$rab_sonder)=$rs_courses_optionen_select -> fetch_row();
		
?>
<tr>
   <td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD colspan=5 ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Mahnungen</TD></TR></TABLE></TD>
</TR>
<TR>
   <TD colspan=5 class=form_row align=left valign=top>
	   <TABLE width=100% border=0><TR><TD WIDTH=20% HEIGHT=100% valign=top>
	   <input type=checkbox name="zahlungen[mahnung_sent1]" title="Mahnung verschickt" <?if ($zahlungen[mahnung_sent1]) {echo "checked";}?> value="1">
	   1.Mahnung verschickt:<br>
	   <input type=checkbox name="zahlungen[mahnung_sent2]" title="Mahnung verschickt" <?if ($zahlungen[mahnung_sent2]) {echo "checked";}?> value="1">
	   2.Mahnung verschickt:<br>
	   <input type=checkbox name="zahlungen[mahnung_sent3]" title="Mahnung verschickt" <?if ($zahlungen[mahnung_sent3]) {echo "checked";}?> value="1">
	   Rechtsanwalt:<br>
	   <textarea cols=50 rows=6 name=zahlungen[mahnung_comment]><?echo $zahlungen[mahnung_comment]?></textarea>
	   <br>
<?if ($camp=="1")
{ ?> 
	    <br>
	    Transfer:<br>
	    <input type='text' size=8 name=zahlungen[is_flughafen_hin] value='<?print($zahlungen[is_flughafen_hin])?>' onchange="calc();">
		Trans. Flughafen hin (<?print($zahlungen[opt_flughafen_hin]);?> &euro;)<br>
		<input type='text' size=8  name=zahlungen[is_flughafen_ret] value='<?print($zahlungen[is_flughafen_ret])?>' onchange="calc();">
		Trans. Flughafen retour (<?print($zahlungen[opt_flughafen_ret]);?> &euro;)<br>
		<input type='text' size=8 name=zahlungen[is_flughafen_hin_minor] value='<?print($zahlungen[is_flughafen_hin_minor])?>' onchange="calc();">
		Trans. Flughf. hin minor (<?print($zahlungen[opt_flughafen_hin_minor]);?> &euro;)<br>
		<input type='text' size=8  name=zahlungen[is_flughafen_ret_minor] value='<?print($zahlungen[is_flughafen_ret_minor])?>' onchange="calc();">
		Trans. Flughf. ret. minor (<?print($zahlungen[opt_flughafen_ret_minor]);?> &euro;)<br>
		<input type='text' size=8 name=zahlungen[is_bahnhof_hin] value='<?print($zahlungen[is_bahnhof_hin])?>' onchange="calc();">
		Trans. Bahnhof hin (<?print($zahlungen[opt_bahnhof_hin]);?> &euro;)<br>
		<input type='text' size=8  name=zahlungen[is_bahnhof_ret] value='<?print($zahlungen[is_bahnhof_ret])?>' onchange="calc();">
		Trans. Bahnhof retour (<?print($zahlungen[opt_bahnhof_ret]);?> &euro;)<br>
<?}?>	   

	   </TD>
	   <TD width=40% valign=top>
		<table width=99%><tr><td colspan=2>Rabatte:</td></tr>
		<tr>
			<td>
				<input type='text' size=8 name=zahlungen[is_early] value='<?print($zahlungen[is_early]);?>' onchange="calc();"> 
				Fr&uml;hbucher (<?print($rab_early)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_last] value='<?print($zahlungen[is_last]);?>'  onchange="calc();"> 
				Last Minute (<?print($rab_last)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_stamm] value='<?print($zahlungen[is_stamm]);?>'  onchange="calc();"> 
				Stammkunde (<?print($rab_stamm)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_geschw] value='<?print($zahlungen[is_geschw]);?>'  onchange="calc();"> 
				Geschwisterrabatt (<?print($rab_geschw)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_kombi1] value='<?print($zahlungen[is_kombi1]);?>'  onchange="calc();"> 
				Kombi-Package 1 (<?print($rab_kombi1)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_kombi2] value='<?print($zahlungen[is_kombi2]);?>'  onchange="calc();"> 
				Kombi-Package 2 (<?print($rab_kombi2)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_verl] value='<?print($zahlungen[is_verl]);?>'  onchange="calc();"> 
				Verl&auml;ngerungsrabatt (<?print($rab_verl)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_halb] value='<?print($zahlungen[is_halb]);?>'  onchange="calc();"> 
				Halbtagsrabatt (<?print($rab_halb)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_firma] value='<?print($zahlungen[is_firma]);?>'  onchange="calc();"> 
				Firmenrabatt (<?print($rab_firmen)?> &euro;)<br>
				<input type='text' size=8 name=zahlungen[is_sonder] value='<?print($zahlungen[is_sonder]);?>'  onchange="calc();"> 
				Sonderrabatt (<?print($rab_sonder)?> &euro;)<br>
				<br>
	   </td></tr></table></TD>
<?if ($camp=="1")
{ ?> 
	   <TD width=40% valign=top>
		<table width=99%><tr><td colspan=2>Optionen:</td></tr>
		<tr>
			<td>
				<input type='text' size=8 name=zahlungen[is_verpflegung] value='<?print($zahlungen[is_verpflegung])?>' onchange="calc();">
				Verpflegung (<?print($opt_verpflegung);?> &euro;)<br>
				<input type='text' size=8  name=zahlungen[is_zertifikat] value='<?print($zahlungen[is_zertifikat])?>' onchange="calc();">
				Zertifikat (<?print($opt_zertifikat);?> &euro;)<br>
				<SELECT NAME=zahlungen[is_zertifikat_auswahl]>
					<OPTION VALUE="1" <?if ($zahlungen[is_zertifikat_auswahl]=="1") print("SELECTED");?>>A1-Anf&auml;nger I</OPTION>
					<OPTION VALUE="2" <?if ($zahlungen[is_zertifikat_auswahl]=="2") print("SELECTED");?>>A2-Anf&auml;nger II</OPTION>
					<OPTION VALUE="3" <?if ($zahlungen[is_zertifikat_auswahl]=="3") print("SELECTED");?>>B1-Fortgeschritten I</OPTION>
					<OPTION VALUE="4" <?if ($zahlungen[is_zertifikat_auswahl]=="4") print("SELECTED");?>>B2-Fortgeschritten II</OPTION>
					<OPTION VALUE="5" <?if ($zahlungen[is_zertifikat_auswahl]=="5") print("SELECTED");?>>C1-Native</OPTION>
				</SELECT>
				<br>
				<input type='text' size=8  name=zahlungen[is_nachmittag] value='<?print($zahlungen[is_nachmittag])?>' onchange="calc();">
				Modul1 (<?print($opt_nachmittag);?> &euro; / Auswahl)<br>
				<input type='hidden' size=8  name=zahlungen[is_nach_auswahl] value='<?print($zahlungen[is_nach_auswahl])?>' onchange="calc();">
				<select multiple name=opt_nachmittag_select[]>
<?
$m1_trim_selected=trim($zahlungen[is_nach_auswahl],";");
$m1_split_selected=split(";",$m1_trim_selected);

$m1_trim=trim($opt_nachmittag_auswahl,";");
$m1_split=split(";",$m1_trim);
for ($i=0;$i<count($m1_split);$i++)
{
	$print_selected="";
	for ($ii=0;$ii<count($m1_split_selected);$ii++)
	{
		print($m1_split_selected[$ii]."<br>");
		if (strcmp(trim($m1_split_selected[$ii]),trim($m1_split[$i]))==0)
		{
			$print_selected="selected";
		}
	}
	echo "<option $print_selected value='".$m1_split[$i]."'>".$m1_split[$i]."</option>";
}
?>
<option value=''> </option>
</select>	
<br>
				<input type='text' size=8  name=zahlungen[is_lernmodul] value='<?print($zahlungen[is_lernmodul])?>' onchange="calc();">
				Modul2 (<?print($opt_lernmodul);?> &euro; / Auswahl)<br>
				<input type='hidden' size=8  name=zahlungen[is_lern_auswahl] value='<?print($zahlungen[is_lern_auswahl])?>' onchange="calc();">
				<select multiple name=opt_lernmodul_select[]>
<?
$m1_trim_selected=rtrim($zahlungen[is_lern_auswahl],";");
$m1_split_selected=split(";",$m1_trim_selected);

$m1_trim=rtrim($opt_lernmodul_auswahl,";");
$m1_split=split(";",$m1_trim);
for ($i=0;$i<count($m1_split);$i++)
{
	$print_selected="";
	for ($ii=0;$ii<count($m1_split_selected);$ii++)
	{
		print($m1_split_selected[$ii]."<br>");
		if (strcmp(trim($m1_split_selected[$ii]),trim($m1_split[$i]))==0)
		{
			$print_selected="selected";
		}
	}
	echo "<option $print_selected value='".$m1_split[$i]."'>".$m1_split[$i]."</option>";
}
?>
<option value=''> </option>
</select>	
<br>
				<input type='text' size=8  name=zahlungen[is_modul3] value='<?print($zahlungen[is_modul3])?>' onchange="calc();">
				Modul 3 (<?print($opt_modul3);?> &euro; / Auswahl)<br>
				<input type='hidden' size=8  name=zahlungen[is_modul3_auswahl] value='<?print($zahlungen[is_modul3_auswahl])?>' onchange="calc();">
				<select multiple name=opt_modul3_select[]>
<?
$m1_trim_selected=rtrim($zahlungen[is_modul3_auswahl],";");
$m1_split_selected=split(";",$m1_trim_selected);

$m1_trim=rtrim($opt_modul3_auswahl,";");
$m1_split=split(";",$m1_trim);
for ($i=0;$i<count($m1_split);$i++)
{
	$print_selected="";
	for ($ii=0;$ii<count($m1_split_selected);$ii++)
	{
		print($m1_split_selected[$ii]."<br>");
		if (strcmp(trim($m1_split_selected[$ii]),trim($m1_split[$i]))==0)
		{
			$print_selected="selected";
		}
	}
	echo "<option $print_selected value='".$m1_split[$i]."'>".$m1_split[$i]."</option>";
}
?>
<option value=''> </option>
</select>	
<br>
				<input type='text' size=8 name=zahlungen[is_modul4] value='<?print($zahlungen[is_modul4])?>' onchange="calc();">
				Modul 4 (<?print($opt_modul4);?> &euro; / Auswahl)<br>
				<input type='hidden' size=8  name=zahlungen[is_modul4_auswahl] value='<?print($zahlungen[is_modul4_auswahl])?>' onchange="calc();">
				<select multiple name=opt_modul4_select[]>
<?
$m1_trim_selected=rtrim($zahlungen[is_modul4_auswahl],";");
$m1_split_selected=split(";",$m1_trim_selected);

$m1_trim=rtrim($opt_modul4_auswahl,";");
$m1_split=split(";",$m1_trim);
for ($i=0;$i<count($m1_split);$i++)
{
	$print_selected="";
	for ($ii=0;$ii<count($m1_split_selected);$ii++)
	{
		print($m1_split_selected[$ii]."<br>");
		if (strcmp(trim($m1_split_selected[$ii]),trim($m1_split[$i]))==0)
		{
			$print_selected="selected";
		}
	}
	echo "<option $print_selected value='".$m1_split[$i]."'>".$m1_split[$i]."</option>";
}
?>
<option value=''> </option>
</select>	
	   </td></tr></table></TD>
<? } ?>
	   </TR>
	   </TABLE>
   </TD>
</TR>

<tr>
   <td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<? } else
   { ?>
<tr>
   <td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD colspan=5 ALIGN=left   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Zweck</TD></TR></TABLE></TD>
</TR>
<TR>
   <TD colspan=5 class=form_row align=center><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <textarea cols=100 rows=3 name=zahlungen[remark]><?echo $zahlungen[remark]?></textarea>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<? } ?> 



<TR>
   <TD colspan=5 height=50 class=form_footer>
   <TABLE WIDTH=100% HEIGHT=100%><TR><TD ALIGN=CENTER>
   <INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD > <TD ALIGN=CENTER>
<? if (isAdmin())
{?>
   <INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
<?} ?>
   <INPUT TYPE="HIDDEN" NAME="zahlungen[web_camp_id]" value=<?echo $zahlungen[web_camp_id]?>>
   <INPUT TYPE="HIDDEN" NAME="id" value=<?echo $id?>>
   <INPUT TYPE="HIDDEN" NAME="camp" value=<?echo $camp?>>
   <INPUT TYPE="HIDDEN" NAME="update" value=1>
   <INPUT TYPE="HIDDEN" NAME="max_payments" value=<?print($max_payments);?>>
   <INPUT TYPE="HIDDEN" NAME="confirm" value=0>
    </TD></TR></TABLE></TD>    </FORM>
</TR>
<tr>
   <td colspan=5 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

</TABLE>
</CENTER>

</BODY>
</HTML>
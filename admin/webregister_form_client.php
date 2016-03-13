<?
/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/* Dateiname: webregister_form.php
*  Zweck: Formular zur Überprüfung und Übernahme der Online-Anmeldungen für Camps
*/

date_default_timezone_set('Europe/Berlin');

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
$change_user = $_SESSION['username'];
$change_date = date('Y-m-d H:m:s');
$used_db = $_SESSION['used_db'];
$update = $_POST['update'];
isAllow(isAdmin() || isSecretary());

$back_url="../admin/webregister_list.php";

/* _POST einlesen */
$save_x = $_POST['save_x'];
$delete_x = $_POST['delete_x'];
$web = $_POST['web'];
$known_name = $_POST['known_name'];
$sim_new = $_POST['sim_new'];
$confirm = $_POST['confirm'];
$opt_nachmittag_select=$_POST['opt_nachmittag_select'];
$opt_lernmodul_select=$_POST['opt_lernmodul_select'];
$opt_modul3_select=$_POST['opt_modul3_select'];
$opt_modul4_select=$_POST['opt_modul4_select'];
$kurs_price=$_POST['kurs_price'];

if (!isset($no_error)) $no_error=1;

if ($print_debug==1) { var_dump($_POST);print("<br><br>");}
?>
<HTML>
<HEAD>
	<link rel="stylesheet" href="../css/ta.css">
	<script src="codebase/dhtmlxcommon.js"></script>
	<script src="codebase/dhtmlxcombo.js"></script>
	<link rel="STYLESHEET" type="text/css" href="codebase/dhtmlxcombo.css">

	<script type="text/javascript">
	function delete_form()
	{
	  document.formular.confirm.value = confirm("Wollen Sie diesen Eintrag wirklich loeschen?");
	}

	function move_form()
	{
	  document.formular.confirm.value = confirm("ACHTUNG! Der aktuelle Kundendatensatz wird von dem ausgewaehlten Datensatz unwiederruflich ueberschrieben!");
	  if (document.formular.confirm.value=true)
	  {
	  	document.formular.submit();
	  }
	}

	function show_message(){
    	document.getElementById('light').style.display='block';
    	document.getElementById('fade').style.display='block';
    }

	/* Formular neu aufbauen, aber ohne Daten aus DB zu laden */
	function loadNew()
	{
	        window.document.formular.update.value=1;
	        window.document.formular.submit();
	}
	
	function calc()
	{
		sconto=parseFloat(window.document.formular.elements['web[rab][early]'].value)+parseFloat(window.document.formular.elements['web[rab][last]'].value)+parseFloat(window.document.formular.elements['web[rab][stamm]'].value)+parseFloat(window.document.formular.elements['web[rab][gesch]'].value)+parseFloat(window.document.formular.elements['web[rab][kombi1]'].value)+parseFloat(window.document.formular.elements['web[rab][kombi2]'].value)+parseFloat(window.document.formular.elements['web[rab][verl]'].value)+parseFloat(window.document.formular.elements['web[rab][halb]'].value)+parseFloat(window.document.formular.elements['web[rab][firmen]'].value)+parseFloat(window.document.formular.elements['web[rab][sonder]'].value);

		options=parseFloat(window.document.formular.elements['web[opt][verpflegung]'].value)+parseFloat(window.document.formular.elements['web[opt][nachmittag]'].value)+parseFloat(window.document.formular.elements['web[opt][lernmodul]'].value)+parseFloat(window.document.formular.elements['web[opt][modul3]'].value)+parseFloat(window.document.formular.elements['web[opt][modul4]'].value)+parseFloat(window.document.formular.elements['web[opt][trans_flug_hin]'].value)+parseFloat(window.document.formular.elements['web[opt][trans_flug_ret]'].value)+parseFloat(window.document.formular.elements['web[opt][trans_flug_hin_minor]'].value)+parseFloat(window.document.formular.elements['web[opt][trans_flug_ret_minor]'].value)+parseFloat(window.document.formular.elements['web[opt][trans_bahn_hin]'].value)+parseFloat(window.document.formular.elements['web[opt][trans_bahn_ret]'].value)+parseFloat(window.document.formular.elements['web[opt][zertifikat]'].value);
	
//		alert(sconto);
//		alert(options);
		window.document.formular.elements['web[price]'].value = parseFloat(window.document.formular.elements['kurs_price'].value) - sconto + options;	
	}	
	</script>
</HEAD>

<?
// Delete-Button wird gedrückt
if (isset($delete_x))
{
        if ($confirm=="true" && $web[id]>0)
        {
		    $sql_webregister_finish = "update web_camp set status='Storno', reg_date=sysdate(), reg_user='$change_user' where id=".$web[id];
		    $rs_webregister_finish=getrs($sql_webregister_finish,$print_debug,$print_error);
           if ($rs_delete->errno==0)
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
 /* Formulardaten per _POST einlesen */
	
/*	$no_error= CheckEmpty($firstname,$error_firstname) &&
  			 CheckEmpty($birthdate_day,$error_birthdate_day) &&
	  		 CheckEmpty($birthdate_month,$error_birthdate_month) &&
  			 CheckEmpty($birthdate_year,$error_birthdate_year) &&
  			 CheckEmpty($lastname,$error_lastname) &&
  			 CheckEmpty($status,$error_status) &&
  			 CheckEmpty($phone1,$error_phone1);
*/
  if (($known_name=="1") && (!isset($sim_new))) // Kunde bekannt und kein neuer Datensatz erwünscht...
  {
	// speichern des clients "web[sim_name_id]" unter clients, id ist die ID der web_camp Anmeldung
       if ($web[stammkunde]!="") { $is_stammkunde="on";} else { $is_stammkunde="";}
	   
	   $sql_client_update = "update clients set
                 firstname='".$web[firstname]."',
                 lastname ='".strtoupper($web[lastname])."',
                 firstname_parent='".$web[firstname_parent]."',
                 lastname_parent='".$web[lastname_parent]."',
                 address  ='".$web[adress]."',
                 zip      ='".$web[zip]."',
                 city     ='".$web[city]."',
                 country  ='".$web[country]."',
                 phone1   ='".$web[phone]."',
                 phone2   ='".$web[phone2]."',
                 email    ='".$web[email]."',
                 sex      ='".$web[sex]."',
                 birthdate='".$web[birthdate]."',
                 status   ='Aktiv',
				 sv_number = '".$web[sv_number]."',
				 campdaten_tetanus='$web[tetanus]',
				 campdaten_zecken='$web[zecken]',
				 campdaten_stammkunde='$is_stammkunde',
				 campdaten_schwimmen='$web[schwimmfaehigkeit]',
				 language_id='$web[language_id]',
				 sprachlevel_deutsch='$web[sprachlevel_deutsch]',
				 sprachlevel_englisch='$web[sprachlevel_englisch]',
				 campdaten_geschwister='$web[geschwister]',
				 campdaten_allergien='$web[allergien]',
				 change_user='$change_user',
				 web_reg=2,
				 remarks='$web[remarks_intern]'
				 where id=".$web[sim_name_id];
      
      $rs_client_update=getrs($sql_client_update,$print_debug,$print_error);
  }
  elseif (($known_name=="0") || (isset($sim_new))) // Kunde ist Neu ODER Kunde ist bekannt, aber soll trotzdem neu angelegt werden...
  {
     $sql_client_insert="insert into clients
          (firstname,lastname,firstname_parent,lastname_parent,address,zip,city,country,phone1,phone2,email,sex,birthdate,school_id,status,sv_number,campdaten_tetanus,campdaten_zecken,language_id,campdaten_schwimmen,sprachlevel_deutsch,sprachlevel_englisch,campdaten_geschwister,campdaten_allergien,campdaten_stammkunde,change_user,web_reg,remarks)
          values
          ('$web[firstname]','".strtoupper($web[lastname])."','$web[firstname_parent]','$web[lastname_parent]','$web[adress]','$web[zip]','$web[city]','$web[country]','$web[phone]','$web[phone2]','$web[email]','$web[sex]','$web[birthdate]','$web[school_id]','Aktiv','$web[sv_number]','$web[tetanus]','$web[zecken]','$web[language_id]','$web[schwimmfaehigkeit]','$web[sprachlevel_deutsch]','$web[sprachlevel_englisch]','$web[geschwister]','$web[allergien]','$is_stammkunde','$change_user',1,'$web[remarks_intern]')";
     $rs_client_insert = getrs($sql_client_insert,$print_debug,$print_error);
     $web[sim_name_id]=mysqli_insert_id($DB_TA_CONNECT);
  }
 
// Module...
// var_dump($opt_nachmittag_select); 

  $web[opt][nachmittag_auswahl]=$opt_nachmittag_select[0];
  for ($i=1;$i<=count($opt_nachmittag_select);$i++)
  {
	$web[opt][nachmittag_auswahl]=$web[opt][nachmittag_auswahl].";".$opt_nachmittag_select[$i];
  } 
  $web[opt][lernmodul_auswahl]=$opt_lernmodul_select[0];
  for ($i=1;$i<=count($opt_lernmodul_select);$i++)
  {
	$web[opt][lernmodul_auswahl]=$web[opt][lernmodul_auswahl].";".$opt_lernmodul_select[$i];
  } 
  $web[opt][modul3_auswahl]=$opt_modul3_select[0];
  for ($i=1;$i<=count($opt_modul3_select);$i++)
  {
	$web[opt][modul3_auswahl]=$web[opt][modul3_auswahl].";".$opt_modul3_select[$i];
  } 
  $web[opt][modul4_auswahl]=$opt_modul4_select[0];
  for ($i=1;$i<=count($opt_modul4_select);$i++)
  {
	$web[opt][modul4_auswahl]=$web[opt][modul4_auswahl].";".$opt_modul4_select[$i];
  } 

  $sql_last_element="select rechnung_id from payments order by rechnung_id desc limit 1";
  $rs_last_element=getrs($sql_last_element,$print_debug,$print_error);
  list($last_id)=$rs_last_element->fetch_row();
  $new_id=$last_id+1;

  $sql_payment_insert="
			insert into payments
               (rechnung_id,clients_id,courses_id,billdate,amount,sconto_id,sconto_amount,opt_amount,client_price,remarks,status,mahnung_sent1,mahnung_sent2,
			    mahnung_sent3,mahnung_comment,rab_earlybook,rab_lastminute,rab_stammkunde,rab_geschwister,rab_kombi1,rab_kombi2,rab_verlaengerung,rab_halbtag,rab_firmen,rab_sonder,create_date,reg_date,reg_user,web_camp_id,register)
            values
			   ('$new_id','$web[sim_name_id]','$web[courses_id]','0000-00-00','0','0','$web[sconto_value]','$web[opt_value]','$web[price]','$web[remarks]','F','0','0','0','0','"
.$web[rab][early]."','".$web[rab][last]."','".$web[rab][stamm]."','".$web[rab][gesch]."','".$web[rab][kombi1]."','".$web[rab][kombi2]."','".$web[rab][verl]."','".$web[rab][halb]."','".$web[rab][firmen]."','".$web[rab][sonder]."',sysdate(),sysdate(),'$change_user','".$id."','on')";
  $rs_payment_insert=getrs($sql_payment_insert,$print_debug,$print_error);

  $sql_payment_options_insert="
			insert into payments_opt_camps
               (rechnung_id,sel_verpflegung,sel_nachmittag,sel_nachmittag_auswahl,sel_lernmodul,sel_lernmodul_auswahl,sel_modul3,sel_modul3_auswahl,sel_modul4,sel_modul4_auswahl,sel_flughafen_hin,sel_flughafen_ret,sel_flughafen_hin_minor,sel_flughafen_ret_minor,sel_bahnhof_hin,sel_bahnhof_ret,sel_zertifikat,sel_zertifikat_auswahl)
            values
			   ('$new_id','".$web[opt][verpflegung]."','".$web[opt][nachmittag]."','".$web[opt][nachmittag_auswahl]."','".$web[opt][lernmodul]."','".$web[opt][lernmodul_auswahl]."','".$web[opt][modul3]."','".$web[opt][modul3_auswahl]."','".$web[opt][modul4]."','".$web[opt][modul4_auswahl]."','".$web[opt][trans_flug_hin]."','".$web[opt][trans_flug_ret]."','".$web[opt][trans_flug_hin_minor]."','".$web[opt][trans_flug_ret_minor]."','".$web[opt][trans_bahn_hin]."','".$web[opt][trans_bahn_ret]."',
				'".$web[opt][zertifikat]."','".$web[opt][zertifikat_auswahl]."')";
  $rs_payment_options_insert=getrs($sql_payment_options_insert,$print_debug,$print_error);

  $sql_webregister_finish = "update web_camp set rechnung_id='$new_id', status='Bearbeitet', reg_date=sysdate(), reg_user='$change_user' where id=".$id;
  $rs_webregister_finish=getrs($sql_webregister_finish,$print_debug,$print_error);

  // Absenden der Bestätigungsmail
  
  $sql_mailinfo="select a.name,b.year,c.name from products a, courses b, timeperiods c where b.id=".$web[courses_id]." and a.id=b.products_id and b.timeperiods_id=c.id";
  $rs_mailinfo=getrs($sql_mailinfo,$print_debug,$print_error);
  list($productinfo,$yearinfo,$timeinfo)=$rs_mailinfo->fetch_row();
  
  if ($web[sex]==1) { $gender="Ihres Sohnes";} else { $gender="Ihrer Tochter";}
  
  $betreff = "Buchungsbestaetigung / Booking confirmation ".$productinfo." / ".$timeinfo." / ".$yearinfo;
  $link=base64_encode("print_debug=0&id=".$id); 
  $absenderadresse = 'camps@teamactivities.at';
  $absendername = 'camps@teamactivities.at';

  $header = array();
  $header[] = "From: ".mb_encode_mimeheader($absendername, "iso-8859-1", "Q")." <".$absenderadresse.">";
  $header[] = "MIME-Version: 1.0";
  $header[] = "Content-type: text/plain; charset=iso-8859-1";
  $header[] = "Content-transfer-encoding: 8bit";
  
  $text = "
  Sehr geehrte Familie ".$web[lastname_parent].",
  
  vielen herzlichen Dank f&uuml;r die Anmeldung ".$gender." ".$web[firstname]." bei unserem ".$productinfo." ! 
  Mit den folgenden Links schicke ich Ihnen die Buchungsbest&auml;tigung sowie Tipps und Informationen.

  Buchungsbest&auml;tigung:
  http://www.teamactivities.at/online/booking_confirmation.php?data=".$link."
  Tips und Informationen:
  http://www.teamactivities.at/informationsmappe.php
 
  Wir w&uuml;nschen ".$web[firstname]." viel Spa&szlig; bei unserem ".$productinfo." ! 
  Bei R&uuml;ckfragen stehen wir Ihnen sehr gerne telefonisch oder per Email zur Verf&uuml;gung.

 -----------------------------------------------------------------------------------------------------------------------------
 
  Dear Mr and Ms ".$web[lastname_parent].",

  Thank you for booking our ".$productinfo." !
  Below you find the links to your booking confirmation for ".$web[firstname]." as well as our brochure &quot;Tips and<br>information&quot;.

  Booking confirmation:
  http://www.teamactivities.at/online/booking_confirmation.php?data=".$link."
  Tips and informations:
  http://www.teamactivities.at/informationsmappe.php

  We wish ".$web[firstname]." a lot of fun at our ".$productinfo." !
  If you have any questions please don&apos;t hesitate to contact me.

  Beste Gr&uuml;&szlig;e / Best regards from Vienna,

  Boris Duniecki<br>
  Leitung Ferienbetreuung / Camps Coordinator

  Team Activities
  Stachegasse 17
  1120 Wien / Vienna
  AUSTRIA
  Tel./Fax: +43 1 786 67 39
  camps@teamactivities.at
  www.teamactivities.at
  ";
  $text = str_replace("<br>", "\n", $text);
  
  mail("camps@teamactivities.at", mb_encode_mimeheader($betreff, "iso-8859-1", "Q"), html_entity_decode($text), implode("\n", $header));
  $email=trim($web[email]);
  mail($email, mb_encode_mimeheader($betreff, "iso-8859-1", "Q"), html_entity_decode($text), implode("\n", $header));
  
}

// Back-Button wird gedrückt
elseif (isset($back_x))
{
  header("Location: $back_url");
}

// Daten aus DB Laden
if (isset($id) && $id!="" && $no_error && !isset($save_x) && !($update))
{
           $sql_client_select="select   a.id,
										a.web_clients_id,
										a.courses_id,
										a.effective_price,
										a.is_earlybook,
										a.is_lastminute,
										a.is_stammkunde,
										a.is_geschwister,
										a.is_kombi1,
										a.is_kombi2,
										a.is_verlaengerung,
										a.is_halbtag,
										a.is_firmen,
										a.is_sonder,
										a.sel_verpflegung,
										a.sel_nachmittag,
										a.sel_nachmittag_auswahl,
										a.sel_lernmodul,
										a.sel_lernmodul_auswahl,
										a.sel_modul3,
										a.sel_modul3_auswahl,
										a.sel_modul4,
										a.sel_modul4_auswahl,
										a.sel_flughafen_there,
										a.sel_flughafen_back,
										a.sel_flughafen_minor_there,
										a.sel_flughafen_minor_back,
										a.sel_bahnhof_there,
										a.sel_bahnhof_back,
										a.sel_zertifikat,
										a.sel_zertifikat_auswahl,
										c.firstname,
										c.lastname,
										c.firstname_parent,
										c.lastname_parent,
										c.adress,
										c.zip,
										c.city,
										c.country,
										c.phone1,
										c.phone2,
										c.email,
										c.sex,
										c.birthdate,
										c.school_name,
										c.campdaten_remarks,
										a.status,
										c.sv_number,
										c.campdaten_tetanus,
										c.campdaten_zecken,
										c.campdaten_stammkunde_besucht,
										c.campdaten_schwimmen,
										c.campdaten_muttersprache_id,
										c.sprachlevel_deutsch,
										c.sprachlevel_englisch,
										c.campdaten_geschwister,
										c.campdaten_allergien,
										c.campdaten_firmenname,
										a.rechnung_id 
								from web_clients c,web_camp a where a.web_clients_id=c.id and a.web_clients_id=".$id;
           $rs_client_select=getrs($sql_client_select,$print_debug,$print_error);
           
}		   
?>
<?
if (isset($save_x) && $no_error) 
{ 
	$command="show_message();";
}
else
{
	$command="javascript:calc();";
}?>

<BODY onload="<?print($command)?>">
<!--  Div für messagebox und fade des Hintergrundes -->		
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
	<tr><td height=12>
		<SPAN class="headline">Camp-Online-Anmeldung - Checkformular</SPAN><br>
	</td></tr>
	</table>
<?
if ((!isset($save_x)) && (!$update) && ($rs_client_select->num_rows==0))
{
	print("<center><font color=red>Das ist keine Online-Anmeldung !</font></center>");
	die;	
}
?>
	<BR>
	
	<!-- Formular Anfang -->
	<FORM  action="<? echo $PHP_SELF?>" method="POST" name=formular>
	<div align=center>
	</div>
	
	<BR>
	<TABLE width=400 border=0 CELLPADDING=0 CELLSPACING=0>
	<tr>
		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<td colspan=3 HEIGHT=1  class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

<?
	$web_count=1;
	while ($rs_client_select>0 && LIST($web[$web_count][id],$web[web_clients_id],$web[$web_count][courses_id],$web[$web_count][price],
				$web[$web_count][rab][early],$web[$web_count][rab][last],$web[$web_count][rab][stamm],$web[$web_count][rab][gesch],$web[$web_count][rab][kombi1],
				$web[$web_count][rab][kombi2],$web[$web_count][rab][verl],$web[$web_count][rab][halb],$web[$web_count][rab][firmen],$web[$web_count][rab][sonder],
				$web[$web_count][opt][verpflegung],$web[$web_count][opt][nachmittag],$web[$web_count][opt][nachmittag_auswahl],$web[$web_count][opt][lernmodul],$web[$web_count][opt][lernmodul_auswahl],$web[$web_count][opt][modul3],$web[$web_count][opt][modul3_auswahl],$web[$web_count][opt][modul4],$web[$web_count][opt][modul4_auswahl],
				$web[$web_count][opt][trans_flug_hin],$web[$web_count][opt][trans_flug_ret],$web[$web_count][opt][trans_flug_hin_minor],$web[$web_count][opt][trans_flug_ret_minor],$web[$web_count][opt][trans_bahn_hin],$web[$web_count][opt][trans_bahn_ret],
				$web[$web_count][opt][zertifikat],$web[$web_count][opt][zertifikat_auswahl],
				$web[firstname],$web[lastname],$web[firstname_parent],$web[lastname_parent],$web[adress],$web[zip],$web[city],$web[country],$web[phone],$web[phone2],
				$web[email],$web[sex],$web[birthdate],$web[school_name],$web[remarks],$web[status],$web[sv_number],$web[tetanus],$web[zecken],
				$web[stammkunde],$web[schwimmfaehigkeit],$web[language_id],$web[sprachlevel_deutsch],$web[sprachlevel_englisch],$web[geschwister],$web[allergien],$web[rabattcode],$web[rechnung_id])=$rs_client_select -> fetch_row())
	{			
				$firstname=ucfirst($web[firstname]);
				$lastname=strtoupper($web[lastname]);
?>
<?if ($web_count==1)
{
?>	
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>ID </TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<input type=hidden name=web[id] value=<?echo $web[id]?>>	
		<?echo $web[id]?>
		<?if ((isset($save_x))||($web[status]=="Bearbeitet")) { print("<font color=red>Diese Anmeldung wurde bereits in die DB &uuml;bernommen!</font>");}?> 
		</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Vorname / Nachname</font></TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_firstname);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=web[firstname] VALUE="<?echo $web[firstname]?>">
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=web[lastname] VALUE="<?echo $web[lastname]?>"><br>
<? if ($web[status]!="Neu")
{?>
			<input type="button" value="-Z>" onclick="javascript:window.open('payment_form.php?id=<?print($web[rechnung_id])?>')" target=_blank>
<?
}
if ((isset($web[firstname]) && isset($web[lastname])) && $web[status]=="Neu")
  {
          if (($update) && ($web[sim_name_id]>0)) // Wenn schonmal die Auswahl aus mehreren möglichen Kunden passiert ist, dann nur den einen Kunden laden...
	  {	  
	     $rs_clients_search=getrs("select id,
   					 firstname,
                     lastname,
					 birthdate,
					 firstname_parent,lastname_parent,address,zip,city,country,phone1,phone2,email,sex,sv_number,school_id
				   from clients a
				   where a.id=".$web[sim_name_id],$print_debug,$print_error);
		 $num_result=$rs_clients_search->num_rows;		   
	  } else if (($update) && ($web[sim_name_id]=-1))
	  {
		 $num_result=0;
	  } else
	  {
	     $rs_clients_search=getrs("select id,
				      					 firstname,
				                        lastname,
				   					 birthdate,
				   					 firstname_parent,lastname_parent,address,zip,city,country,phone1,phone2,email,sex,sv_number,school_id
				   				   from clients a
				   				   where a.firstname Like '%".$web[firstname]."%' and a.lastname Like '%".$web[lastname]."%' and
				   						 a.status='Aktiv'",$print_debug,$print_error);
	     $num_result=$rs_clients_search->num_rows;
	  }
	  if ($num_result==0) { print ("<font color='black'>Kunde nicht bekannt !</font>"); $known_name=0; }
	  if ($num_result==1) 
	  { 
		print ("<font color='green'>Kunde bekannt!</font>"); $known_name=1; 
		LIST($c_id,$c_fn,$c_ln,$c_bd,$c_fn_parent,$c_ln_parent,$c_adress,$c_zip,$c_city,$c_country,$c_phone,$c_phone2,$c_email,$c_sex,$c_sv,$c_school_id)=$rs_clients_search -> fetch_row();
?>			<input type="button" value="-P>" onclick="javascript:window.open('client_form.php?id=<?print($c_id)?>')" target=_blank>
			<input type="hidden" value="<?print($c_id)?>" name="web[sim_name_id]"><br>
			<input type="checkbox" value="1" name="sim_new"> Trotzdem Kunde neu anlegen!
<?		
	  }
	  if ($num_result>1) 
	  { 
	  	print ("<font color='red'>&auml;hnlicher/gleicher Kunde mehrfach vorhanden!Bitte richtigen Kundenamen ausw&auml;hlen oder neuen Kunden anlegen:</font><br>"); $known_name=2; 
?>
			<SELECT NAME=web[sim_name_id] onchange="javascript:loadNew()">
			<OPTION VALUE=0>BITTE AUSW&Auml;HLEN</OPTION>	
			<option value=-1>Neuer Kunde anlegen</option>
		<?while(LIST($c_id,$c_fn,$c_ln,$c_bd,$c_fn_parent,$c_ln_parent,$c_adress,$c_zip,$c_city,$c_country,$c_phone,$c_phone2,$c_email,$c_sex,$c_sv)=$rs_clients_search -> fetch_row())
		{
			if ($web[sim_name_id]==$c_id) 
			{
				echo "<OPTION selected VALUE=$c_id>".$c_fn." ".strtoupper($c_ln)." / ".$c_bd."</OPTION>";
			}
			else
			{
				echo "<OPTION VALUE=".$c_id.">".$c_fn." ".strtoupper($c_ln)." / ".$c_bd."</OPTION>";
			}
		} 
?>
			</SELECT>
<?	
	  }
  }?>
			<INPUT TYPE=hidden NAME=known_name VALUE="<?echo $known_name?>">
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Elternteil <br>Vorname / Nachname</font></TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=web[firstname_parent] VALUE="<?echo $web[firstname_parent]?>">
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=web[lastname_parent] VALUE="<?echo $web[lastname_parent]?>">
			</TD></TR>
			<TR><td>
			<? 
			if ((strcmp($c_fn_parent,$web[firstname_parent])) || (strcmp($c_ln_parent,$web[lastname_parent])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
			if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_fn_parent." ".$c_ln_parent."</font>"); ?>
			</Td></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Geburtstag</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=web[birthdate] VALUE="<?echo $web[birthdate]?>">
			</TD></TR>
			<tr><td>
			<?
			if ((strcmp($c_bd,$web[birthdate])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
			if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_bd."</font>"); ?>
			</tr></td>
		</TABLE>	
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Adresse</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
				<TEXTAREA ROWS=2 COLS=20  NAME=web[adress]><?echo $web[adress]?></TEXTAREA>
			</TD></TR>
			<TR><TD>	
			<? 
			if ((strcmp($c_adress,$web[adress])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
				if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_adress."</font>"); ?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>PLZ / Ort</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=5 SIZE=5 NAME=web[zip] VALUE="<?echo $web[zip]?>">
			<INPUT TYPE=TEXT MAXLENGTH=250 SIZE=30 NAME=web[city] VALUE="<?echo $web[city]?>">
			</TD></TR>
			<TR><TD>
			<? 
			if ((strcmp($c_zip,$web[zip])) || (strcmp($c_city,$web[city])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
				if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_zip." / ".$c_city."</font>"); ?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Land</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=255 SIZE=255 NAME=web[country] VALUE="<?echo $web[country]?>">
			</TD></TR>
			<TR><TD>
			<? 
			if ((strcmp($c_country,$web[country])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
				if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_country."</font>"); ?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Telefon</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_phone1);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=60 NAME=web[phone] VALUE="<?echo $web[phone]?>">
			</TD></TR>
			<TR><TD>
			<? 
			if ((strcmp($c_phone,$web[phone])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
			if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_phone."</font>"); ?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Telefon 2</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_phone2);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=60 NAME=web[phone2] VALUE="<?echo $web[phone2]?>">
			</TD></TR>
			<TR><TD>
			<? 
			if ((strcmp($c_phone2,$web[phone2])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
			if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_phone2."</font>"); ?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>E-Mail</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=100 NAME=web[email] VALUE="<?echo $web[email]?>">
			(Trennzeichen = ;)</TD></TR>
			<TR><TD>			
			<? 
			if ((strcmp($c_email,$web[email])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
			if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_email."</font>"); ?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Geschlecht</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<SELECT NAME=web[sex]>
				<OPTION <?if ($web[sex]==1) echo "selected";?> VALUE=1>M&auml;nnlich</OPTION>
				<OPTION <?if ($web[sex]==0) echo "selected";?> VALUE=0>Weiblich</OPTION>
			</SELECT>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header >
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>SV-Nummer</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=web[sv_number] VALUE="<?echo $web[sv_number]?>">
			</TD></TR>
			<TR><TD>
			<?
			if ((strcmp($c_sv,$web[sv_number])))
			{
				$color=" color=red";
			} else
			{
				$color=" color=green";
			}
			if ($known_name==1) print ("<font ".$color.">gespeicherte Daten: ".$c_sv."</font>"); ?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Institution</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<input type=text size=100 name="web[school_name]" value="<?print($web[school_name])?>">
		        <input type="button" value="Neue Institution" onclick="javascript:window.open('institution_form.php')" target=_blank>
                        <br>
			<SELECT NAME=web[school_id]>
			<OPTION>BITTE AUSW&Auml;HLEN</OPTION>
			<?
   $rs_client_school_search=getrs("select id, name from institutions where name like '".$web[school_name]."' or id='".$c_school_id."'",$print_debug,$print_error);
   if ($rs_client_school_search->num_rows>0)
   {
		LIST($inst_id_search,$name_search)=$rs_client_school_search -> fetch_row();
   }
   $rs_client_school_select=getrs("select id, name from institutions where status in ('Aktiv', 'Inaktiv') order by name asc",$print_debug,$print_error);
   while(LIST($inst_id,$name)=$rs_client_school_select -> fetch_row())
   {
        if ($web[school_id]==0)
	    {
			$inst_compare=$inst_id_search;
		} else
		{
			$inst_compare=$web[school_id];
		}
	    if ($inst_compare==$inst_id)
			echo "<OPTION selected VALUE=$inst_id>$name</OPTION>";
        else
	        echo "<OPTION VALUE=$inst_id>$name</OPTION>";
   } ?>
			</SELECT>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Camp-relevante Daten:</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<input type='checkbox' <?if ($web[tetanus]=="1") {print(" checked ");}?> name=web[tetanus] value='1' ><nobr>Tetanus-Impfung<br>
			<input type='checkbox' <?if ($web[zecken]=="1") {print(" checked ");}?> name=web[zecken] value='1' ><nobr>Zecken-Impfung<br>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Schwimmf&auml;higkeit</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<SELECT NAME=web[schwimmfaehigkeit]>
				<OPTION VALUE="0" <?if ($web[schwimmfaehigkeit]=="0") print("SELECTED");?>>Nichtschwimmer</OPTION>
				<OPTION VALUE="1" <?if ($web[schwimmfaehigkeit]=="1") print("SELECTED");?>>schlechter Schwimmer</OPTION>
				<OPTION VALUE="2" <?if ($web[schwimmfaehigkeit]=="2") print("SELECTED");?>>guter Schwimmer</OPTION>
			</SELECT>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Allergien</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
				<TEXTAREA ROWS=2 COLS=40  NAME=web[allergien]><?echo $web[allergien]?></TEXTAREA>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Geschwister</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
				<TEXTAREA ROWS=2 COLS=40  NAME=web[geschwister]><?echo $web[geschwister]?></TEXTAREA>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>zuletzt besuchte Kurse (Stammkunde?)</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
				<TEXTAREA ROWS=2 COLS=40  NAME=web[stammkunde]><?echo $web[stammkunde]?></TEXTAREA><br>
				(leer lassen, wenn kein Stammkunde, bzw. keine Kurse zuletzt besucht)
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Muttersprache</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<SELECT NAME=web[language_id]>
   <?
   if ($web[language_id]==0) $web[language_id]=1;
   $rs_language_select=getrs("select id, name from language where status in ('Aktiv', 'Inaktiv','') order by name asc",$print_debug,$print_error);
   while(LIST($lang_id,$lang_name)=$rs_language_select -> fetch_row())
   {
       if ($web[language_id]==$lang_id)
			echo "<OPTION selected VALUE=$lang_id>$lang_name</OPTION>";
       else
	        echo "<OPTION VALUE=$lang_id>$lang_name</OPTION>";
   } ?>
			</SELECT>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Sprachlevel Deutsch</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<SELECT NAME=web[sprachlevel_deutsch]>
				<OPTION VALUE="1" <?if ($web[sprachlevel_deutsch]=="1") print("SELECTED");?>>A1-Anf&auml;nger I</OPTION>
				<OPTION VALUE="2" <?if ($web[sprachlevel_deutsch]=="2") print("SELECTED");?>>A2-Anf&auml;nger II</OPTION>
				<OPTION VALUE="3" <?if ($web[sprachlevel_deutsch]=="3") print("SELECTED");?>>B1-Fortgeschritten I</OPTION>
				<OPTION VALUE="4" <?if ($web[sprachlevel_deutsch]=="4") print("SELECTED");?>>B2-Fortgeschritten II</OPTION>
				<OPTION VALUE="5" <?if ($web[sprachlevel_deutsch]=="5") print("SELECTED");?>>C1-Native</OPTION>
			</SELECT>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Sprachlevel Englisch</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<SELECT NAME=web[sprachlevel_englisch]>
				<OPTION VALUE="1" <?if ($web[sprachlevel_englisch]=="1") print("SELECTED");?>>A1-Anf&auml;nger I</OPTION>
				<OPTION VALUE="2" <?if ($web[sprachlevel_englisch]=="2") print("SELECTED");?>>A2-Anf&auml;nger II</OPTION>
				<OPTION VALUE="3" <?if ($web[sprachlevel_englisch]=="3") print("SELECTED");?>>B1-Fortgeschritten I</OPTION>
				<OPTION VALUE="4" <?if ($web[sprachlevel_englisch]=="4") print("SELECTED");?>>B2-Fortgeschritten II</OPTION>
				<OPTION VALUE="5" <?if ($web[sprachlevel_englisch]=="5") print("SELECTED");?>>C1-Native</OPTION>
			</SELECT>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header >
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Firmenname</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=web[rabattcode] VALUE="<?echo $web[rabattcode]?>">
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Anmerkung vom Kunden (scheinen im Kursblatt auf):</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
				<TEXTAREA ROWS=5 COLS=40  NAME=web[remarks]><?echo $web[remarks]?></TEXTAREA>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Anmerkung vom TA-B&uuml;ro (scheinen im Kundenformular auf):</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
				<TEXTAREA ROWS=5 COLS=40  NAME=web[remarks_intern]><?echo $web[remarks_intern]?></TEXTAREA>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=CENTER   class=form_header colspan=3>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100% align=center>Camp Anmeldungen von <?echo $web[firstname]." ".$web[lastname]?></TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
<?} // wird nur einmal ausgeführt ?>
<!-- für jede Campanmeldung -->

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header >
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Anmeldung-Nummer:</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<? echo $web_count; ?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>gebuchtes Camp</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<SELECT NAME=web[<?print($web_count);?>][courses_id] onchange="javascript:loadNew();">
   <?
   $rs_camps_select=getrs("select c.id,a.name, b.name, c.year, d.name,c.price from products a, timeperiods b, courses c, institutions d where c.status='Aktiv' and b.camp=1 and a.id=c.products_id and b.id=c.timeperiods_id and d.id=c.locations_id order by c.year desc,a.name asc",$print_debug,$print_error);
   while(LIST($camp_id,$prod,$time,$year,$inst,$orig_price)=$rs_camps_select -> fetch_row())
   {
       if ($web[courses_id]==$camp_id)
		{   
			echo "<OPTION selected VALUE=$camp_id>".$prod." / ".$year." / ".$time." / ".$inst." -> ".$orig_price."</OPTION>";
			$print_orig_price=$orig_price;
		}
	   else
	   {
	        echo "<OPTION VALUE=$camp_id>".$prod." / ".$year." / ".$time." / ".$inst." -> ".$orig_price."</OPTION>";
	   }
   } ?>
			</SELECT><br>
			<input type="hidden" value="<?print($print_orig_price)?>" name=kurs_price[<?print($web_count)?>]>
			<input type="button" value="->Camp" onclick="javascript:window.open('camp_form.php?id=<?print($web[$web_count][courses_id])?>')" target=_blank>
			<input type="button" value="->Campblatt" onclick="javascript:window.open('kursblatt_form.php?id=<?print($web[$web_count][courses_id])?>')" target=_blank>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Preis (laut Online-Anmeldung)</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=web[<?print($web_count);?>][price] VALUE="<?echo $web[$web_count][price]?>">
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
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
	where id=".$web[$web_count][courses_id],$print_debug,$print_error);
	LIST($opt_verpflegung,$opt_nachmittag,$opt_lernmodul,$opt_modul3,$opt_modul4,$opt_nachmittag_auswahl,$opt_lernmodul_auswahl,$opt_modul3_auswahl,$opt_modul4_auswahl,$opt_flg_hin,$opt_flg_ret,$opt_flg_minor_hin,$opt_flg_minor_ret,$opt_bahn_hin,$opt_bahn_ret,$opt_zertifikat,$rab_early,$rab_last,$rab_stamm,$rab_geschw,$rab_kombi1,$rab_kombi2,$rab_verl,$rab_halb,$rab_firmen,$rab_sonder)=$rs_courses_optionen_select -> fetch_row();
		
?>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Rabatte:</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR>
			<TD WIDTH=50% HEIGHT=100%>
				<nobr>Fr&uuml;hbucher (<?print($rab_early)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][early] size=8 value="<?print($web[$web_count][rab][early])?>" onchange="calc();"><br>
				<nobr>Last-Minute (<?print($rab_last)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][last] size=8 value="<?print($web[$web_count][rab][last])?>" onchange="calc();"><br>
				<nobr>Stammkunde (<?print($rab_stamm)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][stamm] size=8 value="<?print($web[$web_count][rab][stamm])?>" onchange="calc();"><br>
				<nobr>Geschwister (<?print($rab_geschw)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][gesch] size=8 value="<?print($web[$web_count][rab][gesch])?>" onchange="calc();"><br>
				<nobr>Kombi 1 (<?print($rab_kombi1)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][kombi1] size=8 value="<?print($web[$web_count][rab][kombi1])?>" onchange="calc();"><br>
			</TD>
			<TD WIDTH=50% HEIGHT=100%>
				<nobr>Kombi 2 (<?print($rab_kombi2)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][kombi2] size=8 value="<?print($web[$web_count][rab][kombi2])?>" onchange="calc();"><br>
				<nobr>Verl&auml;ngerungswoche (<?print($rab_verl)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][verl] size=8 value="<?print($web[$web_count][rab][verl])?>" onchange="calc();"><br>
				<nobr>Nur Halbtags (<?print($rab_halb)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][halb] size=8 value="<?print($web[$web_count][rab][halb])?>" onchange="calc();"><br>
				<nobr>Firmenbonus (<?print($rab_firmen)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][firmen] size=8 value="<?print($web[$web_count][rab][firmen])?>" onchange="calc();"><br>
				<nobr>Sonderrabatt (<?print($rab_sonder)?> &euro;)<br><input type=text name=web[<?print($web_count);?>][rab][sonder] size=8 value="<?print($web[$web_count][rab][sonder])?>" onchange="calc();"><br>
			</TD>
			</TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Transport:</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR>
			<TD WIDTH=100% HEIGHT=100%>
				<nobr>Flughafen Hinfahrt (<?print($opt_flg_hin)?> &euro;)<br>
				<input type='text' size=8 name=web[<?print($web_count);?>][opt][trans_flug_hin] value='<? print($web[$web_count][opt][trans_flug_hin]);?>' onchange="calc();" ><br>
				<nobr>Flughafen R&uuml;ckfahrt (<?print($opt_flg_ret)?> &euro;)<br>
				<input type='text' size=8  name=web[<?print($web_count);?>][opt][trans_flug_ret] value='<?print($web[$web_count][opt][trans_flug_ret]);?>' onchange="calc();" ><br>
				<nobr>Flughafen Hinfahrt Minderj&auml;hrig (<?print($opt_flg_minor_hin)?> &euro;)<br>
				<input type='text' size=8 name=web[<?print($web_count);?>][opt][trans_flug_hin_minor] value='<?print($web[$web_count][opt][trans_flug_hin_minor]);?>' onchange="calc();" ><br>
				<nobr>Flughafen R&uuml;ckfahrt Minderj&auml;hrig (<?print($opt_flg_minor_ret)?> &euro;)<br>
				<input type='text' size=8  name=web[<?print($web_count);?>][opt][trans_flug_ret_minor] value='<?print($web[$web_count][opt][trans_flug_ret_minor]);?>' onchange="calc();" ><br>
				<nobr>Bahnhof Hinfahrt (<?print($opt_bahn_hin)?> &euro;)<br>
				<input type='text' size=8  name=web[<?print($web_count);?>][opt][trans_bahn_hin] value='<?print($web[$web_count][opt][trans_bahn_hin]);?>' onchange="calc();" ><br>
				<nobr>Bahnhof R&uuml;ckfahrt (<?print($opt_bahn_ret)?> &euro;)<br>
				<input type='text' size=8  name=web[<?print($web_count);?>][opt][trans_bahn_ret] value='<?print($web[$web_count][opt][trans_bahn_ret]);?>' onchange="calc();" ><br>
			</TD>
			</TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Optionen:</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR>
			<TD WIDTH=40% HEIGHT=100%>
				<nobr>Modul 1  (<?print($opt_nachmittag)?> &euro;/Modul) / ausgew&auml;hlte Werte: <input type='text' size=8 name=web[<?print($web_count);?>][opt][nachmittag] value='<?print($web[$web_count][opt][nachmittag]);?>' onchange="calc();"><br>
<? //print($opt_nachmittag_auswahl." -- ".$web[opt][nachmittag_auswahl]."<br>")?>
				<select multiple name=opt_nachmittag_select[<?print($web_count);?>][]>
<?
$m1_trim_selected=trim($web[$web_count][opt][nachmittag_auswahl],";");
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
				<nobr>Modul 2  (<?print($opt_lernmodul)?> &euro;/Modul) / ausgew&auml;hlte Werte: <input type='text' size=8 name=web[<?print($web_count);?>][opt][lernmodul] value='<?print($web[$web_count][opt][lernmodul]);?>' onchange="calc();"><br>
				<select multiple name=opt_lernmodul_select[<?print($web_count);?>][]>
<?
$m1_trim_selected=trim($web[$web_count][opt][lernmodul_auswahl],";");
$m1_split_selected=split(";",$m1_trim_selected);

$m1_trim=trim($opt_lernmodul_auswahl,";");
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
				<nobr>Modul 3  (<?print($opt_modul3)?> &euro;/Modul) / ausgew&auml;hlte Werte: <input type='text' size=8 name=web[<?print($web_count);?>][opt][modul3] value='<?print($web[$web_count][opt][modul3]);?>' onchange="calc();"><br>
				<select multiple name=opt_modul3_select[<?print($web_count);?>][]>
<?
$m1_trim_selected=trim($web[$web_count][opt][modul3_auswahl],";");
$m1_split_selected=split(";",$m1_trim_selected);

$m1_trim=trim($opt_modul3_auswahl,";");
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
				<nobr>Modul 4  (<?print($opt_modul4)?> &euro;/Modul) / ausgew&auml;hlte Werte: <input type='text' size=8 name=web[<?print($web_count);?>][opt][modul4] value='<?print($web[$web_count][opt][modul4]);?>' onchange="calc();"><br>
				<select multiple name=opt_modul4_select[<?print($web_count);?>][]>
<?
$m1_trim_selected=trim($web[$web_count][opt][modul4_auswahl],";");
$m1_split_selected=split(";",$m1_trim_selected);

$m1_trim=trim($opt_modul4_auswahl,";");
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
					<br><br>
				<nobr>Verpflegung  (<?print($opt_verpflegung)?> &euro;)<br><input type='text' size=8 name=web[<?print($web_count);?>][opt][verpflegung] value='<?print($web[$web_count][opt][verpflegung]);?>' onchange="calc();" ><br>
				<nobr>Zertifikat  (<?print($opt_zertifikat)?> &euro;) <input type='text' size=8 name=web[<?print($web_count);?>][opt][zertifikat] value='<?print($web[$web_count][opt][zertifikat]);?>' onchange="calc();" ><br>
				<SELECT NAME=web[<?print($web_count);?>][opt][zertifikat_auswahl]>
					<OPTION VALUE="1" <?if ($web[$web_count][opt][zertifikat_auswahl]=="1") print("SELECTED");?>>A1-Anf&auml;nger I</OPTION>
					<OPTION VALUE="2" <?if ($web[$web_count][opt][zertifikat_auswahl]=="2") print("SELECTED");?>>A2-Anf&auml;nger II</OPTION>
					<OPTION VALUE="3" <?if ($web[$web_count][opt][zertifikat_auswahl]=="3") print("SELECTED");?>>B1-Fortgeschritten I</OPTION>
					<OPTION VALUE="4" <?if ($web[$web_count][opt][zertifikat_auswahl]=="4") print("SELECTED");?>>B2-Fortgeschritten II</OPTION>
					<OPTION VALUE="5" <?if ($web[$web_count][opt][zertifikat_auswahl]=="5") print("SELECTED");?>>C1-Native</OPTION>
					<option value='0' <?if (($web[$web_count][opt][zertifikat_auswahl]=="0") || ($web[$web_count][opt][zertifikat_auswahl]=="")) print("SELECTED");?>> </option>
				</SELECT>
					

			</TD>
			<TD WIDTH=60% HEIGHT=100%>
				<br>
				<br>
				
			</TD>
			</TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

<?
	$web_count++;

} // ende while ?>

<!-- 
Der Aktiv status wird nicht mehr verwendet, bleibt aber im Hintergrund erhalten (Standard ist aktiv=checked!), da gelschte Datenstze auch Entfernt gesetzt werden und nicht physikalisch gelscht werden!!!
-->
	<INPUT TYPE=hidden NAME=status VALUE='Aktiv'>

<?
   if (isset($web[firstname]) && isset($web[lastname]))
   {
      $rs_course_select=getrs("select d.id,
   					 a.name,
                     d.year,
					 e.name,
					 f.name,
					 g.name,
					 d.info,
					 d.price,
					 b.register,
					 b.status,
					 b.client_price,
					 sum(b.amount),
					 b.sconto_id,
					 sum(b.rab_earlybook+b.rab_lastminute+b.rab_stammkunde+b.rab_geschwister+b.rab_kombi1+b.rab_kombi2+b.rab_verlaengerung+b.rab_halbtag+b.rab_firmen+b.rab_sonder), 
					 b.id,
					 h.firstname,
					 h.lastname,
					 h.id
				   from products a,
						payments b,
						courses d,
						timeperiods e,
						institutions f,
						institutions g,
						clients h
				   where h.firstname Like '$web[firstname]' and h.lastname Like '$web[lastname]' and
						  h.id=b.clients_id and
						  b.courses_id=d.id and
						  d.products_id=a.id and
						  d.timeperiods_id=e.id and
						  d.institutions_id=f.id and
						  d.locations_id=g.id and
						  d.status in ('Aktiv', 'Inaktiv') and
				          a.status in ('Aktiv', 'Inaktiv') and
						  f.status in ('Aktiv', 'Inaktiv') and
						  b.status not in ('Entfernt') group by b.courses_id",$print_debug,$print_error);
	}
?>
	<TR>
		<TD colspan=3 height=50 class=form_footer>
		<TABLE WIDTH=100% HEIGHT=100%>
		<TR>
<?
$link=base64_encode("print_debug=0&id=".$id);  // $web[rechnung_id] zukünftig !
if ((!isset($save_x)) && (($web[status]=="Neu") || ($web[status]==""))) {?>
<!--			<TD ALIGN=CENTER><INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD> 
			<TD ALIGN=CENTER><INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()"></TD> -->
<? }?>
			<TD ALIGN=CENTER>
<? if (($web[rechnung_id]!="") && ($web[rechnung_id]>0))
{?>
<a target=_blank href='http://www.teamactivities.at/online/booking_confirmation.php?data=<?print($link)?>'>Buchungsbest&auml;tigung 
<?}?>
				<INPUT TYPE="HIDDEN" NAME="confirm" value=0>
				<INPUT TYPE="HIDDEN" NAME="web[status]" value=<?echo $web[status]?>>
				<INPUT TYPE="HIDDEN" NAME="print_debug" value=<?echo $print_debug?>>
				<INPUT TYPE="HIDDEN" NAME="print_error" value=<?echo $print_error?>>
				<input type="hidden" name="update" value="<?print($update)?>">
				<INPUT TYPE="HIDDEN" NAME="id" value=<?echo $id?>>
			</TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>besuchte Kurse mit &auml;hnlichen Vornamen+Nachnamen</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%><nobr>
<?
   if (($rs_course_select -> num_rows >0) && (isset($id)))
   {
   ?>
		<ul>
   <?
       while(LIST($course_id,$course_name,$course_year,$time_period,$institution,$city,$info,$price,$register,$status,$client_price,$paid,$sconto_id,$sconto,$payment_id,$firstn,$lastn,$client_id)=$rs_course_select -> fetch_row())
       {
			$rs_anwesenheit_select=getrs("select count(a.value) from coursetimes_clients a, coursetimes b where a.clients_id='".$client_id."' and a.coursetimes_id=b.id and b.courses_id='".$course_id."' and a.value='on'",$print_debug,$print_error);
			List($anwesenheit)=$rs_anwesenheit_select -> fetch_row();
			if ($client_price==0)
			{
				$wert=$paid-($price-$sconto);
			} else
			{
				$wert=$paid-$client_price;
			}
				//			if ($sconto_id==1) $wert = $paid-($price-$sconto);
//			if ($sconto_id==2) $wert = $paid-($price-($price*($sconto/100)));
			if ($status=="F") { $status_text="<font color='red'>Forderung offen: ". $wert." &euro; </font>";}
			if ($wert<0) { $color="red"; } else { $color="black"; }
			if ($status=="E") { $status_text="<font color='".$color."'>Bezahlt-Differenz: ".$wert." &euro;  </font>"; }
			if ($register==0) { $reg_text="nicht angemeldet";}
			if ($register==1) { $reg_text="angemeldet";}
			echo "<li><a href='kursblatt_form.php?id=".$course_id."'>".$course_name."</a> <br>( Originalpreis: &euro; ".$price." - ".$course_year." - ".$time_period." - ".$city." - <br>".$info.") -> ".$reg_text." - ".$status_text." - Anwesenheit: ".$anwesenheit." mal</li>";

       }
   ?>
   		</ul>
   <?
   }	
   else 
   { 
   		echo "<table><tr><td class=error>Keine Kurse zugewiesen !</td></tr></table>";
   }
   ?>
		</nobr>
		</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	</TABLE>
	</FORM>

	</CENTER>
</BODY>
</HTML>
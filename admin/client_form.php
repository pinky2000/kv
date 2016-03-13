<?
/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/* Dateiname: client_form.php
*  Zweck: Formular zur Eingabe der Kundendaten
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
$used_db = $_SESSION['used_db'];
isAllow(isAdmin() || isSecretary());

$back_url="../admin/client_list.php";

/* _POST einlesen */
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$confirm = $_POST['confirm'];
$move_client = $_POST['move_client'];
$move_client_continue = $_POST['move_client_continue'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$firstname_parent = $_POST['firstname_parent'];
$lastname_parent = $_POST['lastname_parent'];

if (!isset($no_error)) $no_error=1;

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
	</script>
</HEAD>

<?
// Delete-Button wird gedr�ckt
if (isset($delete_x))
{
        if ($confirm=="true" && $id>0)
        {
           $sql_delete = "update clients set status='Entfernt' where id=".$id;
           $rs_delete = getrs($sql_delete,$print_debug,$print_error);
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

// Save-Button wird gedr�ckt

elseif (isset($save_x))
{
  /* Formulardaten per _POST einlesen */
	$birthdate_day = $_POST['birthdate_day'];
	$birthdate_month = $_POST['birthdate_month'];
	$birthdate_year = $_POST['birthdate_year'];
	$address = $_POST['address'];
	$zip = $_POST['zip'];
	$city = $_POST['city'];
	$country = $_POST['country'];
	$phone1 = $_POST['phone1'];
	$phone2 = $_POST['phone2'];
	$fax = $_POST['fax'];
	$email = $_POST['email'];
	$sex = $_POST['sex'];
	$school_id = $_POST['school_id'];
	$remarks = $_POST['remarks'];
	$status = $_POST['status'];
	$sv_number = $_POST['sv_number'];
	$campdaten = $_POST['campdaten'];
	$schwimmfaehigkeit = $_POST['schwimmfaehigkeit'];
	$language_id = $_POST['language_id'];
	$sprachlevel_deutsch = $_POST['sprachlevel_deutsch'];
	$sprachlevel_englisch = $_POST['sprachlevel_englisch'];
	$campdaten_geschwister = $_POST['campdaten_geschwister'];
	$campdaten_allergien = $_POST['campdaten_allergien'];
	
	$no_error= CheckEmpty($firstname,$error_firstname) &&
  			 CheckEmpty($birthdate_day,$error_birthdate_day) &&
	  		 CheckEmpty($birthdate_month,$error_birthdate_month) &&
  			 CheckEmpty($birthdate_year,$error_birthdate_year) &&
  			 CheckEmpty($lastname,$error_lastname) &&
  			 CheckEmpty($status,$error_status) &&
  			 CheckEmpty($phone1,$error_phone1);

  if (isset($id) && $id!="" && $no_error)
  {
      $birthdate=$birthdate_year."-".$birthdate_month."-".$birthdate_day;
      $sql_client_update = "update clients set
                 firstname='$firstname',
                 lastname ='".strtoupper($lastname)."',
                 firstname_parent='$firstname_parent',
                 lastname_parent ='$lastname_parent',
                 address  ='$address',
                 zip      ='$zip',
                 city     ='$city',
                 country    ='$country',
                 phone1   ='$phone1',
                 phone2   ='$phone2',
                 fax      ='$fax',
                 email    ='$email',
                 sex      ='$sex',
                 birthdate='$birthdate',
                 school_id='$school_id',
                 remarks='$remarks',
                 status   ='$status',
				 sv_number = '$sv_number',
				 campdaten_tetanus='$campdaten[tetanus]',
				 campdaten_zecken='$campdaten[zecken]',
				 campdaten_fragebogen='$campdaten[fragebogen]',
				 campdaten_stammkunde='$campdaten[stammkunde]',
				 campdaten_schwimmen='$schwimmfaehigkeit',
				 language_id='$language_id',
				 sprachlevel_deutsch='$sprachlevel_deutsch',
				 sprachlevel_englisch='$sprachlevel_englisch',
				 campdaten_geschwister='$campdaten_geschwister',
				 campdaten_allergien='$campdaten_allergien'
				 where id=".$id;
      
      $rs_client_update=getrs($sql_client_update,$print_debug,$print_error);
  }
  elseif($no_error)
  {
     $birthdate=$birthdate_year."-".$birthdate_month."-".$birthdate_day;
     $sql_client_insert="insert into clients
          (firstname,lastname,firstname_parent,lastname_parent,address,zip,city,country,phone1,phone2,fax,email,sex,birthdate,school_id,remarks,status,create_date,sv_number,campdaten_tetanus,campdaten_zecken,campdaten_fragebogen,language_id,campdaten_schwimmen,sprachlevel_deutsch,sprachlevel_englisch,campdaten_geschwister,campdaten_allergien)
          values
          ('$firstname','".strtoupper($lastname)."','$firstname_parent','$lastname_parent','$address','$zip','$city','$country','$phone1','$phone2','$fax','$email','$sex','$birthdate','$school_id','$remarks','$status','$change_date','$sv_number','$campdaten[tetanus]','$campdaten[zecken]','$campdaten[fragebogen]','$language_id','$schwimmfaehigkeit','$sprachlevel_deutsch','$sprachlevel_englisch','$campdaten_geschwister','$campdaten_allergien')";
     $rs_client_insert = getrs($sql_client_insert,$print_debug,$print_error);
     $id=mysqli_insert_id($DB_TA_CONNECT);
  }
}

// Back-Button wird gedr�ckt
elseif (isset($back_x))
{
  header("Location: $back_url");
}

if ($move_client_continue)
{
     $new_id=$_POST['new_id'];
     echo "<div align=center><font color=red>";
	 echo "Die zwei Datensaetze wurden verschmolzen! <br>";
	 $sql_new="select lastname,firstname from clients where id=$new_id";
	 $rs_new=getrs($sql_new,$print_debug,$print_error);
	 list($lastname_new,$firstname_new)=$rs_new -> fetch_row();
	 echo "Der Datensatz (ID: ".$id.") ".$firstname." ".$lastname." wurde mit (ID: ".$new_id.") ".$firstname_new." ".$lastname_new." ersetzt!<br>";
	 echo "</font></div>";
	 // Den alten Datensatz "entfernen"  
     $sql="update clients set status='Entfernt' where id=$id";
	 $rs=getrs($sql,$print_debug,$print_error);
	// �berall, wo der Kundenamen vorkommt, neue ID einsetzen
     $sql="update coursetimes_clients set clients_id='$new_id' where clients_id=$id";
	 $rs=getrs($sql,$print_debug,$print_error);
     $sql="update payments set clients_id='$new_id' where clients_id=$id";
	 $rs=getrs($sql,$print_debug,$print_error);
	// Neuen Datensatz laden
	$id=$new_id;
}

// Daten aus DB Laden
if (isset($id) && $id!="" && $no_error)
{
           $sql_client_select="select id,firstname,lastname,firstname_parent,lastname_parent,address,zip,city,country,phone1,phone2,fax,email,sex,birthdate,school_id,remarks,status,sv_number,campdaten_tetanus,campdaten_zecken,campdaten_fragebogen,campdaten_stammkunde,campdaten_schwimmen,language_id,sprachlevel_deutsch,sprachlevel_englisch,campdaten_geschwister,campdaten_allergien from clients where id=".$id;
           $rs_client_select=getrs($sql_client_select,$print_debug,$print_error);
           LIST($id,$firstname,$lastname,$firstname_parent,$lastname_parent,$address,$zip,$city,$country,$phone1,$phone2,$fax,$email,$sex,$birthdate,$school_id,$remarks,$status,$sv_number,$campdaten[tetanus],$campdaten[zecken],$campdaten[fragebogen],$campdaten[stammkunde],$schwimmfaehigkeit,$language_id,$sprachlevel_deutsch,$sprachlevel_englisch,$campdaten_geschwister,$campdaten_allergien)=$rs_client_select -> fetch_row();

		   $firstname=ucfirst($firstname);
		   $date            = explode("-",$birthdate);
		   $birthdate_year  = $date[0];
		   $birthdate_month = $date[1];
		   $birthdate_day   = $date[2];
		   $lastname=strtoupper($lastname);
}
else
{
	$status="Aktiv";	
}
?>
<?if (isset($save_x) && $no_error) $command="show_message();";?>

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
	<tr><td height=12>
		<SPAN class="headline">Kunde</SPAN><br>
	</td></tr>
	</table>

	<BR>

	<!-- Formular Anfang -->
	<FORM  action="<? echo $PHP_SELF?>" method="POST" name=formular>
	<div align=center>
<? 
/* Dialog bez. Verschmelzung zweier Datens�tze */
if (isAdmin() || isSecretary())
{
?>
	<? 
	// Wenn jemand auf den Button "Kunden verschmelzen" geklickt hat, wird das Formular mit folgendem Dialog neu geladen //
	if ($move_client)
	{  ?>
			<b>ACHTUNG:</b> Mit Hilfe dieser Funktion wird der momentan aufgerufene Datensatz mit einem anderen Datensatz freier Wahl ueberschrieben!!	
			<br>	
			<select style='width:200px;'  id="combo_zone1" name="new_id">
			</select>
	 		<script>
				var z=new dhtmlXCombo("combo_zone1","new_id",200);
				z.enableFilteringMode(true,"codebase/loadCombo.php?db=<?print($used_db)?>");
			</script>
		   <input type="hidden" name="move_client_continue" value="1">
		   <input type="button" onclick="move_form()" name="move_client" value="Kunden verschmelzen">
	<?} 
	else
	{?>
		<input type="submit" name="move_client" value="Kunden verschmelzen">
	<?}?>
<?}?>
	</div>
	
	<table>
	<tr><td height=10>
		Felder die mit <font color=red>*</font> gekennzeichnet sind, muessen eingegeben werden ! 
	</td></tr>
	</table>

	<BR>

	<TABLE width=400 border=0 CELLPADDING=0 CELLSPACING=0>
	<tr>
		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<td colspan=3 HEIGHT=1  class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>ID <font color=red>*</font></TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<?echo $id?>
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
			<TR><TD WIDTH=100% HEIGHT=100%>Vorname <font color=red>*</font></TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_firstname);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=firstname VALUE="<?echo $firstname?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%><nobr>Nachname <font color=red>*</font></nobr></TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_lastname);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=lastname VALUE="<?echo $lastname?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%>Vorname Elternteil</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=firstname_parent VALUE="<?echo $firstname_parent?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%><nobr>Nachname Elternteil</nobr></TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=lastname_parent VALUE="<?echo $lastname_parent?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%>Adresse</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_address);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=address VALUE="<?echo $address?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%>Plz</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=zip VALUE="<?echo $zip?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%>Ort</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=city VALUE="<?echo $city?>">
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
			<INPUT TYPE=TEXT MAXLENGTH=250 SIZE=30 NAME=country VALUE="<?echo $country?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%>Telefon 1 <font color=red>*</font></TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_phone1);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=60 NAME=phone1 VALUE="<?echo $phone1?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%>Telefon 2</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=60 NAME=phone2 VALUE="<?echo $phone2?>">
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
			<TR><TD WIDTH=100% HEIGHT=100%>Fax</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_fax);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=fax VALUE="<?echo $fax?>">
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
			<?echo display_error($error_email);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=100 NAME=email VALUE="<?echo $email?>">
			(Trennzeichen = ;)</TD></TR>
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
			<SELECT NAME=sex>
				<OPTION <?if ($sex==1) echo "selected";?> VALUE=1>M&auml;nnlich</OPTION>
				<OPTION <?if ($sex==0) echo "selected";?> VALUE=0>Weiblich</OPTION>
			</SELECT>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Geb.Datum</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD>
			<?echo display_error($error_birthdate_day);?>
			<SELECT NAME=birthdate_day>
         <?for($i=1;$i<=31;$i++){
             if (strlen($i)==1)
             $i="0".$i;
             if ($birthdate_day==$i)
	             echo"<OPTION VALUE=$i selected>$i</OPTION>";
			 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
        }?>
			</SELECT> 
			</TD>
			<TD>
			<?echo display_error($error_birthdate_month);?>
			<SELECT NAME=birthdate_month>
         <?for($i=1;$i<=12;$i++){
             if (strlen($i)==1)
             $i="0".$i;
             if ($birthdate_month==$i)
	             echo"<OPTION VALUE=$i selected>$i</OPTION>";
			 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
        }?>
			</SELECT>
			</TD>
			<TD>
			<?echo display_error($error_birthdate_year);?>
			<SELECT NAME=birthdate_year>
         <? echo date('m.d.Y');
         for($i=date('Y');$i>=1945;$i--){
             if (strlen($i)==1)
             $i="0".$i;
             if ($birthdate_year==$i)
	             echo"<OPTION VALUE=$i selected>$i</OPTION>";
			 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
        }?>
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
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=sv_number VALUE="<?echo $sv_number?>">
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
			<SELECT NAME=school_id>
   <?
   $rs_client_school_select=getrs("select id, name from institutions where status in ('Aktiv', 'Inaktiv') order by name asc",$print_debug,$print_error);
   while(LIST($inst_id,$name)=$rs_client_school_select -> fetch_row())
   {
       if ($school_id==$inst_id)
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
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Anmerkung</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
				<TEXTAREA ROWS=5 COLS=40  NAME=remarks><?echo $remarks?></TEXTAREA>
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
			<input type='checkbox' <?if ($campdaten[tetanus]=="1") {print(" checked ");}?> name=campdaten[tetanus] value='1' ><nobr>Tetanus-Impfung<br>
			<input type='checkbox' <?if ($campdaten[zecken]=="1") {print(" checked ");}?> name=campdaten[zecken] value='1' ><nobr>Zecken-Impfung<br>
			<input type='checkbox' <?if ($campdaten[fragebogen]=="1") {print(" checked ");}?> name=campdaten[fragebogen] value='1' ><nobr>med. Fragebogen vorhanden<br>
			<input type='checkbox' <?if ($campdaten[stammkunde]=="1") {print(" checked ");}?> name=campdaten[stammkunde] value='1' ><nobr>Kunde ist Stammkunde<br>
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
			<SELECT NAME=schwimmfaehigkeit>
				<OPTION VALUE="0" <?if ($schwimmfaehigkeit=="0") print("SELECTED");?>>Nichtschwimmer</OPTION>
				<OPTION VALUE="1" <?if ($schwimmfaehigkeit=="1") print("SELECTED");?>>schlechter Schwimmer</OPTION>
				<OPTION VALUE="2" <?if ($schwimmfaehigkeit=="2") print("SELECTED");?>>guter Schwimmer</OPTION>
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
				<TEXTAREA ROWS=2 COLS=40  NAME=campdaten_allergien><?echo $campdaten_allergien?></TEXTAREA>
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
				<TEXTAREA ROWS=2 COLS=40  NAME=campdaten_geschwister><?echo $campdaten_geschwister?></TEXTAREA>
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
			<SELECT NAME=language_id>
                        <Option>keine ausgew&auml;hlt</option>
   <?
   $rs_language_select=getrs("select id, name from language order by name asc",$print_debug,$print_error);
   while(LIST($lang_id,$lang_name)=$rs_language_select -> fetch_row())
   {
       if ($language_id==$lang_id)
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
			<SELECT NAME=sprachlevel_deutsch>
				<OPTION VALUE="1" <?if ($sprachlevel_deutsch=="1") print("SELECTED");?>>A1</OPTION>
				<OPTION VALUE="2" <?if ($sprachlevel_deutsch=="2") print("SELECTED");?>>A2</OPTION>
				<OPTION VALUE="3" <?if ($sprachlevel_deutsch=="3") print("SELECTED");?>>B1</OPTION>
				<OPTION VALUE="4" <?if ($sprachlevel_deutsch=="4") print("SELECTED");?>>B2</OPTION>
				<OPTION VALUE="5" <?if ($sprachlevel_deutsch=="5") print("SELECTED");?>>C1</OPTION>
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
			<SELECT NAME=sprachlevel_englisch>
				<OPTION VALUE="1" <?if ($sprachlevel_englisch=="1") print("SELECTED");?>>A1</OPTION>
				<OPTION VALUE="2" <?if ($sprachlevel_englisch=="2") print("SELECTED");?>>A2</OPTION>
				<OPTION VALUE="3" <?if ($sprachlevel_englisch=="3") print("SELECTED");?>>B1</OPTION>
				<OPTION VALUE="4" <?if ($sprachlevel_englisch=="4") print("SELECTED");?>>B2</OPTION>
				<OPTION VALUE="5" <?if ($sprachlevel_englisch=="5") print("SELECTED");?>>C1</OPTION>
			</SELECT>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

<!-- 
Der Aktiv status wird nicht mehr verwendet, bleibt aber im Hintergrund erhalten (Standard ist aktiv=checked!), da gel�schte Datens�tze auch Entfernt gesetzt werden und nicht physikalisch gel�scht werden!!!
-->
	<INPUT TYPE=hidden NAME=status VALUE='Aktiv'>

<?
   if (isset($id) && $id>0)
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
                                         e.camp	
				   from products a,
						payments b,
						courses d,
						timeperiods e,
						institutions f,
						institutions g
				   where $id=b.clients_id and
						  b.courses_id=d.id and
						  d.products_id=a.id and
						  d.timeperiods_id=e.id and
						  d.institutions_id=f.id and
						  d.locations_id=g.id and
						  d.status in ('Aktiv', 'Inaktiv') and
				          a.status in ('Aktiv', 'Inaktiv') and
						  f.status in ('Aktiv', 'Inaktiv') and
						  b.status not in ('Entfernt') group by b.courses_id ",$print_debug,$print_error);
	}
?>
	<TR>
		<TD colspan=3 height=50 class=form_footer>
		<TABLE WIDTH=100% HEIGHT=100%>
		<TR>
			<TD ALIGN=CENTER><INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD> 
			<TD ALIGN=CENTER>
				<? if (($rs_course_select -> num_rows == 0) && (isset($id)))
				{ ?> 
					<INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
				<? } ?>
				<INPUT TYPE="HIDDEN" NAME="confirm" value=0>
				<INPUT TYPE="HIDDEN" NAME="print_debug" value=<?echo $print_debug?>>
				<INPUT TYPE="HIDDEN" NAME="print_error" value=<?echo $print_error?>>
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
			<TR><TD WIDTH=100% HEIGHT=100%>Kurse</TD></TR>
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
       while(LIST($course_id,$course_name,$course_year,$time_period,$institution,$city,$info,$price,$register,$status,$client_price,$paid,$sconto_id,$sconto,$payment_id,$iscamp)=$rs_course_select -> fetch_row())
       {
			$rs_anwesenheit_select=getrs("select count(a.value) from coursetimes_clients a, coursetimes b where a.clients_id='".$id."' and a.coursetimes_id=b.id and b.courses_id='".$course_id."' and a.value='on'",$print_debug,$print_error);
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
			if ($status=="F") { $status_text="<font color='red'>Forderung offen: ". $wert." &euro;</font>";}
			if ($wert<0) { $color="red"; } else { $color="black"; }
			if ($status=="E") { $status_text="<font color='".$color."'>Bezahlt-Differenz: ".$wert." &euro; </font>"; }
			if (($register=="")||($register==0)) { $reg_text="nicht angemeldet";}
			if ($register=="on") { $reg_text="angemeldet";}
			if ($institution=="nicht bekannt") {$institution="";}
                        if ($iscamp) { $link="campblatt_form.php"; } else { $link="kursblatt_form.php"; }
echo "<li><a target=_blank href='".$link."?id=".$course_id."'>".$course_name."</a> <br>(Originalpreis: &euro; ".$price." - ".$course_year." - ".$time_period." - ".$institution." - ".$city." - <br>".$info.") -> ".$reg_text." - ".$status_text." - Anwesenheit: ".$anwesenheit." mal</li>";
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

	<BR><br><br>
		Sie koennen nur Datensaetze loeschen, wenn diese nicht in Verwendung sind!
	</CENTER>
</BODY>
</HTML>
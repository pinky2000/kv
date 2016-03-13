<?
/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/* Dateiname: webregister_course_form.php
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

$back_url="../admin/webregister_course_list.php";

/* _POST einlesen */
$save_x = $_POST['save_x'];
$delete_x = $_POST['delete_x'];
$web = $_POST['web'];
$price = $_POST['price'];
$courses = $_POST['courses'];
$known_name = $_POST['known_name'];
$sim_new = $_POST['sim_new'];
$confirm = $_POST['confirm'];
$allready_register=0;

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

	function loadNew()
	{
	        window.document.formular.update.value=1;
	        window.document.formular.submit();
	}
	</script>
</HEAD>

<?
// Delete-Button wird gedrückt
if (isset($delete_x))
{
	print("entry-check-".$confirm.$web[id]);
		if ($confirm=="true" && $web[id]!="")
        {
		    print("storno");
			$sql_webregister_finish = "update web_courseclients set status='Storno', reg_date=sysdate(), reg_user='$change_user' where id=".$web[id];
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
                 address  ='".$web[address]."',
                 zip      ='".$web[zip]."',
                 city     ='".$web[city]."',
                 phone1   ='".$web[phone]."',
                 phone2   ='".$web[phone2]."',
                 email    ='".$web[email]."',
                 sex      ='".$web[sex]."',
                 birthdate='".$web[birthdate]."',
                 status   ='Aktiv',
				 change_user='$change_user',
				 web_reg = '2'
				 where id=".$web[sim_name_id];
      
      $rs_client_update=getrs($sql_client_update,$print_debug,$print_error);
  }
  elseif (($known_name=="0") || (isset($sim_new))) // Kunde ist Neu ODER Kunde ist bekannt, aber soll trotzdem neu angelegt werden...
  {
     $sql_client_insert="insert into clients
          (firstname,lastname,firstname_parent,lastname_parent,address,zip,city,phone1,phone2,email,sex,birthdate,school_id,status,change_user,web_reg)
          values ('$web[firstname]','".strtoupper($web[lastname])."','$web[firstname_parent]','$web[lastname_parent]','$web[address]','$web[zip]','$web[city]','$web[phone]','$web[phone2]','$web[email]','$web[sex]','$web[birthdate]','$web[school_id]','Aktiv','$change_user',1)";
     $rs_client_insert = getrs($sql_client_insert,$print_debug,$print_error);
     $web[sim_name_id]=mysqli_insert_id($DB_TA_CONNECT);
  }
  

  for ($i=0;$i<sizeof($courses);$i++)
  {
	  $sql_last_element="select rechnung_id from payments order by rechnung_id desc limit 1";
	  $rs_last_element=getrs($sql_last_element,$print_debug,$print_error);
	  list($last_id)=$rs_last_element->fetch_row();
	  $new_id=$last_id+1;

  $sql_payment_insert="
			insert into payments
               (rechnung_id,clients_id,courses_id,billdate,amount,sconto_id,sconto_amount,opt_amount,client_price,remarks,status,mahnung_sent1,mahnung_sent2,
			    mahnung_sent3,mahnung_comment,create_date,reg_date,reg_user,register)
            values
			   ('".$new_id."','$web[sim_name_id]','".$courses[$i]."','0000-00-00','0','0','0','0','".$price[$i]."','Rabattgrund: $web[rabattgrund]','F','0','0','0','$web[remarks]',sysdate(),sysdate(),'$change_user','on')";
	$rs_payment_insert=getrs($sql_payment_insert,$print_debug,$print_error);
  }

  $sql_webregister_finish = "update web_courseclients set status='Bearbeitet', reg_date=sysdate(), reg_user='$change_user' where id=".$id;
  $rs_webregister_finish=getrs($sql_webregister_finish,$print_debug,$print_error);

}

// Back-Button wird gedrückt
elseif (isset($back_x))
{
  header("Location: $back_url");
}

// Daten aus DB Laden
if (isset($id) && $id!="" && $no_error && !isset($save_x))
{
           $sql_client_select="select   c.id,
										c.firstname,
										c.lastname,
										c.firstname_parent,
										c.lastname_parent,
										c.adress,
										c.zip,
										c.city,
										c.phone1,
										c.phone2,
										c.email,
										c.sex,
										c.birthdate,
										c.school_name,
										c.coursedaten_remarks,
										c.status,
										c.courses_id,
										c.rabattgrund
								from web_courseclients c where c.id=".$id;
           $rs_client_select=getrs($sql_client_select,$print_debug,$print_error);
           LIST($web[id],$web[firstname],$web[lastname],$web[firstname_parent],$web[lastname_parent],$web[address],$web[zip],$web[city],$web[phone],$web[phone2],
				$web[email],$web[sex],$web[birthdate],$web[school_name],$web[remarks],$web[status],$web[courses],$web[rabattgrund])=$rs_client_select -> fetch_row();

		   $firstname=ucfirst($firstname);
		   $lastname=strtoupper($lastname);
}
else
{
	$status="Aktiv";	
}
?>
<?if (isset($save_x) && $no_error) $command="show_message();";?>

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
		<SPAN class="headline">Kurs-Online-Anmeldung - Checkformular</SPAN><br>
	</td></tr>
	</table>

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
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=web[firstname] VALUE="<?echo trim($web[firstname])?>">
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=web[lastname] VALUE="<?echo trim($web[lastname])?>"><br>
<?
if ((isset($web[firstname]) && isset($web[lastname])) && $web[status]!="Bearbeitet")
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
				   				   where a.firstname Like '%".trim($web[firstname])."%' and a.lastname Like '%".trim($web[lastname])."%' and
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

	}
?>
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
			<TR><TD WIDTH=100% HEIGHT=100%>gebuchte Kurse</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
   <?
   $courses_id=explode(",",$web[courses]);
//   var_dump($c_id);
   for ($i=0;$i<sizeof($courses_id);$i++)
   {
		$sql_check_register="select id from payments where clients_id='".$c_id."' and courses_id='".$courses_id[$i]."' and status in ('E','F')";
        $rs_check_register=getrs($sql_check_register,$print_debug,$print_error);
		$num_result_register=$rs_check_register->num_rows;		   
		if ($num_result_register>0)
		{
			$rs_camps_select=getrs("select c.id,a.name, b.name, c.year, d.name,c.price,c.info from products a, timeperiods b, courses c, institutions d where c.status='Aktiv' and b.camp=0 and a.id=c.products_id and b.id=c.timeperiods_id and d.id=c.locations_id and c.id='".$courses_id[$i]."' order by c.year desc,a.name asc",$print_debug,$print_error);
			LIST($camp_id,$prod,$time,$year,$inst,$orig_price,$info)=$rs_camps_select -> fetch_row();
        	echo "<font color=red>KUNDE BEREITS ANGEMELDET BEI:</font> ".$prod." / ".$year." / ".$time." / ".$inst." / ".$info." -> ".$orig_price;
		$allready_register=1;
                } else
		{
		?>
			<SELECT NAME="courses[<? print($i); ?>]">
	   <?
	    $rs_camps_select=getrs("select c.id,a.name, b.name, c.year, d.name,c.price,c.info from products a, timeperiods b, courses c, institutions d where c.status='Aktiv' and b.camp=0 and a.id=c.products_id and b.id=c.timeperiods_id and d.id=c.locations_id order by c.year desc,a.name asc",$print_debug,$print_error);
   		while(LIST($camp_id,$prod,$time,$year,$inst,$orig_price,$info)=$rs_camps_select -> fetch_row())
   	 	{
			if ($courses_id[$i]==$camp_id)
				echo "<OPTION selected VALUE=$camp_id>".$prod." / ".$year." / ".$time." / ".$inst." / ".$info." -> ".$orig_price."</OPTION>";
       	 	else
	        	echo "<OPTION VALUE=$camp_id>".$prod." / ".$year." / ".$time." / ".$inst." / ".$info." -> ".$orig_price."</OPTION>";
		} ?>
			</SELECT><br>
		<?}?>
			<input type=hidden name="price[<? print($i); ?>]" value="<?print($orig_price)?>">
<? } ?>
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
				<TEXTAREA ROWS=2 COLS=20  NAME=web[address]><?echo $web[address]?></TEXTAREA>
			</TD></TR>
			<TR><TD>	
			<?
			if ((strcmp($c_adress,$web[address])))
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
			<TR><TD WIDTH=100% HEIGHT=100%>Telefon</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<?echo display_error($error_phone1);?>
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=web[phone] VALUE="<?echo $web[phone]?>">
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
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=web[phone2] VALUE="<?echo $web[phone2]?>">
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
			<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=web[email] VALUE="<?echo $web[email]?>">
			</TD></TR>
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
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>Anmerkung</TD></TR>
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
			<TR><TD WIDTH=100% HEIGHT=100%>Rabattgrund:</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
				<TEXTAREA ROWS=2 COLS=40  NAME=web[rabattgrund]><?echo $web[rabattgrund]?></TEXTAREA>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>

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
					 d.price,
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
<? if ((!isset($save_x)) && (($web[status]!="Bearbeitet"))) {?>
<? if ($allready_register==0)
{			?>
<TD ALIGN=CENTER><INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD> 
<?}?>
			<TD ALIGN=CENTER><INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()"></TD> 
<? } ?>
			<TD ALIGN=CENTER>
				<INPUT TYPE="HIDDEN" NAME="confirm" value=0>
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
       while(LIST($course_id,$course_name,$course_year,$time_period,$institution,$city,$info,$price,$register,$status,$price,$paid,$sconto_id,$sconto,$payment_id,$firstn,$lastn,$client_id)=$rs_course_select -> fetch_row())
       {
			$rs_anwesenheit_select=getrs("select count(a.value) from coursetimes_clients a, coursetimes b where a.clients_id='".$client_id."' and a.coursetimes_id=b.id and b.courses_id='".$course_id."' and a.value='on'",$print_debug,$print_error);
			List($anwesenheit)=$rs_anwesenheit_select -> fetch_row();
			$wert=$paid-($price-$sconto);
//			if ($sconto_id==1) $wert = $paid-($price-$sconto);
//			if ($sconto_id==2) $wert = $paid-($price-($price*($sconto/100)));
			if ($status=="F") { $status_text="<font color='red'>Forderung offen: ". $wert." &euro;</font>";}
			if ($wert<0) { $color="red"; } else { $color="black"; }
			if ($status=="E") { $status_text="<font color='".$color."'>Bezahlt-Differenz: ".$wert." &euro;</font>"; }
			if ($register==0) { $reg_text="nicht angemeldet";}
			if ($register==1) { $reg_text="angemeldet";}
			echo "<li><a href='kursblatt_form.php?id=".$course_id."'>".$course_name."</a> <br>( &euro; ".$price." - ".$course_year." - ".$time_period." - ".$institution." - ".$city." - <br>".$info.") -> ".$reg_text." - ".$status_text." - Anwesenheit: ".$anwesenheit." mal</li>";

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
<?
/* ID einlesen, die von_GET und/oder _POST kommen kann */
if (empty($_GET['id']) || $_GET['id'] == "")
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; }
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/* Dateiname: employee_form.php
*  Zweck: Formular zur Eingabe der Mitarbeiterdaten
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
isAllow(isAdmin() || isSecretary());

/* _POST einlesen */
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$confirm = $_POST['confirm'];

if (!isset($no_error)) $no_error=1;
?>
<HTML>
<HEAD>
	<link rel="stylesheet" href="../css/ta.css">
	<script type="text/javascript">
		function delete_form()
		{
	  		document.formular.confirm.value = confirm("Wollen Sie diesen Eintrag wirklich löschen?");
		}
		function show_message(){
	    	document.getElementById('light').style.display='block';
	    	document.getElementById('fade').style.display='block';
	    }
	</script>
</HEAD>

<?
// Delete-Button wird gedrückt
if (isset($delete_x))
{
        if ($confirm=="true" && $id>0)
        {
           $sql_institution_del="update institutions set status='Entfernt' where id=".$id;
           $rs_institution_del=getrs($sql_institution_del,$print_debug,$print_error);

           if ($rs_institution_del -> errno==0)
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
	if ($print_debug == 1) var_dump($_POST);

	$name = $_POST['name'];
	$contactperson = $_POST['contactperson'];
	$address = $_POST['address'];
	$zip = $_POST['zip'];
	$city = $_POST['city'];
	$phone1 = $_POST['phone1'];
	$phone2 = $_POST['phone2'];
	$email = $_POST['email'];
	$fax = $_POST['fax'];
	$remarks = $_POST['remarks'];
	$internal = $_POST['internal'];
	$status = $_POST['status'];
	$inst_typ = $_POST['inst_typ'];
	$link = $_POST['link'];
	
    $no_error = true && CheckEmpty($name,$error_name) &&
	  		  	CheckEmpty($contactperson,$error_contactperson) &&
			  	CheckEmpty($address,$error_address) &&
			  	CheckEmpty($city,$error_city);

	if ($status=="")	$status="Inaktiv";

	if (isset($id) && $id!="" && $no_error)
	{
		$sql_institution_update = "update institutions set
				                name          = '$name',
                 				contactperson = '$contactperson',
                 				address       = '$address',
                 				zip           = '$zip',
                 				city          = '$city',
                 				phone1        = '$phone1',
                 				phone2        = '$phone2',
                 				email         = '$email',
                 				fax           = '$fax',
                 				remarks       = '$remarks',
                 				internal      = '$internal',
                 				status        = '$status',
                 				inst_typ      = '$inst_typ',
								link          = '$link'
        					where     id = ".$id;
		$rs_institution_update=getrs($sql_institution_update,$print_debug,$print_error);
	}
	elseif($no_error)
	{
		$sql_institution_insert="insert into institutions
        						(name,contactperson,address,zip,city,phone1,phone2,email,fax,remarks,internal,status,inst_typ,link)
             					values
            					('$name','$contactperson','$address','$zip','$city','$phone1','$phone2','$email','$fax','$remarks','$internal','$status','$inst_typ','$link')";
		$rs_institution_insert=getrs($sql_institution_insert,$print_debug,$print_error);
        $id=mysqli_insert_id($DB_TA_CONNECT);
  }
  $update=1;
}


// Back-Button wird gedrückt
elseif (isset($back_x))
{
  header("Location: $back_url");
}

// Daten aus DB Laden
if (isset($id) && $id!="" && !$update && $no_error)
{
	$sql_institution_select="select id,name,contactperson,address,zip,city,phone1,phone2,email,fax,remarks,internal,status,inst_typ,link from institutions where id=".$id;
    $rs_institution_select=getrs($sql_institution_select,$print_debug,$print_error);
    LIST($id,$name,$contactperson,$address,$zip,$city,$phone1,$phone2,$email,$fax,$remarks,$internal,$status,$inst_typ,$link)=$rs_institution_select -> fetch_row();
}

?>

<?if (isset($save_x) && $no_error) $command="show_message();";?>

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

	<center>
	<table border=0 cellspacing=0 cellpadding=0>
		<tr><td height=12></td></tr>
		<tr><td width=200 height=27 align=center>
			<SPAN class="headline">Institution</SPAN><br>
		</td></tr>
		<tr><td height=10></td></tr>
	</table>
	
	<BR><BR>
	
	<FORM  action="<? echo $PHP_SELF?>" enctype="multipart/form-data" method="post" name=formular>
	
	<table>
		<tr><td height=10>Felder die mit <font color=red>*</font> gekennzeichnet sind, müssen eingegeben werden ! </td></tr>
	</table>

	<br>

	<TABLE width=400 border=0 CELLPADDING=0 CELLSPACING=0>
		<tr>
		   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <td colspan=3 HEIGHT=1  class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>ID</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <?echo $id;?>
		   </TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Institut <font color=red>*</font> </TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <?echo display_error($error_name);?>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=name VALUE="<?echo $name?>">
		   </TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Kontaktperson<font color=red>*</font> </TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <?echo display_error($error_contactperson);?>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=contactperson VALUE="<?echo $contactperson?>"></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		
		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Adresse <font color=red>*</font> </TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <?echo display_error($error_address);?>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=address VALUE="<?echo $address?>"></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		
		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Plz</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=zip VALUE="<?echo $zip?>"></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		
		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Ort <font color=red>*</font> </TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <?echo display_error($error_city);?>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=city VALUE="<?echo $city?>"></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Telefon 1</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=phone1 VALUE="<?echo $phone1?>"></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		
		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header ><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Telefon 2</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=phone2 VALUE="<?echo $phone2?>"></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		
		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Fax</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=fax VALUE="<?echo $fax?>"></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		
		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>E-Mail</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=email VALUE="<?echo $email?>"></TD></TR></TABLE></TD>
		</TR>
		
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>
		
		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>HTTP-Link</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <INPUT TYPE=TEXT MAXLENGTH=250 SIZE=50 NAME=link VALUE="<?echo $link?>"></TD></TR></TABLE></TD>
		</TR>
		
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Anmerkung<br>(Kursblatt)</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <TEXTAREA ROWS=5 COLS=40  NAME=remarks><?echo $remarks?></TEXTAREA></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>interne Bemerkungen</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <TEXTAREA ROWS=5 COLS=40  NAME=internal><?echo $internal?></TEXTAREA></TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

		<TR>
		   <TD width=150 ALIGN=RIGHT  class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Art</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		   <?echo display_error($error_head);?>
		   <select NAME=inst_typ class="input_text">
				<option value="1" <?if ($inst_typ==1) print("selected"); ?>>Volksschule (VS)</option>
				<option value="2" <?if ($inst_typ==2) print("selected"); ?>>Kingergarten (KG)</option>
				<option value="3" <?if ($inst_typ==3) print("selected"); ?>>Sonstige</option>
		   </select>
		   </TD></TR></TABLE></TD>
		</TR>
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

		<TR>
		   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Aktiv</TD></TR></TABLE></TD>
		   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		   <?
		   if ($status=="Aktiv")
           		$text="checked";
			else
           		$text="";
   			?>
   		   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%><INPUT TYPE=checkbox NAME=status <? echo $text?> VALUE='Aktiv'></TD></TR></TABLE></TD>
		</TR>
		
		<tr>
		   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		</TR>

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
					 d.price
				   from products a,
						courses d,
						timeperiods e,
						institutions f,
						institutions g
				   where  d.products_id=a.id and
						  d.timeperiods_id=e.id and
						  d.institutions_id=f.id and
						  d.locations_id=g.id and
						  d.status in ('Aktiv', 'Inaktiv') and
				          a.status in ('Aktiv', 'Inaktiv') and
						  f.status in ('Aktiv', 'Inaktiv') and
						  d.status not in ('Entfernt') and
						  (d.locations_id=$id or d.institutions_id=$id) order by d.year asc",$print_debug,$print_error);
}
?>

		<TR>
		   <TD colspan=3 height=50 class=form_footer>
		   <TABLE WIDTH=100% HEIGHT=100%><TR><TD ALIGN=CENTER>
		   <INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD > <TD ALIGN=CENTER>
<? if ($rs_course_select -> num_rows == 0)
{ ?>		   	
		   <INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
<? } ?>
		   <INPUT TYPE="HIDDEN" NAME="confirm" value=0>
		   <INPUT TYPE="HIDDEN" NAME="id" value=<?echo $id?>>
		    </TD></TR></TABLE></TD>    </FORM>
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
   if ($rs_course_select -> num_rows >0)
   {
   ?>
		<ul>
   <?
       while(LIST($course_id,$course_name,$course_year,$time_period,$institution,$city,$info,$price)=$rs_course_select -> fetch_row())
       {
			if ($institution=="nicht bekannt") {$institution="";}			
			echo "<li><a href='kursblatt_form.php?id=".$course_id."'>".$course_name."</a> <br>( € ".$price." - ".$course_year." - ".$time_period." - ".$institution." - ".$city." - <br>".$info.")</li>";
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
	<BR><br><br>
	Sie können nur Datensätze löschen, wenn diese nicht in Verwendung sind!
	</CENTER>
</BODY>
</HTML>
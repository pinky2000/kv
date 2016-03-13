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
isAllow(isAdmin() || isSecretary() || isEmployee());

/* _POST einlesen */
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$confirm = $_POST['confirm'];

if (!isset($no_error)) $no_error=1;

if (isAdmin() || isSecretary())
  $back_url="../admin/employee_list.php";
elseif (isEmployee())
  $back_url="../gen.php";
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
if (isset($delete_x))
{
     if ($confirm=="true" && $id>0)
     {
         $sql_employee_delete="update employees set status='Entfernt' where id=".$id;
         $rs_employee_delete=getrs($sql_employee_delete,$print_debug,$print_error);
     }
     else
     {
         error_site($back_url);
     }
}

elseif (isset($save_x))
{
	if ($print_debug == 1) var_dump($_POST);

	$title = $_POST['title'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$address = $_POST['address'];
	$zip = $_POST['zip'];
	$city = $_POST['city'];
	$phone1 = $_POST['phone1'];
	$phone2 = $_POST['phone2'];
	$fax = $_POST['fax'];
	$email = $_POST['email'];
	$sex = $_POST['sex'];
	$sv_number = $_POST['sv_number'];
	$type = $_POST['type'];
	$bank_account = $_POST['bank_account'];
	$bank_code = $_POST['bank_code'];
	$birthdate_day = $_POST['birthdate_day'];
	$birthdate_month = $_POST['birthdate_month'];
	$birthdate_year = $_POST['birthdate_year'];
	$knowledge = $_POST['knowledge'];
	$state = $_POST['state'];
	$family = $_POST['family'];
	$contract_id = $_POST['contract_id'];
	$working_time = $_POST['working_time'];
	$validity = $_POST['validity'];
	$remarks = $_POST['remarks'];
	$costs_per_hour = $_POST['costs_per_hour'];
	$commission = $_POST['commission'];
	$username = $_POST['username'];
	$roles_id = $_POST['roles_id'];
	$status = $_POST['status'];
	$products = $_POST['products'];

	$no_error = true && CheckEmpty($firstname,$error_firstname) &&
				CheckEmpty($lastname,$error_lastname) &&
				CheckEmpty($username,$error_username) && true;

	if ($status=="") $status="Inaktiv";

	$birthdate=$birthdate_year."-".$birthdate_month."-".$birthdate_day;

	if (isset($id) && $id!="" && $no_error)
 	{
		$sql_employee_update = "update employees set
								title	  = '$title',
								firstname = '$firstname',
								lastname  = '$lastname',
								address   = '$address',
								zip       = '$zip',
								city      = '$city',
								phone1    = '$phone1',
								phone2    = '$phone2',
								fax       = '$fax',
								email     = '$email',
                                sex       = '$sex',
                                sv_number = '$sv_number',
								type      = '$type',
								bank_account='$bank_account',
								bank_code='$bank_code',birthdate='$birthdate',knowledge='$knowledge',contract_id='$contract_id',
								working_time='$working_time',remarks='$remarks',costs_per_hour='$costs_per_hour',commission='$commission',
								username='$username',roles_id='$roles_id',job='$job',desc_short='$desc_short',detail='$detail',sportarten='$sportarten',web='$web',status='$status',create_date=sysdate(),pos='$pos',
								validity='$validity',family='$family',state='$state',utype='$utype'
						 where id=".$id;
		$rs_employees_change = getrs($sql_employee_update,$print_debug,$print_error);
		
		if (isAdmin() || isSecretary())
		{
			$sql_products_del = "delete from employees_products where employees_id=".$id;
			$rs_products_del = getrs($sql_products_del,$print_debug,$print_error);
	    }
	}
    elseif($no_error)
	{
		$sql_employees_insert="insert into employees
				(title,firstname,lastname,ln_homepage,address,zip,city,phone1,phone2,fax,email,sex,sv_number,bank_account,bank_code,birthdate,knowledge,contract_id, working_time,remarks, costs_per_hour,commission,username,password,roles_id,job,desc_short,detail,sportarten,web,status,create_date,bild,pos,validity,family,state,utype,type)
			 values
				('$title','$firstname','$lastname','$ln_homepage','$address','$zip','$city','$phone1','$phone2','$fax','$email','$sex','$sv_number','$bank_account','$bank_code','$birthdate','$knowledge','$contract_id','$working_time','$remarks','$costs_per_hour','$commission','$username','".crypt($username,'activities')."','$roles_id','$job','$desc_short','$detail','$sportarten','$web','$status',sysdate(),'$image','$pos','$validity','$family','$state','$utype','$type')";
	 	$rs_employees_change=getrs($sql_employees_insert,$print_debug,$print_error);
        $id=mysqli_insert_id($DB_TA_CONNECT);
	 	$new_employee_id=$id;
		$sql_products_select = "select id,standard_hourcost from products order by id asc";
		$rs_products_select = getrs($sql_products_select,$print_debug,$print_error);
		while (($rs_pr>0) && (list($pid,$standard_hc)=$rs_products_select -> fetch_row()))
		{
			$sql_new_hc_insert="insert into hourcosts (value,products_id,employee_id) values ('$standard_hc','$pid','$new_employee_id')";
            $rs_new_hc_insert=getrs($sql_new_hc_insert);
		}
	}

	if ((isAdmin() || isSecretary()) && ($no_error))
	{
		for($i=0;$i<=(count($products)-1);$i++)
		{
           	$sql_products_insert="insert into employees_products (employees_id,products_id)	values ($id,$products[$i])";
            $rs=getrs($sql_products_insert,$print_debug,$print_error);
		}
	}
}

// Back-Button wird gedrückt

elseif (isset($back_x))

{

  header("Location: $back_url");

}

// Daten aus DB Laden
if (isset($id) && $id!="" && $no_error)
{
		$rs_employee_select = getrs("select id,title,firstname,lastname,ln_homepage,address,zip,city,phone1,phone2,fax,email,sex,sv_number,bank_account,bank_code,birthdate,knowledge,contract_id,working_time,remarks, costs_per_hour,commission,username,roles_id,job,desc_short,detail,sportarten,web,status,create_date,bild,pos,validity,family,state,utype,type from employees where id=$id",$print_debug,$print_error);
		LIST($id,$title,$firstname,$lastname,$ln_homepage,$address,$zip,$city,$phone1,$phone2,$fax,$email,$sex,$sv_number,$bank_account,$bank_code,$birthdate,$knowledge,$contract_id,$working_time,$remarks, $costs_per_hour,$commission,$username,$roles_id,$job,$desc_short,$detail,$sportarten,$web,$status,$create_date,$image,$pos,$validity,$family,$state,$utype,$type)=$rs_employee_select -> fetch_row();
		$rs_employee_products=getrs("select employees_id, products_id from employees_products where employees_id=$id",$print_debug,$print_error);

		$date=explode("-",$birthdate);
		$birthdate_year=$date[0];
		$birthdate_month=$date[1];
		$birthdate_day=$date[2];
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
<?
if ($print_debug) 
{
	echo "Diff: ".$_SESSION['session_diff_time']."<br>";
	echo "Login: ".$_SESSION['session_lastedit_time']."<br>";
}
?>		
	<table border=0 bordercolor=black cellspacing=0 cellpadding=0>
		<tr><td height=12></td></tr>
		<tr><td width=200 height=27 align=center>
			<SPAN class="headline">Mitarbeiter</SPAN><br>
		</td></tr>
	</table>
	<br>
	
<?if (isAdmin() || isSecretary()){?>
	<table>
		<tr><td height=10>Felder die mit <font color=red>*</font> gekennzeichnet sind, müssen eingegeben werden ! </td></tr>
	</table>
<?}?>

	<BR>
	<!-- Formular Anfang -->

	<FORM  action="<? echo $PHP_SELF?>" method="POST" enctype="multipart/form-data" name=formular>

	<TABLE width=700 border=0 CELLPADDING=0 CELLSPACING=0>
	<tr>
		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>ID</TD></TR>
		</TABLE>
		</TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD WIDTH=100% HEIGHT=100%>
			<? echo $id?>
			</TD></TR>
		</TABLE>
		</TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT  class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Titel</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
<? if (isEmployee()) { ?>
	   	<INPUT TYPE=TEXT MAXLENGTH=20 SIZE=20 NAME=title VALUE="<?echo $title?>" class="input_text"  disabled></TD></TR></TABLE></TD>
		<input type=hidden name=title value=<?echo $title?>>
<?}
   elseif (isAdmin() || isSecretary()){?>
	   	<INPUT TYPE=TEXT MAXLENGTH=20 SIZE=20 NAME=title VALUE="<?echo $title?>" class="input_text" ></TD></TR></TABLE></TD>
   <?}?>
	</TR>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT  class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%> Vorname <font color=red>*</font></TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<?echo display_error($error_firstname);?>
<? if (isEmployee()) {?>
 		<INPUT TYPE=TEXT MAXLENGTH=20 SIZE=20 NAME=firstname VALUE="<?echo $firstname?>" class="input_text"  disabled></TD></TR></TABLE></TD>
		<input type=hidden name=firstname value=<?echo $firstname?>>
<?}
elseif (isAdmin() || isSecretary()){?>
		<INPUT TYPE=TEXT MAXLENGTH=20 SIZE=20 NAME=firstname VALUE="<?echo $firstname?>" class="input_text" ></TD></TR></TABLE></TD>
<?}?>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%> Nachname <font color=red>*</font></TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<?echo display_error($error_lastname);?>
<? if (isEmployee()) {?>
		<INPUT TYPE=TEXT MAXLENGTH=50 SIZE=20 NAME=lastname VALUE="<?echo $lastname?>" disabled></TD></TR></TABLE></TD>
		<input type=hidden name=lastname value=<?echo $lastname?>>
<?}
elseif (isAdmin() || isSecretary()){?>
		<INPUT TYPE=TEXT MAXLENGTH=50 SIZE=20 NAME=lastname VALUE="<?echo $lastname?>"></TD></TR></TABLE></TD>
<?}?>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Adresse</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=address VALUE="<?echo $address?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Plz</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=20 SIZE=20 NAME=zip VALUE="<?echo $zip?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Ort</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
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
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>E-mail</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=email VALUE="<?echo $email?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Geschlecht</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<SELECT NAME=sex>
			<OPTION <?if ($sex==1) echo "selected";?> VALUE=1>Maennlich</OPTION>
            <OPTION <?if ($sex==0) echo "selected";?> VALUE=0>Weiblich</OPTION>
		</SELECT>
		</TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>SV-Nummer</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=sv_number VALUE="<?echo $sv_number?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Kontonummer / IBAN</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=bank_account VALUE="<?echo $bank_account?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>BLZ / BIC</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=30 NAME=bank_code VALUE="<?echo $bank_code?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Geb.Datum</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row>
		<TABLE>
			<TR><TD >
			<SELECT NAME=birthdate_day>
<?
for($i=1;$i<=31;$i++)
{
	if (strlen($i)==1) $i="0".$i;
	if ($birthdate_day==$i)
             echo"<OPTION VALUE=$i selected>$i</OPTION>";
			 else
			 echo"<OPTION VALUE=$i>$i</OPTION>";
}?>
			</SELECT> </TD>
			 <TD >
			 <SELECT NAME=birthdate_month>
<?
for($i=1;$i<=12;$i++)
{
	if (strlen($i)==1) $i="0".$i;
	if ($birthdate_month==$i)
			echo"<OPTION VALUE=$i selected>$i</OPTION>";
			else
			echo"<OPTION VALUE=$i>$i</OPTION>";
}?>
			</SELECT></TD>
			<TD>
			<SELECT NAME=birthdate_year>
<? 
for($i=1945;$i<=(date("Y")-15);$i++)
{
	if ($birthdate_year==$i)
    		echo"<OPTION VALUE=$i selected>$i</OPTION>";
	 		else
			echo"<OPTION VALUE=$i>$i</OPTION>";
}?>
			</SELECT></TD></TR></TR>
		</TABLE>
		</TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Staatsbürgerschaft</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=20 SIZE=20 NAME=state VALUE="<?echo $state?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Familienstand</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=20 SIZE=20 NAME=family VALUE="<?echo $family?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Kenntnisse</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<TEXTAREA ROWS=5 COLS=40  NAME=knowledge><?echo $knowledge?></TEXTAREA></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
<?if (isAdmin() || (isSecretary() && $roles_id!=1))
{?>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Vertragsart</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<SELECT NAME=contract_id>
<?

   $rs_contract_select = getrs("select id, name from contractsemployment order by id",$print_debug,$print_error);
   while(LIST($contractsemployment_id,$contractsemployment_name)=$rs_contract_select -> fetch_row())
   {
       if ($contract_id==$contractsemployment_id)
			echo "<OPTION selected VALUE=$contractsemployment_id>$contractsemployment_name</OPTION>";
       else
			echo "<OPTION VALUE=$contractsemployment_id>$contractsemployment_name</OPTION>";
   }
?>
		</SELECT>
   		</TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
<?}
else
{?>
	<input type=hidden name=contract_id value=<?echo $contract_id?>>
<?}?>
	<TR>
	   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Arbeitszeit</TD></TR></TABLE></TD>
	   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
	   <TEXTAREA ROWS=4 COLS=40 NAME=working_time><?echo $working_time?></TEXTAREA></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Einschränkungen Arbeitszeit</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<TEXTAREA ROWS=4 COLS=40 NAME=validity><?echo $validity?></TEXTAREA></TD></TR></TABLE></TD>
	</TR>

	<tr>
   		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

<?if (isAdmin() || isSecretary())
{?>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Produkte</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=500  class=form_row><TABLE border=0><TR>
   <?
	if ((!isset($id)) || ($id=="")) { $id=0; }

	$rs_products_select=getrs("	SELECT products.id, products.name, employees_products.employees_id
							   	FROM products
								LEFT JOIN employees_products ON ( products.status = 'Aktiv'
									AND employees_products.employees_id =$id
									AND products.id = employees_products.products_id)
								WHERE products.status = 'Aktiv' 
								ORDER BY products.name ASC ",$print_debug,$print_error);
	if ($id==0) unset($id);
    $count=0;
    while(LIST($products_id,$products_name,$employees_check)=$rs_products_select -> fetch_row())
	{
		$count++;
        if (isset($employees_check))
		{
       		echo "<td><input type='checkbox' name=products[] value='$products_id' checked><nobr>$products_name</td>";
       	} else
		{
      		echo "<td><input type='checkbox' name=products[] value='$products_id'        ><nobr>$products_name</td>";
		}
       	if (!($count%2))echo "</TR>\n<TR>";
     }
	if ($count%2) echo "<TD></TD></TR>\n";
?>
		</TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Provision</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=commission VALUE="<?echo $commission?>"></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>interne Anmerkung<br>für Kursleiter nicht sichtbar</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<TEXTAREA ROWS=5 COLS=40  NAME=remarks><?echo $remarks?></TEXTAREA></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Rolle</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<SELECT NAME=roles_id>
<?
   if (!isset($id) || $roles_id>1)   // Wenn Rolle des ausgewŠhlten Mitarbeiters ein Administrator ist, dann bleibt die Rolle
   {
	   if (isSecretary())  // Eine SekretŠrin kann keinen Mitarbeiter als Administrator auswŠhlen
	   {
			$rs_rolle=getrs("select id, name from roles where id<>1 order by id desc",$print_debug,$print_error);
	   } else
	   {
		   	$rs_rolle=getrs("select id, name from roles  order by id desc",$print_debug,$print_error);
	   }
	   while(LIST($role_id,$role_name)=$rs_rolle -> fetch_row())
	   {
			if ($roles_id==$role_id)
		         echo "<OPTION selected VALUE=$role_id>$role_name</OPTION>";
	      	else
		         echo "<OPTION VALUE=$role_id>$role_name</OPTION>";
	   }
   }
   else
   { ?>
	        <OPTION selected VALUE=1>Administrator</OPTION>
<? } ?>
	   </SELECT>
	   </TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

<?} 

if (isEmployee())
{?>
	<TR>
		<INPUT TYPE=HIDDEN NAME=remarks VALUE="<?echo $remarks?>">
		<INPUT TYPE=HIDDEN NAME=commission VALUE="<?echo $commission?>">
		<INPUT TYPE=HIDDEN NAME=roles_id VALUE="<?echo $roles_id?>">
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%> Benutzername <font color=red>*</font></TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<?echo display_error($error_username);?>
		<?echo $username?>
		<INPUT TYPE=HIDDEN MAXLENGTH=150 SIZE=50 NAME=username VALUE="<?echo $username?>"></TD></TR></TABLE></TD>
	</TR>
<? } else {?>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%> Benutzername <font color=red>*</font></TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<?echo display_error($error_username);?>
		<INPUT TYPE=TEXT MAXLENGTH=150 SIZE=50 NAME=username VALUE="<?echo $username?>"></TD></TR></TABLE></TD>
	</TR>
<? } ?>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
<?
if ((isset($id)) && ($id>0)) 
{?>
	<tr>
		<td colspan=3 align="center">
			<a target="_blank" href="change_pw.php?id=<?print($id)?>">
			Passwort ändern
<!-- 				<input type="button" name="change" value="Passwort ändern"> -->
			</a>
			</FORM>
		</TD>
	</TR>
<?}
else
{?>
	<tr>
		<td colspan=3>
			Initial wird das Passwort gleich dem Usernamen gesetzt ! Bitte asap das Passwort ändern!		
		</TD>
	</TR>
<?}?>
	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

<?if (isAdmin() || isSecretary())
{?>
	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Aktiv</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
		<SELECT NAME='status'>
			<OPTION <?if ($status=="Aktiv") echo"selected"?> VALUE=Aktiv>Aktiv</OPTION>
			<OPTION <?if ($status=="Inaktiv") echo"selected"?> VALUE=Inaktiv>Inaktiv</OPTION>
		</SELECT></TD></TR></TABLE></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

<? }
else
{?>
	<INPUT TYPE=HIDDEN NAME=status VALUE="<?echo $status?>">
<?
}

/* 	SQL Query, das die vom Mitarbeiter betreuten Kurse anzeigt (UNION deshalb, weil sowohl in 
	der Tab coursetimes_employees Mitarbeiter eingetragen sind, als auch in der Tab courses (employee4) direkt */
if (isset($id) && $id>0)
{
	$rs_course_employee_select=getrs("(select d.id,
   					 a.name,
                     d.year,
					 concat(left(e.name,10),'..'),
					 concat(left(f.name,10),'...'),
					 concat(left(g.name,10),'...'),
					 d.info,
					 d.price
   from products a,
		coursetimes_employees b,
		coursetimes c,
		courses d,
		timeperiods e,
		institutions f,
		institutions g
	where $id=b.employees_id and
		b.coursetimes_id=c.id and
		c.courses_id=d.id and
		d.products_id=a.id and
		d.timeperiods_id=e.id and
		d.institutions_id=f.id and
		d.locations_id=g.id and
		d.status in ('Aktiv', 'Inaktiv') and
        a.status in ('Aktiv', 'Inaktiv') and
		f.status in ('Aktiv', 'Inaktiv') and
		d.status in ('Aktiv', 'Inaktiv') 
	group by d.id
	order by d.year desc
	) UNION (	
	select d.id,
   					 a.name,
                     d.year,
					 concat(left(e.name,10),'..'),
					 concat(left(f.name,10),'...'),
					 concat(left(g.name,10),'...'),
					 d.info,
					 d.price
   from products a,
		coursetimes c,
		courses d,
		timeperiods e,
		institutions f,
		institutions g
	where $id=c.employee4_id and
		c.courses_id=d.id and
		d.products_id=a.id and
		d.timeperiods_id=e.id and
		d.institutions_id=f.id and
		d.locations_id=g.id and
		d.status in ('Aktiv', 'Inaktiv') and
        a.status in ('Aktiv', 'Inaktiv') and
		f.status in ('Aktiv', 'Inaktiv') and
		d.status in ('Aktiv', 'Inaktiv') 
	group by d.id
	order by d.year desc
)",$print_debug,$print_error);

}
?>
	<TR>
		<TD colspan=3 height=50 class=form_footer>
		<TABLE WIDTH=100% HEIGHT=100% border=0><TR><TD ALIGN=CENTER>
		<INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD > <TD ALIGN=CENTER>
<?if ((isAdmin() || isSecretary()) && $rs_course_employee_select -> num_rows ==0 )
{?>
		<INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
<?}?>

		<INPUT TYPE="HIDDEN" NAME="confirm" value=0>
		<INPUT TYPE="HIDDEN" NAME="id" value=<?echo $id?>>
		<INPUT TYPE="HIDDEN" NAME="print_debug" value=<?echo $print_debug?>>
		<INPUT TYPE="HIDDEN" NAME="print_error" value=<?echo $print_error?>>
		</TD></TR></TABLE></TD>    </FORM>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<TR>
		<TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Kurse</TD></TR></TABLE></TD>
		<td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
		<TD width=500 class=form_row>
<?
if ($rs_course_employee_select -> num_rows > 0)
{
?>
	<ul>
<?
	while(LIST($course_id,$course_name,$course_year,$time_period,$institution,$city,$info,$price)=$rs_course_employee_select -> fetch_row())
	{
		echo "<li><a href='kurs_form.php?id=".$course_id."'>".$course_name."</a> <br>( € ".$price." - ".$course_year." - ".$time_period." - ".$institution." - ".$city." - <br>".$info.") &nbsp;</li>";
	}?>
	</ul>
<?
}
   else { echo "<table><tr><td class=error>Keine Kurse zugewiesen !</td></tr></table>";
}
?>
	</TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>

	<tr>
		<td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	</TR>
</TABLE>

<BR><br><br>

<?if (isAdmin() || isSecretary())
{
?>
	Sie können nur Datensätz löschen, wenn diese nicht in Verwendung sind!
<?}?>

</CENTER>
<? echo session_id()."-".$_SESSION['session_diff_time'];?>
</BODY>

</HTML>
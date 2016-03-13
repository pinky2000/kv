<?
require_once("../../include/session.php");
require_once("littlefunctions.php");

$end=0;
// Save-Button wird gedrückt

$id=$_GET["id"];
$noblank=$_GET["noblank"];
$aufrufmehrmals=$_POST["aufrufmehrmals"];
$showform=$_POST["showform"];
if (!empty($_GET["sql_view"]))
{
	$sql_view=$_GET["sql_view"];
} else
{
	$sql_view=$_POST["sql_view"];
}

switch ($id)
{
        // Mitarbeiter mit wievielen Kursen
        case "1": 	$headline="Trainerabfrage";$show_form=true;
			include("employee_report.php");
        $end=1;
		break;

        // Mitarbeiter / Sportart
        case "2": $headline="Produktabfrage";$show_form=true;
        		include("product_report.php");
		$end=1;
		break;

        // Kundenabfrage
        case "3":  $headline="Institutionenabfrage";$show_form=true;
        		include("institution_report.php");
        $end=1;
		break;

        // Monatsabrechnung
        case "4": $headline="Bericht - <br>Monatsabrechung";$show_form=true;
                break;

        // Kunden ohne Zuweisung
        case "5": $headline="Bericht - <br>Kunden ohne Zuweisung";$show_form=false;
		if (!isset($year)) { $year=date('Y');}
		$inst=$_POST["inst"];
		if ($showform==1) ClientWithoutRelation($inst,$year); 
        break;

        // Zahlungen
        case "6":  $headline="Bericht - <br>Zahlungen";$show_form=true;
        break;

		// Tagessaldo
        case "7":  $headline="Bericht - <br>Tagessaldo";$show_form=false;
        break;
        
        // Geräteverwaltung
        case "9":  $headline="Bericht - <br>Ger&auml;teverwaltung";$show_form=false;
		$things=$_POST["things"];
		$employee=$_POST["employee"];
        break;

        // Kursübersicht
        case "13": 	$headline="Kursuebersicht";$show_form=true;
			$sql_view=0;
			$new=1;
			include("course_report.php");
        $end=1;
		break;

        // Statistik
        case "14": $headline="";$show_form=true;
		$sql_view=0;
		include("statistic_report.php");
        break;

}
if ($end==0) 
{
// Formular ausgeben
require_once("../../include/session.php");
require_once("../../include/html.php");
require_once("../../include/checkfunction.php");
require_once("littlefunctions.php");

?>

<HTML>
<HEAD>
<link rel="stylesheet" href="../../css/ta.css">
<script language=javascript>

function frage()
{
  document.form1.frage.value = confirm("Die Ausdrucke werden zur Verrechung fuer das jeweilige Monat gebucht!");
}

function loadForm(){
window.document.formular.submit();
}

function MakeMonthlyCalcForm(){
var employee=document.formular.employee_firstname.value;
//employee="27";
window_abrechnung=window.open('monthly_calc.php?employee='+employee+'&select_month='+document.formular.payment_month.value+'&select_year='+document.formular.payment_year.value+'&viewsoz='+document.formular.viewsoz.value,'muster',"location=no,resizable=yes,menubar=yes,scrollbars=yes,status=yes");
}

</script>
</HEAD>
<BODY>
<center>
<table border=0 cellspacing=0 cellpadding=0>
<tr><td height=12></td></tr>
<tr><td width=200 height=27 align=center valign=top background='../../images/underlineheader.gif'>
<SPAN class="headline"><? echo $headline?></SPAN><br>
</td></tr>
<tr><td height=10></td></tr>
</table>
<BR><BR>
<FORM  action="<? echo $PHP_SELF?>?id=<?echo $id?>" method="POST" name=formular>

<?
/////////////////////////////////////
//
//          MONATSABRECHNUNG
//
/////////////////////////////////////
if ($show_form && $id==4)
{	
$payment_month=$_POST["payment_month"];
$payment_year=$_POST["payment_year"];
$viewsoz=$_POST["viewsoz"];
$bill=$_POST["bill"];
$employee=$_POST["employee"];

	$datum   =date('d-m-Y');
	$datepiece=explode("-",$datum);

	$entered_date[0]=$payment_month;
    $entered_date[1]=$payment_year;
$DEBUG=0;
$ERROR=1;
	if (!isset($payment_year)) $payment_year=date('Y');
	if (!isset($payment_month)) $payment_month=date('m');

	$sql="select id from months where month='$payment_month' and year='$payment_year'";
	$rs_mon=getrs($sql,$DEBUG,$ERROR);
	if (($rs_mon>0) && (list($monat)=$rs_mon -> fetch_row()))
	{
		$mid=$monat;
		$sql_e="select id from employees order by id asc";
		$rs_e=getrs($sql_e,$DEBUG,$ERROR);
		while (($rs_e>0) && (list($e)=$rs_e -> fetch_row()))
		{
			$sql_="select id from bills where month_id=$monat and employee_id=$e";
			$rs_=getrs($sql_,$DEBUG,$ERROR);
			if (($rs_>0) && (list($emp)=$rs_->fetch_row()))
			{} else
			{
				$sql_in_emp="insert into bills (month_id, employee_id) values ('$monat','$e')";
				$rs_in=getrs($sql_in_emp,$DEBUG,$ERROR);
			}
		}
		//echo $monat;
	}
	else 
	{
		$sql_i="insert into months (month,year) values ('$payment_month','$payment_year')";
		$in=getrs($sql_i,$DEBUG,$ERROR);
        $mid=mysqli_insert_id($DB_TA_CONNECT);
		$sql_emp="select id from employees order by id";
		$rs_emp=getrs($sql_emp,$DEBUG,$ERROR);
		while(($rs_emp>0) && (list($empid)=$rs_emp->fetch_row()))
		{
			$sql_bills="insert into bills(month_id,employee_id) values ($mid,$empid)";
			$rs_bills=getrs($sql_bills,$DEBUG,$ERROR);
		}
	}
?>
		
		
<FORM action="report_form.php" method=post name=formular>
<TABLE width=600 border=0 CELLPADDING=0 CELLSPACING=0>	
<TR>       
	<TD  valign=top>
Monat<br> 
 <SELECT NAME=payment_month onchange=loadForm()>
         <?for($i=1;$i<=12;$i++){
             if (strlen($i)==1)
             $i="0".$i;
			 if (!$aufrufmehrmals)
			 {
				 if ($payment_month==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
			 elseif ($aufrufmehrmals==1)
			 {
				 if ($payment_month==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
        }?>
</SELECT>
	</TD>
	<TD valign=top>
	Jahr<br>
<SELECT NAME=payment_year onchange=loadForm()>
         <? echo date('Y');
         for($i=date('Y');$i>=1999;$i--){

             if (strlen($i)==1)
             $i="0".$i;
			 if (!$aufrufmehrmals)
			 {
				 if ($payment_year==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
			 elseif ($aufrufmehrmals==1)
			 {
				 if ($payment_year==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
        }
        ?>
</SELECT>
	</TD>
</FORM>
<FORM action="monthly_calc.php" method=post target=_blank name="form1">
<input type="hidden" name="frage" value="<?print($frage)?>">
<input type="hidden" name="payment_month" value="<?print($payment_month)?>">
<input type="hidden" name="payment_year" value="<?print($payment_year)?>">
   <TD valign=top>
   Ansicht intern &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><SELECT name="viewsoz" class="input_text">
   <option selected value="Ja" >Ja
   <option  value="Nein">Nein
   </select> 
   </TD>
   <TD valign=top>
   zur Verrechnung &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><SELECT name="bill" class="input_text">
   <option selected value="Ja" >Ja
   <option  value="Nein">Nein
   </select> 
   </TD>
	<TD  valign=top>Mitarbeiter <br>
	<select name=employee[] class="input_text" multiple size=20>
	<?
	$sql_payment="
		select 
			employees.id,employees.firstname, employees.lastname 
		from 
			employees, coursetimes, coursetimes_employees
		where
			coursetimes_employees.employees_id=employees.id 
			and 
			employees.status in ('Aktiv','Neu')
			and 
			coursetimes_employees.coursetimes_id=coursetimes.id 
			and 
			coursetimes.date like '$payment_year-$payment_month-%' 
		group by lastname,firstname 
		order by lastname asc,firstname asc";
	$rs=getrs($sql_payment,1,1);
	while(List($employee_id,$employee_firstname,$employee_lastname)=$rs -> fetch_row())
    { 
   	  if ($employee==$employee_id) { ?>
	  	  <option selected value=<?print($employee_id)?>><?print($employee_lastname." ".$employee_firstname." ".$employee_title)?>
<?    } else { ?>
	  	  <option  value=<?print($employee_id)?>><?print($employee_lastname." ".$employee_firstname." ".$employee_title)?>

<?    } 
	}?><input type='hidden' name='employee_firstname' value='<?$employee_firstname?>'>
   </select><br>
   
   </TD>
   
<TD width=120 valign=top ALIGN=CENTER>
		
<input type="hidden" name="mid" value="<?print($mid)?>">
<input type="submit" src="../../images/buttons/suchen.gif" BORDER=0 value="senden">
</TD>
<input type=hidden name=showform value=true>
<input type=hidden name=id value=5>
<input type=hidden name=aufrufmehrmals value=1>
</TR>
</TABLE>
	</FORM>		
<?
}
/////////////////////////////////////
//
// KUNDEN OHNE ZUWEISUNG
//
/////////////////////////////////////
elseif (!$show_form && $id==5)
{
?>        
	<TABLE width=600 border=0 CELLPADDING=0 CELLSPACING=0>	
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Jahr, indem die Kunden keinen Kurs besucht haben</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <INPUT TYPE=TEXT MAXLENGTH=20 SIZE=20 NAME=year VALUE="<?echo $year?>"></TD></TR></TABLE></TD>
   </TD></TR></TABLE></TD>
</TR>

<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Institution</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <SELECT NAME='inst[]' multiple>
<? if (!(isset($inst))) {$inst[0]="-1";} ?>
   <OPTION VALUE=-1 <? for ($i=0;$i<=sizeof($inst);$i++) { if ($inst[$i]=="-1") echo " selected";} ?>>Alle</OPTION>
   <?
   $rs=getrs("select id,name from institutions where status='Aktiv' order by name asc",0,1);
   while(LIST($i_id,$i_name)=$rs -> fetch_row())
   {?>
         <OPTION VALUE="<? echo($i_id) ?>" <? for ($i=0;$i<=sizeof($inst);$i++) { if ($inst[$i]==$i_id) echo " selected";} ?>><?echo($i_name)?></OPTION>
<?  }
           ?>

   </SELECT>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

	</TABLE>

	<TABLE width=600 border=0 cellpadding=0 cellspacing=0>
	<TR>
		<TD colspan=3 height=50 >
			<TABLE WIDTH=100% HEIGHT=100%>
				<TR>
					<TD ALIGN=CENTER>
						<input type=image src="../../images/buttons/suchen.gif" BORDER=0>
						<input type=hidden name=showform value=1>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<A HREF="javascript:window.print()"><IMG src="../../images/buttons/drucken.gif" BORDER=0></A>
					</TD>
				</TR>
			</TABLE>
		</TD> 
	</TR>
	</TABLE>
			<? echo $html_output;?>     
	
<?
}
/////////////////////////////////////
//
//          ZAHLUNGEN
//
/////////////////////////////////////
elseif ($show_form && $id==6)
{	
	$paymenttype=$_POST["paymenttype"];
	$course=$_POST["course"];
	$date_choose=$_POST["date_choose"];
	$date_legal=$_POST["date_legal"];
	$register_day=$_POST["register_day"];
	$register_month=$_POST["register_month"];	
	$register_year=$_POST["register_year"];	
	$register_day_to=$_POST["register_day_to"];
	$register_month_to=$_POST["register_month_to"];	
	$register_year_to=$_POST["register_year_to"];	
	$payment_day=$_POST["payment_day"];
	$payment_month=$_POST["payment_month"];	
	$payment_year=$_POST["payment_year"];	
	$payment_day_to=$_POST["payment_day_to"];
	$payment_month_to=$_POST["payment_month_to"];	
	$payment_year_to=$_POST["payment_year_to"];	
	$course_year_from = $_POST["course_year_from"];
	$course_year_to = $_POST["course_year_to"];
	$timeperiod_to = $_POST["timeperiod_to"];
	$timeperiod_from = $_POST["timeperiod_from"];
	
	$datum   =date('Y-m-d');
	$datepiece=explode("-",$datum);
	$datepiece_to=explode("-",$datum);
	$datepiece[1]=$datepiece[1]-1;
	$register_datepiece=explode("-",$datum);
	$register_datepiece_to=explode("-",$datum);
	$register_datepiece[1]=$register_datepiece[1]-1;
	
	$selection_date_from=$payment_year."-".$payment_month."-".$payment_day;
	$selection_date_to=$payment_year_to."-".$payment_month_to."-".$payment_day_to;
	$selection_date_to_first=($datepiece[0])."-".$datepiece[1]."-".$datepiece[2];
	
	$register_date_from=$register_year."-".$register_month."-".$register_day;
	$register_date_to=$register_year_to."-".$register_month_to."-".$register_day_to;
	$register_date_to_first=($register_datepiece[0])."-".$register_datepiece[1]."-".$register_datepiece[2];

	if (!isset($date_choose)) { $date_choose=1;}
		
	if     ($aufrufmehrmals==1){CoursesWithSpecialnumber($sql_view,$paymenttype,$course,$selection_date_from,$selection_date_to,$course_year_from,$course_year_to,$timeperiod_from,$timeperiod_to,$register_date_from,$register_date_to,$date_choose,$date_legal);}
	elseif (!$aufrufmehrmals){}
?>        
	<TABLE width=600 border=0 CELLPADDING=0 CELLSPACING=0>	
		<TR>       
           <TD width=100% class=form_row>
			   <TABLE BORDER=0 width=100%>
				   <TR>					   
					   <TD>Art<br>
							<select name=paymenttype>
<?if (($_SESSION['roleid']==1) || ($_SESSION['roleid']==2))
{?>
							<option value="Alle" >Alle
						    <option value="F"    <?if ($paymenttype=="F") print("selected"); ?>>Forderung
							<option value="E"    <?if ($paymenttype=="E") print("selected"); ?>>Einnahme
							<option value="A"    <?if ($paymenttype=="A") print("selected"); ?>>Ausgabe
<?} else {?>
						    <option value="F"    <?if ($paymenttype=="F") print("selected"); ?>>Forderung
<?}?>
							</select>

					    </TD>
					</TR>
					<TR>
						<TD>Kurs<br>
						<select name=course class="input_text">
						<option selected value=0>Alle</option>
						<?
					$sql = "select 
								a.id 				AS 'ID',
								b.name				AS 'Produkt',
								a.year				AS 'Jahr',
								c.name				AS 'Zeitperiode',
								d.name				AS 'Institution',	
								e.name				AS 'Ort',
								LEFT(a.info,20)		AS 'Information',
								CONCAT(a.products_id,a.year,a.timeperiods_id,a.institutions_id,a.locations_id)	AS 'Code'
							from 
								courses a,
								products b,
								timeperiods c,
								institutions d,
								institutions e
							where
								a.id > 0 
							and 
								a.products_id=b.id
							and
								a.timeperiods_id=c.id
							and
								a.institutions_id=d.id
							and
								a.locations_id=e.id
							and (a.status='Aktiv' or a.status='Inaktiv') 
							order by year desc, b.name asc";
					
						$rs=getrs($sql,0,1);
						while(List($course_id,$course_pid,$course_year,$course_tid,$course_iid,$course_lid,$course_info,$course_name)=$rs -> fetch_row())
					    { 
					   	  if ($course==$course_id) { ?>
						  	  <option selected value=<?print($course_id)?>><?print($course_pid."-".$course_year."-".$course_tid."-".$course_iid."-".$course_lid."-".$course_info)?>
					<?    } else { ?>
						  	  <option value=<?print($course_id)?>><?print($course_pid."-".$course_year."-".$course_tid."-".$course_iid."-".$course_lid."-".$course_info)?>
					<?    } 
						}?>
					   </select>
					   </TD>
				   </TR>
   <TR>
   <TD width=250>
   	 <b>Hinweis:</b><br>
	 Das <b>Datum f&uuml;r den Zahlungseingang</b> kann NUR f&uuml;r Einnahmen bzw. Ausgaben verwendet werden, jedoch NICHT f&uuml;r Forderungen!!<br>
   	 Das <b>Anmeldedatum</b> ist das Datum, an dem der Kursleiter oder das TA B&uuml;ro das jeweilige Kind in das Kursblatt eingetragen hat.<br>
	 Das <b>Kursjahr</b> ist das Jahr in dem der jeweilige Kurs stattgefunden hat.<br>
	 Das Kursjahr wird NUR verwendet, wenn die Abfrage auf "ALLE Kurse" gestellt ist!! <br>
	<br><br>
	<table>
	<tr>
		<td>
            <input type="radio" name="date_choose" value="1" <?if ($date_choose==1) echo "checked"?>>
			<b>Anmeldedatum</b> ab Datum (Tag-Monat-Jahr)<br>
       		<TABLE>
       		<TR>
       			<TD >
			       <SELECT NAME=register_day>
				   <OPTION VALUE=0 selected>Alle</OPTION>";
			         <?for($i=1;$i<=31;$i++){
			             if (strlen($i)==1)
			             $i="0".$i;
						 if (!$aufrufmehrmals)
						 {
							 if ($register_datepiece[2]==$i)
							 echo"<OPTION VALUE=$i selected>$i</OPTION>";
							 else
							 echo"<OPTION VALUE=$i>$i</OPTION>";
						 }
						 elseif ($aufrufmehrmals==1)
						 {
							 if ($register_day==$i)
							 echo"<OPTION VALUE=$i selected>$i</OPTION>";
							 else
							 echo"<OPTION VALUE=$i>$i</OPTION>";
						 }
			
			        }?>
					</SELECT> 
				</TD>
		 		<TD >
					 <SELECT NAME=register_month>
					 <OPTION VALUE=0 selected>Alle</OPTION>";
					         <?for($i=1;$i<=12;$i++){
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($register_datepiece[1]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
								 {
									 if ($register_month==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }?>
					</SELECT>
				</TD>
				<TD>
					<SELECT NAME=register_year>
					<OPTION VALUE=0 selected>Alle</OPTION>";
					         <? echo date('m.d.yy');
					         for($i=1999;$i<=date('Y');$i++){
					
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($register_datepiece[0]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
					  			 {
									 if ($register_year==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }
					        ?>
					</SELECT>
				</TD>
			</TR>
			</TABLE>
		</td>
		<td width=35></td>
		<td>
			Anmeldedatum bis Datum (Tag-Monat-Jahr)<br>
       		<TABLE>
       		<TR>
       			<TD >
			       <SELECT NAME=register_day_to>
				   <OPTION VALUE=0 selected>Alle</OPTION>";
			         <? for($i=1;$i<=31;$i++){
			             if (strlen($i)==1)
			             $i="0".$i;
						 if (!$aufrufmehrmals)
						 {
							 if ($register_datepiece_to[2]==$i)
							 echo"<OPTION VALUE=$i selected>$i</OPTION>";
							 else
							 echo"<OPTION VALUE=$i>$i</OPTION>";
						 }
						 elseif ($aufrufmehrmals==1)
						 {
							 if ($register_day_to==$i)
							 echo"<OPTION VALUE=$i selected>$i</OPTION>";
							 else
							 echo"<OPTION VALUE=$i>$i</OPTION>";
						 }
			
			        }?>
					</SELECT> 
				</TD>
 				<TD >
					 <SELECT NAME=register_month_to>
					 <OPTION VALUE=0 selected>Alle</OPTION>";
					         <? for($i=1;$i<=12;$i++){
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($register_datepiece_to[1]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
								 {
									 if ($register_month_to==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }?>
					</SELECT>
				</TD>
				<TD>
					<SELECT NAME=register_year_to>
					<OPTION VALUE=0 selected>Alle</OPTION>";
					         <? //echo date('m.d.yy');
					         for($i=1999;$i<=date('Y');$i++){
					
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($register_datepiece_to[0]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
					  			 {
									 if ($register_year_to==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }
					        ?>
					</SELECT>
				</TD>
			</TR>
			</TABLE> 		   
		</td>
		</tr>
	 </table>
	</td>
   </TR>
   <TR>
   <TD width=250 border=1>
	 <table>
   	 <tr>
   		<td>
            <input type="radio" name="date_choose" value="2" <?if ($date_choose==2) echo "checked"?>>
			<b>Zahlungseingang</b> ab Datum (Tag-Monat-Jahr)<br>
       		<TABLE>
       		<TR>
       			<TD >
			       <SELECT NAME=payment_day>
				   <OPTION VALUE=0 <? if ($payment_day==0) { echo"selected";}?>>Alle</OPTION>";
			         <?for($i=1;$i<=31;$i++){
			             if (strlen($i)==1)
			             $i="0".$i;
						 if (!$aufrufmehrmals)
						 {
							 if ($datepiece[2]==$i)
							 echo"<OPTION VALUE=$i selected>$i</OPTION>";
							 else
							 echo"<OPTION VALUE=$i>$i</OPTION>";
						 }
						 elseif ($aufrufmehrmals==1)
						 {
							 if ($payment_day==$i)
							 echo"<OPTION VALUE=$i selected>$i</OPTION>";
							 else
							 echo"<OPTION VALUE=$i>$i</OPTION>";
						 }
			
			        }?>
					</SELECT> 
				</TD>
				 <TD >
					 <SELECT NAME=payment_month>
					 <OPTION VALUE=0 selected>Alle</OPTION>";
					         <?for($i=1;$i<=12;$i++){
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($datepiece[1]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
								 {
									 if ($payment_month==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }?>
					</SELECT>
				</TD>
				<TD>
					<SELECT NAME=payment_year>
					<OPTION VALUE=0 selected>Alle</OPTION>";
					         <? echo date('m.d.yy');
					         for($i=1999;$i<=date('Y');$i++){
					
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($datepiece[0]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
					  			 {
									 if ($payment_year==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }
					        ?>
					</SELECT>
					<input type=hidden name=showform value=false>
					<input type=hidden name=id value=6>
					<input type=hidden name=aufrufmehrmals value=1>
					<input type=hidden name=sql_view value=<?echo $sql_view?>>
				</TD>
			</TR>
			</TABLE>
		</td>
		<td width=30></td>
		<td>
			Zahlungseingang bis Datum (Tag-Monat-Jahr)<br>
       		<TABLE>
       		<TR>
       			<TD >
			       <SELECT NAME=payment_day_to>
				   <OPTION VALUE=0 selected>Alle</OPTION>";
			         <? for($i=1;$i<=31;$i++){
			             if (strlen($i)==1)
			             $i="0".$i;
						 if (!$aufrufmehrmals)
						 {
							 if ($datepiece_to[2]==$i)
							 echo"<OPTION VALUE=$i selected>$i</OPTION>";
							 else
							 echo"<OPTION VALUE=$i>$i</OPTION>";
						 }
						 elseif ($aufrufmehrmals==1)
						 {
							 if ($payment_day_to==$i)
							 echo"<OPTION VALUE=$i selected>$i</OPTION>";
							 else
							 echo"<OPTION VALUE=$i>$i</OPTION>";
						 }
			
			        }?>
					</SELECT> 
				</TD>
				<TD >
					<SELECT NAME=payment_month_to>
					 <OPTION VALUE=0 selected>Alle</OPTION>";
					         <? for($i=1;$i<=12;$i++){
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($datepiece_to[1]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
								 {
									 if ($payment_month_to==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }?>
					</SELECT>
				</TD>
				<TD>
					<SELECT NAME=payment_year_to>
					<OPTION VALUE=0 selected>Alle</OPTION>";
					         <? //echo date('m.d.yy');
					         for($i=1999;$i<=date('Y');$i++){
					
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($datepiece_to[0]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
					  			 {
									 if ($payment_year_to==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }
					        ?>
					</SELECT>
				</TD>
			</TR>
			</TABLE> 		   
		</td>
		</tr>		
		</table>			  
    </TD>
	</TR>
   <TR>
   <TD width=250>
   	 <table>
   	 <tr>
   		<td>
           <input type="radio" name="date_choose" value="3" <?if ($date_choose==3) echo "checked"?>>
			Kursjahr von 
       		<TABLE>
       		<TR>
       			<TD >
					<SELECT NAME=course_year_from>
					<OPTION VALUE=0 selected>Alle</OPTION>";
					         <? //echo date('m.d.yy');
					         for($i=1999;$i<=date('Y');$i++){
					
					             if (strlen($i)==1)
					             $i="0".$i;
								 if (!$aufrufmehrmals)
								 {
									 if ($datepiece[0]==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
								 elseif ($aufrufmehrmals==1)
					  			 {
									 if ($course_year_from==$i)
									 echo"<OPTION VALUE=$i selected>$i</OPTION>";
									 else
									 echo"<OPTION VALUE=$i>$i</OPTION>";
								 }
					        }
					        ?>
					</SELECT>
					<select name="timeperiod_from" class="input_text">
							<?
							$SQL="select id,name from timeperiods where status='Aktiv'";
							$rs_time = getrs($SQL,0,1);
							While ($rs_time>0 && List($id,$name) = $rs_time->fetch_row())
							{ 
								if ($id==$timeperiod_from) 
								{ ?>
									<option class="input_text" value=<?print($id)?> selected><?print($name)?></option>
							<?	} else {?>
									<option class="input_text" value=<?print($id)?>><?print($name)?></option>
							<?	} ?>
						<?	} ?>
					</select>
				</TD>
			</TR>
			</TABLE>
		</td>
		</tr>
		</table>
	</td>
	</tr>
   <TR>
   <TD width=250>
   	 <table>
   	 <tr>
   		<td>
           <input type="checkbox" name="date_legal" value="ein" <?if ($date_legal=="ein") echo "checked"?>>
			Nur g&uuml;ltige Datumseingaben anzeigen?
		</td>
	</tr>
	</table>
	</td>
	</tr>
	</TABLE>
	</td></tr></table>
	
	<TABLE width=600 border=0 cellpadding=0 cellspacing=0>
	<TR>
		<TD colspan=3 height=50>


			<TABLE WIDTH=100% HEIGHT=100%>
				<TR>
					<TD ALIGN=CENTER>
						<A HREF="javascript:loadForm()"><IMG src="../../images/buttons/suchen.gif" BORDER=0></A>
						<A HREF="javascript:window.print()"><IMG src="../../images/buttons/drucken.gif" BORDER=0></A>
					</TD>
				</TR>
			</TABLE>
		</TD> 
	</TR>
	</TABLE>
	</FORM>		
<? echo $html_output;?>   

<?
}

/////////////////////////////////////
//
// TAGESSALDO
//
////////////////////////////////////

if (!$show_form && $id==7)
{
	$selection_day=$_POST["selection_day"];
	$selection_month=$_POST["selection_month"];
	$selection_year=$_POST["selection_year"];
	
	$selection_day_to=$_POST["selection_day_to"];
	$selection_month_to=$_POST["selection_month_to"];
	$selection_year_to=$_POST["selection_year_to"];

	$datum   =date('d-m-Y');
	$datepiece=explode("-",$datum);
	$selection_date=$selection_year."-".$selection_month."-".$selection_day;
	
	if     ($aufrufmehrmals==1){DaySaldo($selection_date);}
	elseif (!$aufrufmehrmals){DaySaldo(date('Y-m-d'));}
	
?>
	<TABLE width=600 border=0 CELLPADDING=0 CELLSPACING=0>	
		<TR>       
           <TD width=50% class=form_row>
			   <TABLE BORDER=1
			 width=100%>
				   <TR>
				       <? if ($html_output<>"") {$no_relation_text="Tagessaldo für den jeweiligen Tag";}
					   else{$no_relation_text="Für den heutigen Tag wurden keine Zahlungen gefunden !";}?>
					   <TD height=20 align=center><b><?echo $no_relation_text?></b><br><br>
					   <center>VON (Tag - Monat - Jahr)</center>
       <TABLE border=0>
       <TR><TD align=center>
       
       <SELECT NAME=selection_day onchange=loadForm()>
         <?for($i=1;$i<=31;$i++){
             if (strlen($i)==1)
             $i="0".$i;
			 if (!$aufrufmehrmals)
			 {
				 if ($datepiece[0]==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
			 elseif ($aufrufmehrmals==1)
			 {
				 if ($selection_day==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }

        }?>
</SELECT><br> </TD>
 <TD >
 
 <SELECT NAME=selection_month onchange=loadForm()>
         <?for($i=1;$i<=12;$i++){
             if (strlen($i)==1)
             $i="0".$i;
			 if (!$aufrufmehrmals)
			 {
				 if ($datepiece[1]==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
			 elseif ($aufrufmehrmals==1)
			 {
				 if ($selection_month==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
        }?>
</SELECT><br></TD>
<TD>

<SELECT NAME=selection_year onchange=loadForm();>
         <? 
         for($i=1999;$i<=date('Y');$i++){

             if (strlen($i)==1)
             $i="0".$i;
			 if (!$aufrufmehrmals)
			 {
				 if ($datepiece[2]==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
			 elseif ($aufrufmehrmals==1)
			 {
				 if ($selection_year==$i)
				 echo"<OPTION VALUE=$i selected>$i</OPTION>";
				 else
				 echo"<OPTION VALUE=$i>$i</OPTION>";
			 }
        }
        ?>
</SELECT><br>
<input type=hidden name=showform value=false>
<input type=hidden name=id value=7>
<input type=hidden name=aufrufmehrmals value=1>
</TD>
</tr>
</TABLE>
				   </TR>
			   </TABLE> 		   
           </TD>
		</TR>
	</TABLE>

	<TABLE width=600 border=0 cellpadding=0 cellspacing=0>
	<TR>
		<TD colspan=3 height=50 >
			<TABLE WIDTH=100% HEIGHT=100%>
				<TR>
					<TD ALIGN=CENTER>
						<A HREF="javascript:window.print()"><IMG src="../../images/buttons/drucken.gif" BORDER=0></A>
					</TD>
				</TR>
			</TABLE>
		</TD> 
	</TR>
	</TABLE>
			<? echo $html_output;?>     
<?
}
/////////////////////////////////////
//
//          Geräteverleih
//
/////////////////////////////////////
elseif($id==9)
{	
	if     ($things>0  && $employee==0){$modus=1;$modus_id=$things;
}
	elseif ($things==0 && $employee>0) {$modus=2;$modus_id=$employee;}
	
	if (($things>0 && $employee>0) || ($things==0 &&$employee==0)){$error_msg="Bitte wählen Sie EINEN Eintrag aus!";}
	
	if     ($aufrufmehrmals==1 && $modus && $modus_id){loanthings($modus,$modus_id);}
	elseif (!$aufrufmehrmals && $modus && $modus_id)  {loanthings($modus,$modus_id);}
	$things=0;
	$employee=0;
	$no_relation_text="Um eine Übersicht der Entlehner eines<br> Geräts zu erhalten, wählen Sie ein Gerät.<br>ODER<br>Um eine Übersicht der entlehnten Geräte eines<br> Mitarbeiters zu erhalten, wählen Sie einen Mitarbeiter.";
?>  <center>      
	<TABLE width=600 border=0 CELLPADDING=0 CELLSPACING=0>	
		<TR>       
           <TD width=100% class=form_row>
			   <TABLE BORDER=0 width=100%>
				   <TR>				   
					   <TD height=40 align=center><b><?echo $no_relation_text?></b><br><br><?echo $error_msg?></TD>
				   </TR>
</table><br>
<center>
	<TABLE width=400 border=0 CELLPADDING=0 CELLSPACING=0>
<tr>
   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1  class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Gerät</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <select name=things class="input_text">
   <option value=0> Entweder Gerät... </option>
	<?
	$rs=getrs("select id, name from objects where status in('Aktiv','Inaktiv') order by name ",0,1);
	while(List($things_id,$things_name)=$rs->fetch_row())
    { 
if ($things==$things_id) { ?>
	  	  <option selected value=<?print($things_id)?>><?print($things_name)?>
<?  }else { ?>
		  <option  value=<?print($things_id)?>><?print($things_name)?>
	
<?  }
}?>
   </select>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Mitarbeiter</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <select name=employee class="input_text">
   <option value=0> ...
   oder Mitarbeiter</option>
	<?
	$rs_mit=getrs("select id, title, firstname, lastname from employees where status in('Aktiv','Inaktiv') order by lastname ",0,1);
	while(List($employee_id,$employee_title,$employee_firstname,$employee_lastname)=$rs_mit->fetch_row())
    { 
if ($employee==$employee_id) { ?>
	  	  <option selected value=<?print($employee_id)?>><?print($employee_lastname." ".$employee_firstname." ".$employee_title)?>
<?  }else { ?>
		  <option  value=<?print($employee_id)?>><?print($employee_lastname." ".$employee_firstname." ".$employee_title)?>
<?    } }?>		  


   </select>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<center>
			<TABLE WIDTH=100% HEIGHT=100% border=0>
				<TR>
				<TD ALIGN=CENTER>
		
						<input type=image src="../../images/buttons/suchen.gif" BORDER=0>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<A HREF="javascript:window.print()"><IMG src="../../images/buttons/drucken.gif" BORDER=0></A>
					</TD>
				</TR>
			</TABLE>
	 <? echo $html_output;?>
	</FORM>		
<?
			 }
} // ende of $end?>

</CENTER>
</BODY>
</HTML>
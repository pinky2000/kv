<?php
require_once("../../include/session.php");
require_once("../../include/html.php");
require_once("../../include/checkfunction.php");

$nb=$_GET["nb"];
$emplo=$_POST["emplo"];
$prod=$_POST["prod"];
$status=$_POST["status"];
$year_from=$_POST["year_from"];
$year_to=$_POST["year_to"];
$zeit_to=$_POST["zeit_to"];
$zeit_from=$_POST["zeit_from"];
$darstellung=$_POST["darstellung"];
$felder=$_POST["felder"];
$sort1=$_POST["sort1"];
$sort2=$_POST["sort2"];
$sort3=$_POST["sort3"];
$order=$_POST["order"];

// Mitarbeiter function ///

//sql string:
// select a.ID,a.firstname,a.lastname,e.name,c.year,f.name,g.name,h.name, c.info from employees a, coursetimes_employees b, courses c, coursetimes d, products e, timeperiods f, institutions g, institutions h where a.status!='Entfernt' and a.id=b.employees_id and b.coursetimes_id=d.id and c.id=d.courses_id and c.products_id=e.id and c.timeperiods_id=f.id and c.institutions_id=g.id and c.locations_id=h.id and c.year>=2008 and c.year<2009 group by c.id order by a.ID, c.ID asc 

/* Neue Abfrage mit Stundensatz und Anzahl der gehaltenen Einheiten/Trainer
select a.ID,c.id,count(b.id) as Anzahl,aa.lastname,b.hourcost,a.lastname as Nachname,a.firstname as Vorname,e.name as Produkt,c.year as Jahr,f.name as Zeitperiode,g.name as Institution,h.name as Ort,c.info as Info from employees a, employees aa, coursetimes_employees b, courses c, coursetimes d, products e, timeperiods f, institutions g, institutions h where aa.id=c.standard_employee and a.status!='Entfernt' and a.id=b.employees_id and b.coursetimes_id=d.id and c.id=d.courses_id and c.products_id=e.id and c.timeperiods_id=f.id and c.institutions_id=g.id and c.locations_id=h.id and ( (c.year>2008 and c.year<2009) or c.year=2008 or c.year=2009) and a.status in ('Neu','Aktiv') group by a.id,c.id order by Nachname, Nachname, Nachname asc
 */
function employees ($status,$prod,$time_en,$year_from,$zeit_from,$year_to,$zeit_to,$felder,$sort1,$sort2,$sort3,$order,$emplo,$darstellung)
{
		global $html_output;
		global $query_text;

		for($c=0;$c<sizeof($status);$c++)
	    {
				if     ($status[$c]==0){$status[$c]="Inaktiv";}
				elseif ($status[$c]==1){$status[$c]="Aktiv";}
				elseif ($status[$c]==3){$status[$c]="Neu";}
				elseif ($status[$c]==2){$status[$c]="derz.n.verf�gbar";}
				if($c<(sizeof($status)-1))
					$sql_status.="'".$status[$c]."'".",";
				elseif($c==(sizeof($status)-1))
			    	$sql_status.="'".$status[$c]."'";
		}
		for($aa=0;$aa<sizeof($prod);$aa++)
	    {
				if($aa<(sizeof($prod)-1))
					$sql_prod.="'".$prod[$aa]."'".",";
				elseif($aa==(sizeof($prod)-1))
				    $sql_prod.="'".$prod[$aa]."'";
		}

		for($aa=0;$aa<sizeof($emplo);$aa++)
	    {
				if($aa<(sizeof($emplo)-1))
					$sql_emplo.="'".$emplo[$aa]."'".",";
				elseif($aa==(sizeof($emplo)-1))
				    $sql_emplo.="'".$emplo[$aa]."'";
		}

	if (($prod[0]=='-1') || ($prod[0]=='-2'))   
	{
			$prod_select        = "  ";
	}
	else
	{
			$prod_select        = "and c.products_id in (".$sql_prod.") ";
	}

	if ($emplo[0]=='-1')
	{
			$emplo_select        = "  ";
	}
	else
	{
			$emplo_select        = "and a.id in (".$sql_emplo.") ";
	}

	if ($status[0]=='4')   
	{
			$select_from        = "  ";
	}
	else
	{
			$select_from        = "and a.status in (".$sql_status.") ";
	}
/*
	if (isset($year_from)) 
	{ 
		if ($zeit_from>'0') { $zeit_from_where="and ((f.id=$zeit_from";}
		$year_from_where="and ((c.year=".$year_from.")";
	} else { $year_from_where="";}
	if (isset($year_to)) 
	{ 
		if ($zeit_to>'0') { $zeit_to_where=" or (f.id=$zeit_to";}
		$year_to_where="or (c.year=".$year_to."))";
	} else { $year_to_where="";}
*/	

	$year_period = " and (";
	if (isset($year_from) || (isset($year_to)))
	{
		$year_period.= " (c.year>$year_from and c.year<$year_to)";
	}
	if ($zeit_from>0)
	{
		$year_period.=" or (c.year=$year_from and f.id=$zeit_from) ";
		$period_select =" and f.id in ('$zeit_from'";
	}
	else
	{
		$year_period.=" or c.year=$year_from";
	}
	if ($zeit_to>0)
	{
		$year_period.=" or (c.year=$year_to and f.id=$zeit_to) ";
		$period_select.=",'$zeit_to')";
	}
	else
	{
		$year_period.=" or c.year=$year_to";
		if ($zeit_from>0) $period_select.=")";
	}
	$year_period.=")";

	$prod_ena=0;

	$time_sql=0;

	for($aa=0;$aa<sizeof($felder);$aa++)
    {
		if ($felder[$aa]=="keine")
		{
			$felder_select=" ";
			$aa=10000;
		} else
		{
			$felder[$aa]=strtr($felder[$aa],"|","'");
			$felder_select.= ",". $felder[$aa];
		}
	}

	if (($time_en=="on") && (!$time_sql)) { $felder_select.=", ".$time; $time_sql=1;}

	if ($darstellung=="own") 
	{
		$employees1_from="";
		$employees1_where=" and c.standard_employee=a.id ";		
		$employees1_select=" ";
		$employees1_group=" group by c.id ";
	} else
	{
		$employees1_from=" employees aa, ";
		$employees1_where=" and aa.id=c.standard_employee ";
		$employees1_select=", (aa.id=a.id)";
		$employees1_group=" group by a.id,c.id ";
	}
	$xxx=" (select a.ID,c.id,a.lastname as Nachname,a.firstname as Vorname,e.name as Produkt,c.year as Jahr,f.name as Zeitperiode,g.name as Institution,h.name as Ort,c.info as Info".$employees1_select.$felder_select." ";

	$xxx.=" from employees a, $employees1_from coursetimes_employees b, courses c, coursetimes d, products e, timeperiods f, institutions g, institutions h";

	$xxx.=" where a.status!='Entfernt' and c.status!='Entfernt' $employees1_where and a.id=b.employees_id and b.coursetimes_id=d.id and c.id=d.courses_id and c.products_id=e.id and c.timeperiods_id=f.id and c.institutions_id=g.id and c.locations_id=h.id $year_from_where $year_to_where $year_period $select_from  $prod_select $emplo_select $period_select";

	$xxx.=" $employees1_group order by $sort1, $sort2, $sort3 $order";

	$xxx.=" ) UNION (";
	$xxx.=" select a.ID,c.id,a.lastname as Nachname,a.firstname as Vorname,e.name as Produkt,c.year as Jahr,f.name as Zeitperiode,g.name as Institution,h.name as Ort,c.info as Info".$employees1_select.$felder_select." ";

	$xxx.=" from employees a, $employees1_from courses c, coursetimes d, products e, timeperiods f, institutions g, institutions h";

	$xxx.=" where a.status!='Entfernt' and c.status!='Entfernt' $employees1_where and a.id=d.employee4_id and c.id=d.courses_id and c.products_id=e.id and c.timeperiods_id=f.id and c.institutions_id=g.id and c.locations_id=h.id $year_from_where $year_to_where $year_period $select_from  $prod_select $emplo_select $period_select";

	$xxx.=" $employees1_group order by $sort1, $sort2, $sort3 $order) order by $sort1, $sort2, $sort3 $order";

	$query_text = $xxx;



	$rs=getrs($xxx,$sql_view);

print($xxx);



	$num= $rs -> num_rows;

	$html_output="<div align=center>Anzahl der Datens&auml;tze: $num</div>";

	$html_output.="

	<TABLE width=100% CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1>

		<tr>

			<td align=center>
				<b>Nr</b>
			</td>
			<td align=center>
				<b>ID</b>
			</td>
			<td align=center>
				<b>Vorname</b>
			</td>
			<td align=center>
				<b>Nachname</b>
			</td>
			<td align=center>
				<b>Produkt</b>
			</td>
			<td align=center>
				<b>Jahr</b>
			</td>
			<td align=center>
				<b>Zeitperiode</b>
			</td>
			<td align=center>
				<b>Institution</b>
			</td>
			<td align=center>
				<b>Ort</b>
			</td>
			<td align=center>
				<b>Info</b>
			</td>";
	if ($darstellung=="all") {
		$html_output.="		
			<td align=center>
				<b>Vertretung</b>
			</td>";
	}

	for($aa=0;$aa<sizeof($felder);$aa++)

    {

    $fieldname = explode(" ",$felder[$aa]);

			$html_output.="

			<td align=center>

				<b>$fieldname[2]</b>

			</td>";



	}

	$html_output.="</tr>";

	$nr=0;

         while($ergebnis=$rs -> fetch_row())

         {
			$nr++;

			$html_output.="<tr>";

			$html_output.="

			<td align=left>$nr</td>";


				if (($old_lastname==$ergebnis[2]) && ($old_firstname==$ergebnis[3]))
				{ $html_output.="
				<td>&nbsp;</td>
				<td align=center>&nbsp;</td><td align=center>&nbsp;</td>"; }
				else
				{ $html_output.="
				<td align=center><a href='../employee_form.php?id=$ergebnis[0]' target=_blank>$ergebnis[0]</a></td>
				<td align=center>$ergebnis[2]</td><td align=center>$ergebnis[3]</td>"; } 
				
				$html_output.="<td align=center><a href='../kurs_form.php?id=$ergebnis[1]' target=_blank>$ergebnis[4]</td>";
			
			for($aa=5;$aa<sizeof($ergebnis);$aa++)

		    {
				if ($darstellung=="all") {
					if ($aa==10) 
					{
						if ($ergebnis[$aa]==0) $ergebnis[$aa]="ja";
						if ($ergebnis[$aa]==1) $ergebnis[$aa]="nein";
					}
				}
				if ($ergebnis[$aa]) { $html_output.="<td align=center>$ergebnis[$aa]</td>"; } 
				else 
				{ $html_output.="<td align=center>&nbsp;</td>"; }
			}

			$html_output.="</tr>";

			$old_lastname=$ergebnis[2];
			$old_firstname=$ergebnis[3];
         }



return $html_output;

}

// Body of this php file

if ($nb==0)
{
	employees($status,$prod,$time_en,$year_from,$zeit_from,$year_to,$zeit_to,$felder,$sort1,$sort2,$sort3,$order,$emplo,$darstellung);
}
?>        
<HTML>
<HEAD>
<link rel="stylesheet" href="../../css/ta.css">
<script language=javascript>

function loadForm(){
window.document.formular.submit();
}

</script>
</HEAD>
<BODY>
<center>
<table border=0 cellspacing=0 cellpadding=0>
<tr><td height=12></td></tr>
<tr><td width=200 height=27 align=center valign=top background='../../images/underlineheader.gif'>
<SPAN class="headline">Trainerabfrage</SPAN><br>
</td></tr>
<tr><td height=10></td></tr>
</table>
<BR><BR>


<FORM action="<? echo $PHP_SELF?>?id=<?echo $id?>" method="POST" name=formular>

	<TABLE width=700 border=1 CELLPADDING=0 CELLSPACING=0>	
		<TR>       
           <TD width=100% class=form_row align=middle>
			   <TABLE BORDER=0 width=100%>
				   <TR>				   
					   <TD height=40 colspan=3><b><?echo $no_relation_text?></b></TD>
				   </TR>
				</table>

<div align=center>
<TABLE width=500 border=0 CELLPADDING=0 CELLSPACING=0>
<tr>
   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1  class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Trainer</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <SELECT NAME='emplo[]' multiple>
<? if (!(isset($emplo))) {$emplo[0]="-1";} ?>
   <OPTION VALUE=-1 <? for ($i=0;$i<=sizeof($emplo);$i++) { if ($emplo[$i]=="-1") echo " selected";} ?>>Alle</OPTION>
   <?
   $rs_emplo=getrs("select id,firstname,lastname from employees where status<>'Inaktiv' order by lastname,firstname asc",0,1);
   while(LIST($employee_id,$firstname,$lastname)=$rs_emplo -> fetch_row())
   {?>
         <OPTION VALUE="<? echo($employee_id) ?>" <? for ($i=0;$i<=sizeof($emplo);$i++) { if ($emplo[$i]==$employee_id) echo " selected";} ?>><?echo($lastname.",".$firstname)?></OPTION>
<?  }
           ?>

   </SELECT>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Produkte</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <SELECT NAME='prod[]' multiple>
<? if (!(isset($prod))) {$prod[0]="-1";} ?>
   <OPTION VALUE=-1 <? for ($i=0;$i<=sizeof($prod);$i++) { if ($prod[$i]=="-1") echo " selected";} ?>>Alle</OPTION>
   <?
   $rs_prod=getrs("select id,name from products where status='Aktiv' order by name asc",0,1);
   while(LIST($pid,$name)= $rs_prod -> fetch_row())
   {?>
         <OPTION VALUE="<? echo($pid) ?>" <? for ($i=0;$i<=sizeof($prod);$i++) { if ($prod[$i]==$pid) echo " selected";} ?>><?echo($name)?></OPTION>
<?  }
           ?>

   </SELECT>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Status</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
 <? if (!(isset($status))) {$status[0]=3;$status[1]=1;} ?>
   <SELECT NAME=status[] multiple>
   <OPTION VALUE=4 <? for ($i=0;$i<=sizeof($status);$i++) { if ($status[$i]=="4") echo " selected";} ?>>Alle</OPTION>
   <OPTION VALUE=1 <? for ($i=0;$i<=sizeof($status);$i++) { if ($status[$i]=="1") echo " selected";} ?>>Aktiv</OPTION>
   <OPTION VALUE=0 <? for ($i=0;$i<=sizeof($status);$i++) { if ($status[$i]=="0") echo " selected";} ?>>Inaktiv</OPTION>
   </SELECT>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>von Jahr</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
 <? if (!(isset($year_from))) {$year_from=(date("Y"))-1;} ?>
   <input type="text" size=50 NAME=year_from value= "<? print($year_from) ?>">
			<select name="zeit_from" class="input_text">
			<option value=0 <? if ($zeit_from=='0') { echo "selected";} ?>>Alle</option>
			<?
			$rs_time=getrs("select id,name from timeperiods where status='Aktiv'",0,1);
			While ($rs_time>0 && List($tid,$name) = $rs_time -> fetch_row())
			{ 
				if ($tid==$zeit_from) 
				{ ?>
					<option class="input_text" value=<?print($tid)?> selected><?print($name)?></option>
			<?	} else {?>
					<option class="input_text" value=<?print($tid)?>><?print($name)?></option>
			<?	} ?>
		<?	} ?>
			</select>
   
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>bis Jahr</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
 <? if (!(isset($year_to))) {$year_to=date("Y");} ?>
   <input type="text" size=50 NAME=year_to value="<? print($year_to) ?>">
			<select name="zeit_to" class="input_text">
			<option value=0 <? if ($zeit_to=='0') { echo "selected";} ?>>Alle</option>
			<?
			$rs_time_end=getrs("select id,name from timeperiods where status='Aktiv'",0,1);
			While ($rs_time_end>0 && List($tid,$name) = $rs_time_end -> fetch_row())
			{ 
				if ($tid==$zeit_to) 
				{ ?>
					<option class="input_text" value=<?print($tid)?> selected><?print($name)?></option>
			<?	} else {?>
					<option class="input_text" value=<?print($tid)?>><?print($name)?></option>
			<?	} ?>
		<?	} ?>
			</select>

   </TD></TR></TABLE></TD>
</TR>

<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Darstellung</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <input type="radio" name="darstellung" value="own" <?if ($darstellung=="own") {echo "checked";}?>>Nur eigene Kurse (Standard Kursleiter) anzeigen<br>
   <input type="radio" name="darstellung" value="all" <?if ($darstellung=="all") {echo "checked";}?>>Alle Kurse und gehaltenen Kurseinheiten anzeigen<br>
   </TD></TR></TABLE></TD>
</TR>

<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>

   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>zus�tzliche <br>Auswahlfelder</TD></TR></TABLE></TD>

   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>

   <select name='felder[]' multiple onblur="javascript:loadForm()">
<?
      for ($i=0;$i<=sizeof($felder);$i++)
      {
          if ($felder[$i]=="keine") { $sel_id= "selected";}
          if ($felder[$i]=="a.title as Titel") { $sel_titel="selected"; }
          if ($felder[$i]=="a.address as Adresse") { $sel_adresse="selected"; }
          if ($felder[$i]=="a.zip as PLZ") { $sel_plz="selected"; }
          if ($felder[$i]=="a.city as Ort") { $sel_ort="selected"; }
          if ($felder[$i]=="a.phone1 as Tel1") { $sel_tel1="selected"; }
          if ($felder[$i]=="a.phone2 as Tel2") { $sel_tel2="selected"; }
          if ($felder[$i]=="a.fax as Fax") { $sel_fax="selected"; }
          if ($felder[$i]=="a.email as Email") { $sel_email="selected"; }
          if ($felder[$i]=="a.sv_number as SV_Nummer") { $sel_sv="selected"; }
          if ($felder[$i]=="a.bank_account as Kontonr") { $sel_konto="selected"; }
          if ($felder[$i]=="a.bank_code as BLZ") { $sel_blz="selected"; }
          if ($felder[$i]=="a.birthdate as Geburtstag") { $sel_geburtstag="selected"; }
          if ($felder[$i]=="a.username as Username") { $sel_username="selected"; }
          if ($felder[$i]=="a.status as Status") { $sel_status="selected"; }
          if ($felder[$i]=="b.hourcost as Stundensatz") { $sel_stundensatz="selected"; }
          if ($felder[$i]=="count(d.id) as Anzahl") { $sel_anzahl="selected"; }
     }

?>
   	<option value="keine" <?echo $sel_id?>> </option>

   	<option value="a.title as Titel" <?echo $sel_titel?>>Titel</option>

   	<option value="a.address as Adresse" <?echo $sel_adresse?>>Adresse</option>

   	<option value="a.zip as PLZ" <?echo $sel_plz?>>PLZ</option>

   	<option value="a.city as Ort" <?echo $sel_ort?>>Ort</option>

   	<option value="a.phone1 as Tel1" <?echo $sel_tel1?>>Tel1</option>

   	<option value="a.phone2 as Tel2" <?echo $sel_tel2?>>Tel2</option>

   	<option value="a.fax as Fax" <?echo $sel_fax?>>Fax</option>

   	<option value="a.email as Email" <?echo $sel_mail?>>Mail</option>

   	<option value="a.sv_number as SV_Nummer" <?echo $sel_sv?>>SV-Nummer</option>

   	<option value="a.bank_account as Kontonr" <?echo $sel_konto?>>Kontonummer</option>

   	<option value="a.bank_code as BLZ" <?echo $sel_blz?>>BLZ</option>

   	<option value="a.birthdate as Geburtstag" <?echo $sel_geburtstag?>>Geburtstag</option>

   	<option value="a.username as Username" <?echo $sel_username?>>Username</option>

   	<option value="a.status as Status" <?echo $sel_status?>>Status</option>

   	<option value="b.hourcost as Stundensatz" <?echo $sel_stundensatz?>>Stundensatz</option>

   	<option value="count(d.id) as Anzahl" <?echo $sel_anzahl?>>Anzahl Einheiten</option>
   </select>
   </TD></TR></TABLE></TD>

</TR>

<tr>

   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

</TR>



<TR>

   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Sortierung</TD></TR></TABLE></TD>

   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

   <TD class=form_row width=80%><TABLE><TR><TD WIDTH=100% HEIGHT=100%>

   <select name='sort1'>
				<option <?if ($sort1=="Nachname") echo "selected";?> value="Nachname">Nachname</option>
				<option <?if ($sort1=="Vorname") echo "selected";?> value="Vorname">Vorname</option>
				<option <?if ($sort1=="Produkt") echo "selected";?> value="Produkt">Produkt</option>
				<option <?if ($sort1=="Jahr") echo "selected";?> value="Jahr">Jahr</option>
				<option <?if ($sort1=="Zeitperiode") echo "selected";?> value="Zeitperiode">Zeitperiode</option>
				<option <?if ($sort1=="Institution") echo "selected";?> value="Institution">Institution</option>
				<option <?if ($sort1=="Ort") echo "selected";?> value="Ort">Ort</option>
				<option <?if ($sort1=="Info") echo "selected";?> value="Info">Info</option>
				<option <?if ($sort1=="Vertretung") echo "selected";?> value="Vertretung">Info</option>

<?
	for($aa=0;$aa<sizeof($felder);$aa++)

    {
?>
				<? $felder_part=explode(" as ",$felder[$aa]);?>

				<option <?if ($felder_part[1]==$sort1) echo "selected";?> value="<?echo($felder_part[1])?>"><?echo($felder_part[1])?></option>
<? }?>
   </select>

   &nbsp;&nbsp;&nbsp;&nbsp;

   <select name='sort2'>
				<option <?if ($sort2=="Nachname") echo "selected";?> value="Nachname">Nachname</option>
				<option <?if ($sort2=="Vorname") echo "selected";?> value="Vorname">Vorname</option>
				<option <?if ($sort2=="Produkt") echo "selected";?> value="Produkt">Produkt</option>
				<option <?if ($sort2=="Jahr") echo "selected";?> value="Jahr">Jahr</option>
				<option <?if ($sort2=="Zeitperiode") echo "selected";?> value="Zeitperiode">Zeitperiode</option>
				<option <?if ($sort2=="Institution") echo "selected";?> value="Institution">Institution</option>
				<option <?if ($sort2=="Ort") echo "selected";?> value="Ort">Ort</option>
				<option <?if ($sort2=="Info") echo "selected";?> value="Info">Info</option>
				<option <?if ($sort2=="Vertretung") echo "selected";?> value="Vertretung">Info</option>

<?
	for($aa=0;$aa<sizeof($felder);$aa++)

    {
?>
				<? $felder_part=explode(" as ",$felder[$aa]);?>

				<option <?if ($felder_part[1]==$sort2) echo "selected";?> value="<?echo($felder_part[1])?>"><?echo($felder_part[1])?></option>
<? }?>
   </select>

   &nbsp;&nbsp;&nbsp;&nbsp;

   <select name='sort3'>
				<option <?if ($sort3=="Nachname") echo "selected";?> value="Nachname">Nachname</option>
				<option <?if ($sort3=="Vorname") echo "selected";?> value="Vorname">Vorname</option>
				<option <?if ($sort3=="Produkt") echo "selected";?> value="Produkt">Produkt</option>
				<option <?if ($sort3=="Jahr") echo "selected";?> value="Jahr">Jahr</option>
				<option <?if ($sort3=="Zeitperiode") echo "selected";?> value="Zeitperiode">Zeitperiode</option>
				<option <?if ($sort3=="Institution") echo "selected";?> value="Institution">Institution</option>
				<option <?if ($sort3=="Ort") echo "selected";?> value="Ort">Ort</option>
				<option <?if ($sort3=="Info") echo "selected";?> value="Info">Info</option>
				<option <?if ($sort3=="Vertretung") echo "selected";?> value="Vertretung">Info</option>

<?
	for($aa=0;$aa<sizeof($felder);$aa++)

    {
?>
				<? $felder_part=explode(" as ",$felder[$aa]);?>

				<option <?if ($felder_part[1]==$sort3) echo "selected";?> value="<?echo($felder_part[1])?>"><?echo($felder_part[1])?></option>
<? }?>

   </select>

   </TD></TR></TABLE></TD>

</TR>

<tr>

   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

</TR>

<TR>

   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Sortierung</TD></TR></TABLE></TD>

   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>

   <select name='order'>

   	<option value="asc" <?if ($order=="asc") echo "selected";?>>aufsteigend</option>

   	<option value="desc" <?if ($order=="desc") echo "selected";?>>absteigend</option>

   </select>

   </TD></TR></TABLE></TD>

</TR>

<tr>

   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

</TR>

	</TABLE>

	<TABLE width=600 border=0 cellpadding=0 cellspacing=0>
	<TR>
		<TD colspan=3 height=50>


			<TABLE WIDTH=100% HEIGHT=100%>
				<TR>
				<TD align=center valign=bottom ALIGN=CENTER>
		
						<input type=hidden value="0" name="nb">
						<input type=image src="../../images/buttons/suchen.gif" BORDER=0>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<A HREF="javascript:window.print()"><IMG src="../../images/buttons/drucken.gif" BORDER=0></A>
					</TD>
				</TR>
			</TABLE>
		</TD> 
	</TR>
	</TABLE>
	</FORM>		
</TABLE>
</div>	
	<? echo $html_output;?> 

	
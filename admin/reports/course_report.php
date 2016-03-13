<?php
require_once("../../include/session.php");
require_once("../../include/html.php");
require_once("../../include/checkfunction.php");

$nb=$_GET["nb"];
$darstellung=$_POST["darstellung"];
$felder=$_POST["felder"];
$sort1=$_POST["sort1"];
$sort2=$_POST["sort2"];
$sort3=$_POST["sort3"];
$order=$_POST["order"];
$year=$_POST["year"];
$timeperiod=$_POST["timeperiod"];
$wday=$_POST["wday"];
$sql_view=$_POST["sql_view"];


// Kurs�bersicht function ///

function courses_report ($year,$timeperiod,$sort1,$sort2,$sort3,$order,$wday,$sql_view)
{
	global $html_output;
	global $query_text;

	if ($wday>0)
	{
		$weekday_select = " and a.weekday=".$wday;
	} else
	{
		$weekday_select ="";
	}
	$kurs=" select 	a.ID, 
					f.weekday,
					b.name as Kursart,
					a.info as Besonderheit, 
					min(d.date) as erstesDatum, 
					a.standard_time as Uhrzeit,
					a.standard_durance as Dauer, 
					e.name as Institution, 
					a.price as SP, 
					a.jbetrag as JP,
					c.name as Veranstalter,
					c.zip,
					concat(g.lastname,' ',g.firstname) as Kursleiter,
					a.remarks,
					a.intern_remarks,
					c.id,
					e.id,
					g.id,
					b.id,
					a.type as Kursinfo
			from 
					products b, institutions c, coursetimes d, institutions e, weekdays f,courses a 
left join employees g on a.standard_employee=g.id			
where 					f.id=a.weekday and 
					e.id=a.locations_id and 
					a.year=$year and 
					a.timeperiods_id=$timeperiod 
					$weekday_select 
					and a.institutions_id = c.id 
					and a.products_id=b.id 
					and d.courses_id=a.id
					and a.status!='Entfernt' 
			group by a.id order by $sort1,$sort2,$sort3 $order";
	$rs_kurs=getrs($kurs,$sql_view,1);
	$num=$rs_kurs->num_rows;

	/* Bestimmung der Vorg�nger Kurse im vorigen und vor-vorigen Semester */
	if($timeperiod==1) 
	{
		$last_timeperiod=2;
		$last_year=$year-1;
		$last_last_timeperiod=$timeperiod;
		$last_last_year=$year-1;
		$last_3_timeperiod=2;
		$last_3_year=$year-2;
		$last_4_timeperiod=$timeperiod;
		$last_4_year=$year-2;
		$last_5_timeperiod=2;
		$last_5_year=$year-3;
		$last_6_timeperiod=$timeperiod;
		$last_6_year=$year-3;
		$last_7_timeperiod=2;
		$last_7_year=$year-4;
		$last_8_timeperiod=$timeperiod;
		$last_8_year=$year-4;
		$last_9_timeperiod=2;
		$last_9_year=$year-5;
	}
	else if($timeperiod==2)
	{
		$last_timeperiod=1;
		$last_year=$year;
		$last_last_timeperiod=$timeperiod;
		$last_last_year=$year-1;
		$last_3_timeperiod=1;
		$last_3_year=$year-1;
		$last_4_timeperiod=$timeperiod;
		$last_4_year=$year-2;
		$last_5_timeperiod=1;
		$last_5_year=$year-2;
		$last_6_timeperiod=$timeperiod;
		$last_6_year=$year-3;
		$last_7_timeperiod=1;
		$last_7_year=$year-3;
		$last_8_timeperiod=$timeperiod;
		$last_8_year=$year-4;
		$last_9_timeperiod=1;
		$last_9_year=$year-4;
	}
	else
	{
		$last_timeperiod=0;
		$last_year=0;
	}
	

	$html_output="<br><div align=center>Anzahl der Datens&auml;tze: $num</div><br>";
	$html_output.="
	<TABLE width=100% CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1>

		<tr>

			<td align=center>
				<b>Wochentag</b>
			</td>
			<td align=center>
				<b>Kursart</b>
			</td>
			<td align=center>
				<b>Besonderheit</b>
			</td>
			<td align=center>
				<b>Kurstyp</b>
			</td>
			<td width=100 align=center>
				<b>Beginn</b>
			</td>
			<td align=center>
				<b>Anzahl<br>Kinder</b>
			</td>
			<td>
				<b>Kinder<br>$last_timeperiod.$last_year</b>
			</td>
			<td>
				<b>Kinder<br>$last_last_timeperiod.$last_last_year</b>
			</td>
			<td>
				<b>Kinder<br>$last_3_timeperiod.$last_3_year</b>
			</td>
			<td>
				<b>Kinder<br>$last_4_timeperiod.$last_4_year</b>
			</td>
			<td>
				<b>Kinder<br>$last_5_timeperiod.$last_5_year</b>
			</td>
			<td>
				<b>Kinder<br>$last_6_timeperiod.$last_6_year</b>
			</td>
			<td>
				<b>Kinder<br>$last_7_timeperiod.$last_7_year</b>
			</td>
			<td>
				<b>Kinder<br>$last_8_timeperiod.$last_8_year</b>
			</td>
			<td>
				<b>Kinder<br>$last_9_timeperiod.$last_9_year</b>
			</td>
			<td align=center>
				<b>von</b>
			</td>
			<td align=center>
				<b>Dauer</b>
			</td>
			<td width=200 align=center>
				<b>Kursort</b>
			</td>
			<td align=center>
				<b>EH</b>
			</td>
			<td align=center>
				<b>SP</b>
			</td>
			<td align=center>
				<b>JP</b>
			</td>
			<td width=200 align=center>
				<b>Kursleiter</b>
			</td>
			<td align=center>
				<b>Bemerkungen</b>
			</td>
	";
	$html_output.="</tr>";
         
		 
		 while($ergebnis=$rs_kurs->fetch_row())

         {
// 1 //			/* Bestimmen der last und last-last Kursteilnehmer */
			$sql_last_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_year' and
									a.timeperiods_id='$last_timeperiod' and
									a.type='$ergebnis[19]' group by b.courses_id";
										
			$rs_last_anzahl_kinder=getrs($sql_last_anzahl_kinder,$sql_view,1);
			list($last_id,$last_anzahl_kinder)=$rs_last_anzahl_kinder->fetch_row();
// 2 //		
			$sql_last_last_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_last_year' and
									a.timeperiods_id='$last_last_timeperiod' and
									a.type = '$ergebnis[19]'  group by b.courses_id";
										
			$rs_last_last_anzahl_kinder=getrs($sql_last_last_anzahl_kinder,$sql_view,1);
			list($last_last_id,$last_last_anzahl_kinder)=$rs_last_last_anzahl_kinder->fetch_row();
// 3 //
			$sql_last_3_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_3_year' and
									a.timeperiods_id='$last_3_timeperiod' and
									a.type = '$ergebnis[19]'  group by b.courses_id";
										
			$rs_last_3_anzahl_kinder=getrs($sql_last_3_anzahl_kinder,$sql_view,1);
			list($last_3_id,$last_3_anzahl_kinder)=$rs_last_3_anzahl_kinder->fetch_row();
// 4 //
			$sql_last_4_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_4_year' and
									a.timeperiods_id='$last_4_timeperiod' and
									a.type = '$ergebnis[19]'  group by b.courses_id";
										
			$rs_last_4_anzahl_kinder=getrs($sql_last_4_anzahl_kinder,$sql_view,1);
			list($last_4_id,$last_4_anzahl_kinder)=$rs_last_4_anzahl_kinder->fetch_row();
// 5 //
			$sql_last_5_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_5_year' and
									a.timeperiods_id='$last_5_timeperiod' and
									a.type = '$ergebnis[19]'  group by b.courses_id";
										
			$rs_last_5_anzahl_kinder=getrs($sql_last_5_anzahl_kinder,$sql_view,1);
			list($last_5_id,$last_5_anzahl_kinder)=$rs_last_5_anzahl_kinder->fetch_row();
// 6 //
			$sql_last_6_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_6_year' and
									a.timeperiods_id='$last_6_timeperiod' and
									a.type = '$ergebnis[19]'  group by b.courses_id";
										
			$rs_last_6_anzahl_kinder=getrs($sql_last_6_anzahl_kinder,$sql_view,1);
			list($last_6_id,$last_6_anzahl_kinder)=$rs_last_6_anzahl_kinder->fetch_row();
// 7 //
			$sql_last_7_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_7_year' and
									a.timeperiods_id='$last_7_timeperiod' and
									a.type = '$ergebnis[19]'  group by b.courses_id";
										
			$rs_last_7_anzahl_kinder=getrs($sql_last_7_anzahl_kinder,$sql_view,1);
			list($last_7_id,$last_7_anzahl_kinder)=$rs_last_7_anzahl_kinder->fetch_row();
// 8 //
			$sql_last_8_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_8_year' and
									a.timeperiods_id='$last_8_timeperiod' and
									a.type = '$ergebnis[19]'  group by b.courses_id";
										
			$rs_last_8_anzahl_kinder=getrs($sql_last_8_anzahl_kinder,$sql_view,1);
			list($last_8_id,$last_8_anzahl_kinder)=$rs_last_8_anzahl_kinder->fetch_row();
// 9 //
			$sql_last_9_anzahl_kinder="select a.id,count(b.clients_id) from courses a, payments b, clients c 
								where 
									c.id=b.clients_id and 
									a.id=b.courses_id and 
									a.status!='Entfernt' 
									and b.status!='Entfernt' 
									and c.status!='Entfernt' 
									and a.products_id='$ergebnis[18]' and
									a.institutions_id='$ergebnis[15]' and
									a.locations_id='$ergebnis[16]' and
									a.year='$last_9_year' and
									a.timeperiods_id='$last_9_timeperiod' and
									a.type = '$ergebnis[19]'  group by b.courses_id";
										
			$rs_last_9_anzahl_kinder=getrs($sql_last_9_anzahl_kinder,$sql_view,1);
			list($last_9_id,$last_9_anzahl_kinder)=$rs_last_9_anzahl_kinder->fetch_row();

			$sql_anzahl_kinder="select count(b.clients_id) from courses a, payments b, clients c where c.id=b.clients_id and a.id=b.courses_id and a.status!='Entfernt' and b.status!='Entfernt' and c.status!='Entfernt' and a.id=$ergebnis[0]";	
			$rs_anzahl_kinder=getrs($sql_anzahl_kinder,$sql_view,1);
			list($anzahl_kinder)=$rs_anzahl_kinder->fetch_row();

         	for ($ii=0;$ii<=17;$ii++)
			{
				if ($ergebnis[$ii]=="") $ergebnis[$ii]="&nbsp;";
			}
			$html_output.="<tr>";
			
			/* Einf�rben der Zellen, je nach Kriterium */
			$cell_color = "";
			$cell_color_storno="";
			if (($anzahl_kinder>$last_anzahl_kinder) && ($anzahl_kinder>$last_last_anzahl_kinder)) {$cell_color=" bgcolor=green ";}
			if ($anzahl_kinder<$last_anzahl_kinder) {$cell_color=" bgcolor=yellow ";}
			if ($anzahl_kinder==0) {$cell_color=" bgcolor=red ";}
			if (preg_match ("/storniert/i", $ergebnis[3])) {$cell_color_storno=" bgcolor=red ";}
			
			$dauer_norm = $ergebnis[6]/1;

			$html_output.="
				<td ";
					switch ($ergebnis[1]) 
						{
							case "Montag":
								$html_output.="bgcolor=blue"; 
								break; 
							case "Dienstag":
								$html_output.="bgcolor=green";
								break;
							case "Mittwoch":
								$html_output.="bgcolor=yellow";
								break;
							case "Donnerstag":
								$html_output.="bgcolor=red";
								break;
							case "Freitag":
								$html_output.="bgcolor=pink";
								break;
							case "Samstag":
								$html_output.="bgcolor=white";
								break;
						}				
			 switch($ergebnis[19])
			 {
			 	case "0":$kursinfo_text="k.A.";break;
				case "1":$kursinfo_text="KG";break;
			 	case "2":$kursinfo_text="VS 1./2.Kl";break;			 	
				case "3":$kursinfo_text="VS 3./4.Kl";break;
			 	case "4":$kursinfo_text="VS 1.-4.Kl";break;			 	
			 	case "5":$kursinfo_text="Krippe";break;			 	
			 	case "6":$kursinfo_text=">10 J";break;			 	
			 }
			
			$html_output.=" align=center $cell_color_storno><a target=_blank href='../kurs_form.php?id=$ergebnis[0]'>$ergebnis[1]</a>&nbsp;</td> <!-- Wochentag -->
				<td align=center $cell_color_storno><a target=_blank href='../kurs_form.php?id=$ergebnis[0]'>$ergebnis[2]</a>&nbsp;</td> <!-- Kursart -->
				<td align=center $cell_color_storno><a target=_blank href='../kurs_form.php?id=$ergebnis[0]'>$ergebnis[3]</a>&nbsp;</td> <!-- Besonderheit -->
				<td align=center $cell_color_storno><a target=_blank href='../kurs_form.php?id=$ergebnis[0]'>$kursinfo_text</a>&nbsp;</td> <!-- Kursinfo -->
				<td align=center $cell_color_storno>$ergebnis[4]&nbsp;</td> <!-- Erstes Datum -->";
			$html_output.="
				<td align=center $cell_color_storno $cell_color>
				<a target=_blank href='../kursblatt_form.php?id=$ergebnis[0]'>$anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_id'>$last_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_last_id'>$last_last_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_3_id'>$last_3_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_4_id'>$last_4_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_5_id'>$last_5_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_6_id'>$last_6_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_7_id'>$last_7_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_8_id'>$last_8_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno><a target=_blank href='../kursblatt_form.php?id=$last_9_id'>$last_9_anzahl_kinder</a>&nbsp;</td> <!-- Anzahl Kinder --> 
				<td align=center $cell_color_storno>$ergebnis[5]&nbsp;</td> <!-- Uhrzeit von -->
				<td align=center $cell_color_storno>$dauer_norm&nbsp;</td> <!-- Dauer -->
				<td align=center $cell_color_storno><a target=_blank href='../institution_form.php?id=$ergebnis[15]'>$ergebnis[7]</a>&nbsp;</td> <!-- Institution -->";
			$sql_anzahl_termine="select count(b.id) from courses a, coursetimes b where a.id=b.courses_id and a.status!='Entfernt' and a.id=$ergebnis[0]";	
			$rs_termine=getrs($sql_anzahl_termine,$sql_view,1);
			list($anzahl_termine)=$rs_termine->fetch_row();
			$html_output.="
				<td align=center $cell_color_storno>$anzahl_termine&nbsp;</td> <!-- Einheiten --> 
				<td align=center $cell_color_storno>$ergebnis[8]&nbsp;</td> <!-- SP -->
				<td align=center $cell_color_storno>$ergebnis[9]&nbsp;</td> <!-- JP -->
<!--				<td align=center $cell_color_storno><a target=_blank href='../institution_form.php?id=$ergebnis[16]'>$ergebnis[11], $ergebnis[10]</a>&nbsp;</td> --> <!-- Veranstalter -->
				<td align=center $cell_color_storno><a target=_blank href='../employee_form.php?id=$ergebnis[17]'>$ergebnis[12]</a>&nbsp;</td> <!-- Kursleiter 1 -->
				<td align=center $cell_color_storno>$ergebnis[13]<br>$ergebnis[14]&nbsp;</td> <!-- Bemerkungen -->";

			$html_output.="</tr>";
         }


return $html_output;

}

// Body of this php file

if ($nb==0)
{
	courses_report ($year,$timeperiod,$sort1,$sort2,$sort3,$order,$wday,$sql_view);
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
<BODY <?if ($new=="1") { print("onload='javascript:loadForm()'");}?>>
<center>
<table border=0 cellspacing=0 cellpadding=0>
<tr><td height=12></td></tr>
<tr><td width=200 height=27 align=center valign=top background='../../images/underlineheader.gif'>
<SPAN class="headline">Kursuebersicht</SPAN>
</td></tr>
</table>


<FORM action="course_report.php" method="POST" name=formular>

	<TABLE width=700 border=0 CELLPADDING=0 CELLSPACING=0>	
		<TR>       
           <TD width=100% class=form_row align=middle>

<div align=center>
<TABLE width=500 border=0 CELLPADDING=0 CELLSPACING=0>
<tr>
   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1  class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Zeitangaben</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
 <? if (!(isset($year))) {$year=(date("Y"));} ?>
   <input type="text" size=10 NAME=year value= "<? print($year) ?>">

			<select name="timeperiod" class="input_text">
			<?
			$SQL="select id,name from timeperiods where status='Aktiv'";
			$time = getrs($SQL,0,1);
			While ($time>0 && List($id,$name) = $time->fetch_row())
			{ 
				if ($id==$timeperiod) 
				{ ?>
					<option class="input_text" value=<?print($id)?> selected><?print($name)?></option>
			<?	} else {?>
					<option class="input_text" value=<?print($id)?>><?print($name)?></option>
			<?	} ?>
		<?	} ?>
			</select>

			<select name="wday" class="input_text">
			<option value=0 <? if ($wday=='0') { echo "selected";} ?>>Alle</option>
			<?
			$SQL="select id,weekday from weekdays order by id asc";
			$time_wday = getrs($SQL,0,1);
			While ($time_wday>0 && List($wday_id,$wday_string) = $time_wday->fetch_row())
			{ 
				if ($wday_id==$wday) 
				{ ?>
					<option class="input_text" value=<?print($wday_id)?> selected><?print($wday_string)?></option>
			<?	} else {?>
					<option class="input_text" value=<?print($wday_id)?>><?print($wday_string)?></option>
			<?	} ?>
		<?	} ?>
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
				<option <?if ($sort1=="a.weekday") echo "selected";?> value="a.weekday">Wochentag</option>
				<option <?if ($sort1=="Kursart") echo "selected";?> value="Kursart">Kursart</option>
				<option <?if ($sort1=="Besonderheit") echo "selected";?> value="Besonderheit">Besonderheit</option>
				<option <?if ($sort1=="erstesDatum") echo "selected";?> value="erstesDatum">erstes Datum</option>
				<option <?if ($sort1=="Uhrzeit") echo "selected";?> value="Uhrzeit">Uhrzeit</option>
				<option <?if ($sort1=="Dauer") echo "selected";?> value="Dauer">Dauer</option>
				<option <?if ($sort1=="Institution") echo "selected";?> value="Institution">Institution</option>
				<option <?if ($sort1=="SP") echo "selected";?> value="SP">SP</option>
				<option <?if ($sort1=="JP") echo "selected";?> value="JP">JP</option>
				<option <?if ($sort1=="Veranstalter") echo "selected";?> value="Veranstalter">Veranstalter</option>
				<option <?if ($sort1=="Kursleiter") echo "selected";?> value="Kursleiter">Kursleiter</option>

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
				<option <?if ($sort2=="a.weekday") echo "selected";?> value="a.weekday">Wochentag</option>
				<option <?if ($sort2=="Kursart") echo "selected";?> value="Kursart">Kursart</option>
				<option <?if ($sort2=="Besonderheit") echo "selected";?> value="Besonderheit">Besonderheit</option>
				<option <?if ($sort2=="erstesDatum") echo "selected";?> value="erstesDatum">erstes Datum</option>
				<option <?if ($sort2=="Uhrzeit") echo "selected";?> value="Uhrzeit">Uhrzeit</option>
				<option <?if ($sort2=="Dauer") echo "selected";?> value="Dauer">Dauer</option>
				<option <?if ($sort2=="Institution") echo "selected";?> value="Institution">Institution</option>
				<option <?if ($sort2=="SP") echo "selected";?> value="SP">SP</option>
				<option <?if ($sort2=="JP") echo "selected";?> value="JP">JP</option>
				<option <?if ($sort2=="Veranstalter") echo "selected";?> value="Veranstalter">Veranstalter</option>
				<option <?if ($sort2=="Kursleiter") echo "selected";?> value="Kursleiter">Kursleiter</option>

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
				<option <?if ($sort3=="a.weekday") echo "selected";?> value="a.weekday">Wochentag</option>
				<option <?if ($sort3=="Kursart") echo "selected";?> value="Kursart">Kursart</option>
				<option <?if ($sort3=="Besonderheit") echo "selected";?> value="Besonderheit">Besonderheit</option>
				<option <?if ($sort3=="erstesDatum") echo "selected";?> value="erstesDatum">erstes Datum</option>
				<option <?if ($sort3=="Uhrzeit") echo "selected";?> value="Uhrzeit">Uhrzeit</option>
				<option <?if ($sort3=="Dauer") echo "selected";?> value="Dauer">Dauer</option>
				<option <?if ($sort3=="Institution") echo "selected";?> value="Institution">Institution</option>
				<option <?if ($sort3=="SP") echo "selected";?> value="SP">SP</option>
				<option <?if ($sort3=="JP") echo "selected";?> value="JP">JP</option>
				<option <?if ($sort3=="Veranstalter") echo "selected";?> value="Veranstalter">Veranstalter</option>
				<option <?if ($sort3=="Kursleiter") echo "selected";?> value="Kursleiter">Kursleiter</option>
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
		<TD colspan=3 height=10>


			<TABLE WIDTH=100% HEIGHT=100%>
				<TR>
				<TD align=center valign=bottom ALIGN=CENTER>
						<input type=hidden name=sql_view value=<?print($sql_view);?>>
						<input type=submit src="../../images/buttons/suchen.gif" BORDER=0 value=senden>
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

	
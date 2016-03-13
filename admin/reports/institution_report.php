<?php
require_once("../../include/session.php");
require_once("../../include/html.php");
require_once("../../include/checkfunction.php");

$nb=$_GET["nb"];
$prod=$_POST["prod"];
$inst=$_POST["inst"];
$status=$_POST["status"];
$felder=$_POST["felder"];
$sort1=$_POST["sort1"];
$sort2=$_POST["sort2"];
$sort3=$_POST["sort3"];
$order=$_POST["order"];
$darstellung=$_POST["darstellung"];
$year_from=$_POST["year_from"];
$year_to=$_POST["year_to"];
$zeit_to=$_POST["zeit_to"];
$zeit_from=$_POST["zeit_from"];
$filter_period=$_POST["filter_period"];
$birthdate_field=$_POST["birthdate_field"];
$birthdate_sign=$_POST["birthdate_sign"];
$plz=$_POST["plz"];
$ort=$_POST["ort"];
$search=$_POST["search"];
$suchen_x=$_POST["suchen_x"];
$print_debug=$_POST["print_debug"];
$print_error=$_POST["print_error"];

/* besserer SQL-String
select a.ID,a.lastname as Nachname,a.firstname as Vorname,a.zip as PLZ, a.city as Ort, a.address as Adresse,b.name as Schule,f.name as Sportart,e.year as Jahr from clients a, institutions b, courses e, products f, timeperiods g,payments h where a.status!='Entfernt' and h.clients_id=a.id and h.courses_id=e.id and e.timeperiods_id=g.id and e.products_id=f.id and b.id=a.school_id and a.status in ('Aktiv') and (e.year>=2008 and e.year<=2009) and e.products_id in ('7') group by f.id,a.id order by a.lastname asc,a.firstname, a.lastname asc,a.firstname, a.lastname asc,a.firstname asc
*/

function institutions_report ($status,$prod,$inst,$year_from,$zeit_from,$year_to,$zeit_to,$plz_,$ort,$felder,$sort1,$sort2,$sort3,$order,$darstellung,$birthdate_field,$birthdate_sign,$sql_view,$filter_period)
{
		global $html_output;
		global $query_text;

/*		for($c=0;$c<sizeof($status);$c++)
	    {
				if     ($status[$c]==0){$status[$c]="Inaktiv";}
				elseif ($status[$c]==1){$status[$c]="Aktiv";}
				elseif ($status[$c]==3){$status[$c]="Neu";}
				elseif ($status[$c]==2){$status[$c]="derz.n.verf&uuml;gbar";}
				if($c<(sizeof($status)-1))
					$sql_status.="'".$status[$c]."'".",";
				elseif($c==(sizeof($status)-1))
			    	$sql_status.="'".$status[$c]."'";
		}
*/
		$sql_status="'".$status."'";
		for($aa=0;$aa<sizeof($inst);$aa++)
	    {
				if($aa<(sizeof($inst)-1))
					$sql_inst.="'".$inst[$aa]."'".",";
				elseif($aa==(sizeof($inst)-1))
				    $sql_inst.="'".$inst[$aa]."'";
		}

		for($aa=0;$aa<sizeof($prod);$aa++)
	    {
				if($aa<(sizeof($prod)-1))
					$sql_prod.="'".$prod[$aa]."'".",";
				elseif($aa==(sizeof($prod)-1))
				    $sql_prod.="'".$prod[$aa]."'";
		}

	if (($inst[0]=='-1') || ($inst[0]=='-2'))   
	{
			$inst_select        = "  ";
	}
	else
	{
			$inst_select        = "and a.school_id in (".$sql_inst.") ";
	}

	if (($prod[0]=='-1') || ($prod[0]=='-2'))   
	{
			$prod_select        = "  ";
	}
	else
	{
			$prod_select        = "and e.products_id in (".$sql_prod.") ";
	}

	if ($status[0]=='4')   
	{
			$select_from        = "  ";
	}
	else
	{
			$select_from        = "and a.status in (".$sql_status.") ";
	}

	if ($filter_period=='0')   
	{
			$select_filter_period        = "  ";
	}
	else
	{
			$select_filter_period        = "and g.id=".$filter_period." ";
	}


//	if (isset($year_from)) { $year_from_where="and e.year>=".$year_from;} else { $year_from_where="";}
//	if (isset($year_to)) { $year_to_where="and e.year<=".$year_to;} else { $year_to_where="";}

	if ((isset($year_from)) && ($zeit_from>'0')) 
	{ 
		if ($zeit_from>'0') { $zeit_from_where="and (((g.id>=$zeit_from";}
		$year_from_where=$zeit_from_where." and e.year=".$year_from.")";
	} else { $year_from_where="";}
	if ((isset($year_to)) && ($zeit_to>'0'))
	{ 
		if ($zeit_to>'0') { $zeit_to_where=" or (g.id<=$zeit_to";}
		$year_to_where=$zeit_to_where." and e.year=".$year_to."))";
	} else { $year_to_where="";}
	
	if (isset($year_from) || (isset($year_to)))
	{
		$year_period = " or (e.year>$year_from and e.year<$year_to))";
	}

	if (($year_from==$year_to) && ($zeit_from==$zeit_to))
	{
		$year_from_where="and (g.id=$zeit_from and e.year=".$year_from.")";
		$year_to_where="";
		$year_period = "";		
	}
	
	if ($ort=="") { $ort_where="";} else {  $ort_where="and a.city='".$ort."'";}
	if ($plz_=="") { $plz_where="";} else {  $plz_where="and a.zip='".$plz_."'";}

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
			if ($felder[$aa]=="concat(i.lastname,',',i.firstname) as Campleiter")
			{
				$felder_from=", employees i ";
				$felder_where=" and i.id=e.campleiter ";
			}
			if (($felder[$aa]=="(j.sel_verpflegung>0) as Verpflegung") || ($felder[$aa]=="concat((j.sel_flughafen_hin>0),'/',(j.sel_flughafen_ret>0),'/',(j.sel_flughafen_hin_minor>0),'/',(j.sel_flughafen_ret_minor>0)) as Transfers"))
			{
				$felder_from_1=", payments_opt_camps j ";
				$felder_where_1=" and h.rechnung_id=j.rechnung_id ";
			}
			
		}
	}

	if (($darstellung=="each_child") || ($darstellung=="")) {
		$group_by="a.id";
	}
	elseif ($darstellung=="no")
	{	
		$group_by="f.id,a.id";	
	}
	
	if ($darstellung=="history") {
		$group_by="e.id,a.id";
	}
	
	if ($birthdate_field=="") { $birthdate_field_where="";} else {$birthdate_field_where=" and a.birthdate".$birthdate_sign."CONVERT('".$birthdate_field."',DATE) ";}
	
	if (($time_en=="on") && (!$time_sql)) { $felder_select.=", ".$time; $time_sql=1;}

	if ($sort1=="Name")
	{
		$sort1="a.lastname $order,a.firstname";
	} else $sort1=$sort1." ".$order;
	if ($sort2=="Name")
	{
		$sort2="a.lastname $order,a.firstname";
	} else $sort2=$sort2." ".$order;
	if ($sort3=="Name")
	{
		$sort3="a.lastname $order,a.firstname";
	} else $sort3=$sort3." ".$order;

	$xxx="select e.id,a.id,a.lastname as Nachname,a.firstname as Vorname,a.zip as PLZ, a.city as Ort, a.address as Adresse,b.name as Schule,f.name as Sportart,e.year as Jahr ".$felder_select." ";

	$xxx.="  from clients a, institutions b, courses e, products f, timeperiods g,payments h".$felder_from.$felder_from_1;

	$xxx.=" where e.status='Aktiv' and a.status!='Entfernt' and h.status!='Entfernt' and h.clients_id=a.id and h.courses_id=e.id and e.timeperiods_id=g.id and e.products_id=f.id and b.id=a.school_id $inst_select $select_from $prod_select $plz_where $ort_where $year_from_where $year_to_where $year_period  $select_filter_period $birthdate_field_where $felder_where $felder_where_1";
	$xxx.=" group by $group_by";

	$xxx.=" order by $sort1, $sort2, $sort3 $order";

	$query_text = $xxx;



	$rs=getrs($xxx,$sql_view,$print_debug,$print_error);
	echo mysql_error();

	print($xxx);

	$num=$rs->num_rows;

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
				<b>Nachname</b>
			</td>
			<td align=center>
				<b>Vorname</b>
			</td>
			<td align=center>
				<b>PLZ</b>
			</td>
			<td align=center>
				<b>Ort</b>
			</td>
			<td align=center>
				<b>Adresse</b>
			</td>
			<td align=center>
				<b>Schule</b>
			</td>
			<td align=center>
				<b>Sportart</b>
			</td>
			<td align=center>
				<b>Jahr</b>
			</td>";

	for($aa=0;$aa<sizeof($felder);$aa++)

    {
		$fieldname = explode(" ",$felder[$aa]);
		if ($fieldname[2]=="Transfers") $fieldname[2]="Transfers<br>(hin/retour/minors)";
		$html_output.="
			<td align=center>
				<b>$fieldname[2]</b>
			</td>";
	}
	if ($darstellung=="history") 
	{
		$html_output.="<td><b>Einheiten</b></td>";
	}
	$html_output.="</tr>";

	$nr=0;

         while($ergebnis=$rs->fetch_row())

         {
			$nr++;

			$html_output.="<tr>";

			$html_output.="

			<td align=left>$nr</td>";


				if ($old_id==$ergebnis[1])
				{ $html_output.="
				<td>&nbsp;</td>
				<td align=center>&nbsp;</td><td align=center>&nbsp;</td>"; }
				else
				{ $html_output.="
				<td align=center><a href='../client_form.php?id=$ergebnis[1]' target=_blank>$ergebnis[1]</a></td>
				<td align=center>$ergebnis[2]</td><td align=center>$ergebnis[3]</td>"; } 

			for($aa=4;$aa<sizeof($ergebnis);$aa++)

		    {

				if ($ergebnis[$aa]) { $html_output.="<td align=center>$ergebnis[$aa]</td>"; } 
				else 
				{ $html_output.="<td align=center>&nbsp;</td>"; }
			}
			if ($darstellung=="history") 
			{
				$sql_count_times="select count(c.id) from courses a, clients b, coursetimes c, coursetimes_clients d where a.id=$ergebnis[0] and b.id=$ergebnis[1] and c.courses_id=a.id and d.coursetimes_id=c.id and d.clients_id=b.id and d.value='on'";
				$rs_count_times=getrs($sql_count_times,$print_debug,$print_error);
				$count_times=$rs_count_times -> fetch_row();			
				$html_output.="<td><a href='../kursblatt_form.php?id=$ergebnis[0]' target=_blank>$count_times[0]</a></td>";
			}
			$html_output.="</tr>";
			
			$old_id = $ergebnis[1];
			$old_lastname=$ergebnis[2];
			$old_firstname=$ergebnis[3];
         }



return $html_output;

}

// Body of this php file

if (($nb==0) && ($suchen_x>0))
{
	institutions_report($status,$prod,$inst,$year_from,$zeit_from,$year_to,$zeit_to,$plz_,$ort,$felder,$sort1,$sort2,$sort3,$order,$darstellung,$birthdate_field,$birthdate_sign,$sql_view,$filter_period);
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
<SPAN class="headline">Kundenabfrage</SPAN><br>
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
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Produkte</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <SELECT NAME='prod[]' multiple>
<? if (!(isset($prod))) {$prod[0]="-1";} ?>
   <OPTION VALUE=-1 <? for ($i=0;$i<=sizeof($prod);$i++) { if ($prod[$i]=="-1") echo " selected";} ?>>Alle</OPTION>
   <?
   $rs_prod=getrs("select id,name from products where status='Aktiv' order by name asc",$print_debug,$print_error);
   while(LIST($id,$name)=$rs_prod -> fetch_row())
   {?>
         <OPTION VALUE="<? echo($id) ?>" <? for ($i=0;$i<=sizeof($prod);$i++) { if ($prod[$i]==$id) echo " selected";} ?>><?echo($name)?></OPTION>
<?  }
           ?>

   </SELECT>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Filterung nach Zeitperiode</TD></TR></TABLE></TD>

   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>

   <select name='filter_period'>
         <OPTION VALUE="0" <? if ($filter_period==0) echo " selected"; ?>>Alle</OPTION>
   <?
   $rs_time=getrs("select id,name from timeperiods where status='Aktiv' order by id asc",$print_debug,$print_error);
   while(LIST($t_id,$t_name)=$rs_time -> fetch_row())
   {?>
         <OPTION VALUE="<? echo($t_id) ?>" <? if ($filter_period==$t_id) echo " selected"; ?>><?echo($t_name)?></OPTION>
<?  }
           ?>

   </SELECT>
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
   $rs_inst=getrs("select id,name from institutions where status='Aktiv' order by name asc",$print_debug,$print_error);
   while(LIST($i_id,$i_name)=$rs_inst -> fetch_row())
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

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>ODER <br>Postleitzahl</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <input type="text" size=50 NAME=plz_ value="<? print($plz_) ?>">
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>ODER <br>Ort</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <input type="text" size=50 NAME=ort value="<? print($ort) ?>">
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<!--
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Status</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
 <? if (!(isset($status))) {$status[0]=3;$status[1]=1;} ?>
   <SELECT NAME=status[] multiple>
   <OPTION VALUE=1 <? for ($i=0;$i<=sizeof($status);$i++) { if ($status[$i]=="1") echo " selected";} ?>>Aktiv</OPTION>
   <OPTION VALUE=0 <? for ($i=0;$i<=sizeof($status);$i++) { if ($status[$i]=="0") echo " selected";} ?>>Inaktiv</OPTION>
   </SELECT>
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
-->
<input type="hidden" NAME=status value= "Aktiv">

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>von Jahr</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
 <? if (!(isset($year_from))) {$year_from=(date("Y"))-1;} ?>
   <input type="text" size=50 NAME=year_from value= "<? print($year_from) ?>">
			<select name="zeit_from" class="input_text">
			<?
			$rs_time_from=getrs("select id,name from timeperiods where status='Aktiv'",$print_debug,$print_error);
			While ($rs_time_from>0 && List($id,$name) = $rs_time_from -> fetch_row())
			{ 
				if ($id==$zeit_from) 
				{ ?>
					<option class="input_text" value=<?print($id)?> selected><?print($name)?></option>
			<?	} else {?>
					<option class="input_text" value=<?print($id)?>><?print($name)?></option>
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
			<?
			$rs_time_to=getrs("select id,name from timeperiods where status='Aktiv'",$print_debug,$print_error);
			While ($rs_time_to>0 && List($id,$name) = $rs_time_to -> fetch_row())
			{ 
				if ($id==$zeit_to) 
				{ ?>
					<option class="input_text" value=<?print($id)?> selected><?print($name)?></option>
			<?	} else {?>
					<option class="input_text" value=<?print($id)?>><?print($name)?></option>
			<?	} ?>
		<?	} ?>
			</select>

   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Geburtstagsfilter</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <SELECT NAME=birthdate_sign>
   <OPTION VALUE="<=" <? if ($birthdate_sign=="<=") echo " selected"; ?>><=</OPTION>
   <OPTION VALUE=">=" <? if ($birthdate_sign==">=") echo " selected"; ?>>>=</OPTION>
   <OPTION VALUE="=" <? if ($birthdate_sign=="=") echo " selected"; ?>>=</OPTION>
   </SELECT>
   <input type="text" size=50 NAME=birthdate_field value="<? print($birthdate_field) ?>"><br>
   <b></b>Hinweis:</b><br>
   <= 2008-01-01 bedeutet &auml;lter als der 01.01.2008<br>
   >= 2008-01-01 bedeutet j&uuml;nger als der 01.01.2008
   </TD></TR></TABLE></TD>
</TR>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
<? if ($nb==1) $darstellung="no";?>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Darstellung</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
   <input type="radio" name="darstellung" value="no" <?if ($darstellung=="no") {echo "checked";}?>>Gruppierung nach Sportart (mehrere Sportarten pro Kind)<br>
   <input type="radio" name="darstellung" value="each_child" <?if ($darstellung=="each_child") {echo "checked";}?>>Jedes Kind nur einmal anzeigen (Briefaktion)<br>
   <input type="radio" name="darstellung" value="history" <?if ($darstellung=="history") {echo "checked";}?>>Historie der Kunden anzeigen
   </TD></TR></TABLE></TD>
</TR>

<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>

   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>zus&auml;tzliche <br>Auswahlfelder</TD></TR></TABLE></TD>

   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
	<font color=red>Hinweis: </font>Zuerst die zus&auml;tzlichen Felder ausw&auml;hlen und dann ausserhalb dem Feld einmal klicken! <br>
	Nur so werden die zusätzlichen Felder &uuml;bernommen!<br>
   <select name='felder[]' multiple size=10 onblur="javascript:loadForm()">
<?
      for ($i=0;$i<=sizeof($felder);$i++)
      {
          if ($felder[$i]=="keine") { $sel_id= "selected";}
          if ($felder[$i]=="a.phone1 as Tel1") { $sel_tel1="selected"; }
          if ($felder[$i]=="a.phone2 as Tel2") { $sel_tel2="selected"; }
          if ($felder[$i]=="a.fax as Fax") { $sel_fax="selected"; }
          if ($felder[$i]=="a.email as Mail") { $sel_mail="selected"; }
          if ($felder[$i]=="a.birthdate as Geburtstag") { $sel_geburtstag="selected"; }
          if ($felder[$i]=="a.sex as Geschlecht") { $sel_geschlecht="selected"; }
          if ($felder[$i]=="e.info as Infos") { $sel_info="selected"; }
          if ($felder[$i]=="g.name as Zeitperiode") { $sel_zeit="selected"; }
          if ($felder[$i]=="b.zip as SchulPLZ") { $sel_plz="selected"; }
          if ($felder[$i]=="count(a.id) as Anzahl") { $sel_count="selected"; }
          if ($felder[$i]=="concat(i.lastname,',',i.firstname) as Campleiter") { $sel_campleiter="selected"; }
          if ($felder[$i]=="(j.sel_verpflegung>0) as Verpflegung") { $sel_verpflegung="selected"; }
          if ($felder[$i]=="concat((j.sel_flughafen_hin>0),'/',(j.sel_flughafen_ret>0),'/',(j.sel_flughafen_hin_minor>0),'/',(j.sel_flughafen_ret_minor>0)) as Transfers") { $sel_transfer="selected"; }

		  }

?>
   	<option value="keine" <?echo $sel_id?>> </option>

   	<option value="a.phone1 as Tel1" <?echo $sel_tel1?>>Tel1</option>

   	<option value="a.phone2 as Tel2" <?echo $sel_tel2?>>Tel2</option>

   	<option value="a.fax as Fax" <?echo $sel_fax?>>Fax</option>

   	<option value="a.email as Mail" <?echo $sel_mail?>>Mail</option>

   	<option value="a.birthdate as Geburtstag" <?echo $sel_geburtstag?>>Geburtstag</option>

   	<option value="a.sex as Geschlecht" <?echo $sel_geschlecht?>>Geschlecht</option>

   	<option value="e.info as Infos" <?echo $sel_info?>>Infos</option>

   	<option value="b.zip as SchulPLZ" <?echo $sel_plz?>>Schul-PLZ</option>

   	<option value="g.name as Zeitperiode" <?echo $sel_zeit?>>Zeitperiode</option>
   	<option value="count(a.id) as Anzahl" <?echo $sel_count?>>Anzahl der Kurse</option>
   	<option value="concat(i.lastname,',',i.firstname) as Campleiter" <?echo $sel_campleiter?>>Campleiter</option>
   	<option value="(j.sel_verpflegung>0) as Verpflegung" <?echo $sel_verpflegung?>>Verpflegung</option>
   	<option value="concat((j.sel_flughafen_hin>0),'/',(j.sel_flughafen_ret>0),'/',(j.sel_flughafen_hin_minor>0),'/',(j.sel_flughafen_ret_minor>0)) as Transfers" <?echo $sel_transfer?>>Transfers</option>

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
				<option <?if ($sort1=="Name") echo "selected";?> value="Name">Name</option>
				<option <?if ($sort1=="PLZ") echo "selected";?> value="PLZ">PLZ</option>
				<option <?if ($sort1=="Ort") echo "selected";?> value="Ort">Ort</option>
				<option <?if ($sort1=="Adresse") echo "selected";?> value="Adresse">Adresse</option>
				<option <?if ($sort1=="Schule") echo "selected";?> value="Schule">Schule</option>
				<option <?if ($sort1=="Sportart") echo "selected";?> value="Sportart">Sportart</option>
				<option <?if ($sort1=="Jahr") echo "selected";?> value="Jahr">Jahr</option>

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
				<option <?if ($sort2=="Name") echo "selected";?> value="Name">Name</option>
				<option <?if ($sort2=="PLZ") echo "selected";?> value="PLZ">PLZ</option>
				<option <?if ($sort2=="Ort") echo "selected";?> value="Ort">Ort</option>
				<option <?if ($sort2=="Adresse") echo "selected";?> value="Adresse">Adresse</option>
				<option <?if ($sort2=="Schule") echo "selected";?> value="Schule">Schule</option>
				<option <?if ($sort2=="Sportart") echo "selected";?> value="Sportart">Sportart</option>
				<option <?if ($sort2=="Jahr") echo "selected";?> value="Jahr">Jahr</option>
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
				<option <?if ($sort3=="Name") echo "selected";?> value="Name">Name</option>
				<option <?if ($sort3=="PLZ") echo "selected";?> value="PLZ">PLZ</option>
				<option <?if ($sort3=="Ort") echo "selected";?> value="Ort">Ort</option>
				<option <?if ($sort3=="Adresse") echo "selected";?> value="Adresse">Adresse</option>
				<option <?if ($sort3=="Schule") echo "selected";?> value="Schule">Schule</option>
				<option <?if ($sort3=="Sportart") echo "selected";?> value="Sportart">Sportart</option>
				<option <?if ($sort3=="Jahr") echo "selected";?> value="Jahr">Jahr</option>
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
						<input type=hidden name=sql_view value="<?print($sql_view)?>">
						<input type=hidden name=search value="1">
		
						<input type=image name=suchen src="../../images/buttons/suchen.gif" BORDER=0>
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

	
<?
$sql_view=1;


// Gibt alle Kunden ohne Kurszuweisung zur￿n
function ClientWithoutRelation($inst,$year)
{

	global $html_output;

	global $query_text;
	
		for($aa=0;$aa<sizeof($inst);$aa++)
	    {
				if($aa<(sizeof($inst)-1))
					$sql_inst.="'".$inst[$aa]."'".",";
				elseif($aa==(sizeof($inst)-1))
				    $sql_inst.="'".$inst[$aa]."'";
		}
	
	if ($inst[0]!='-1') $institution_sql = "a.school_id in (".$sql_inst.") and ";

	$sql_source="select g.id,g.firstname,g.lastname,a.name,

                     left(c.date,4),

					 e.name,

					 concat(left(f.name,7),'...'),

					 f.city,

					 d.price

   from products a,

		coursetimes_clients b,

        coursetimes c,

		courses d,

		timeperiods e,

		institutions f,

		clients g

	where g.id=b.clients_id and

		  b.coursetimes_id=c.id and

		  c.courses_id=d.id and

		  d.products_id=a.id and

		  d.timeperiods_id=e.id and

		  d.institutions_id=f.id and

		  d.status in ('Aktiv', 'Inaktiv') and

          a.status in ('Aktiv', 'Inaktiv') and

		  f.status in ('Aktiv', 'Inaktiv') and

		  d.status in ('Aktiv', 'Inaktiv') and

          left(c.date,4) in ($year) group by g.id";
	$rs=getrs($sql_source,$DEBUG,1);


$nums=$rs->num_rows;

if ($nums>0)

   {

	     $client_ids="(";


		 $i=1;

		 while(LIST($client_id)=$rs->fetch_row())

         {

             if($i<$nums)

			 $client_ids.=" $client_id, ";

			 else

			 $client_ids.=" $client_id)";

			 $i++;

         }

   }


$sql_rs = "select distinct a.id,concat(a.firstname,' ',a.lastname),
					  a.address,
					  a.zip,
					  a.city,
                      a.email,
					  a.phone1,
					  b.name

		   from clients a, institutions b
		   where $institution_sql b.id=a.school_id and a.id not in $client_ids
		   group by a.id order by a.lastname asc, a.firstname asc";
	$rs2=getrs($sql_rs,$DEBUG,1);

    $html_output="";

	$num=$rs2->num_rows;

	$html_output.="<div align=center>Anzahl der Datens￿e: $num</div>";

   $html_output.="<TABLE width=800   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1><tr><td>id</td><td height=40 align=center><b>Name</b></td><td align=center><b>Adresse</b></td><td align=center><b>Plz</b></td><td align=center><b>Ort</b></td><td align=center><b>Telefon</b></td><td align=center><b>Email</b></td><td align=center><b>Schule</b></td><td align=center><b>letzter Kurs</b></td></tr>";



   if ($num>0)

   {

        while(LIST($client_id,$client_name,$client_address,$client_zip,$client_city,$client_email,$client_phone1,$client_school)=$rs2->fetch_row())

         {
			$sql_last_course_rs = "select a.id, b.name, a.year,c.name,d.name,e.name,f.name
					   from courses a, products b, timeperiods c, kursinfo d, institutions e, institutions f, payments g
					   where g.clients_id=$client_id and b.id=a.products_id and c.id=a.timeperiods_id and d.id=a.type and a.locations_id=f.id and a.institutions_id=e.id
					    and a.status!='Entfernt' and g.courses_id=a.id group by a.id order by a.year desc, a.timeperiods_id desc";
			
			 $rs_last_course=getrs($sql_last_course_rs,$DEBUG,1);
			 LIST($course_id,$product,$year,$timeperiod,$kursinfo,$location,$institution)=$rs_last_course->fetch_row();

			 $html_output.="<tr height=15>
   			 <td align=right>&nbsp;$client_id&nbsp;</td>

             <td><a href=../client_form.php?id=$client_id target=_blank>&nbsp; $client_name</a></td>

			 <td align=right>&nbsp;$client_address&nbsp;</td>

			 <td align=right>&nbsp;$client_zip&nbsp;</td>

			 <td align=right>&nbsp;$client_city &nbsp;</td>

			 <td align=right>&nbsp;$client_phone1&nbsp;</td>

			 <td align=right>&nbsp;$client_email&nbsp;</td>
			 <td align=right>&nbsp;$client_school&nbsp;</td>
			 <td align=right>&nbsp;$product-$year- $timeperiod-$kursinfo&nbsp;</td></tr>";

         }

   }

		$html_output.="</TABLE>";



return $html_output;

}



function ClientWithRelation($year_from,$year_to,$zeit,$school_id)

{

	global $html_output;

	global $query_text;

    if ($zeit>0) {
                      $tp_select = ", a.timeperiods_id";
                      $tp_from = "timeperiods f, ";
	              $tp = "a.timeperiods_id=$zeit and ";
                      $tp.="a.timeperiods_id = f.id and ";
			
                 }
                 else {
                      $tp_select = ", f.name";
                      $tp_from = "timeperiods f, ";
                      $tp="a.timeperiods_id = f.id and";
                      }
    if ($school_id>0) {$school="b.school_id=$school_id and ";}


    $rs = getrs("select
                   a.id as Cid,
                   b.id,
                   CONCAT(b.lastname,' ',b.firstname),
                   b.address,
                   b.zip,
                   b.city,
                   b.phone1,
                   e.name,
	 	           g.name,
  			       b.birthdate,
			       a.year,
			       COUNT(d.value)
                   $tp_select
             from courses a,
                  clients b,
                  coursetimes c,
                  coursetimes_clients d,
                  $tp_from
                  products e,
		  institutions g,
		  payments p
             where
                  a.year>=$year_from and a.year<=$year_to and
                  $tp
		  		  $school
                  a.id=c.courses_id and
                  d.clients_id=b.id and
                  d.coursetimes_id=c.id and
                  e.id=a.products_id and
                  a.status in ('Aktiv', 'Inaktiv') and
                  b.status in ('Aktiv', 'Inaktiv') and
			      b.school_id = g.id
				  AND p.status IN ('E', 'A', 'F')
				  AND p.courses_id = a.id
				  AND p.clients_id = b.id
				  group by b.id, a.id,f.name, a.year order by b.lastname asc, b.firstname asc",1);

    $html_output="";

	$num=@mysql_num_rows($rs);

	$html_output="<div align=center>Anzahl der Datens￿e: $num</div> <br> $ciiis";

    $html_output.="<TABLE width=700   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1><tr><td height=40 align=center><b>Kurs ID</b></td><td height=40 align=center><b>Name</b></td><td align=center><b>Adresse</b></td><td align=center><b>Plz</b></td><td align=center><b>Ort</b></td><td align=center><b>Schule</b></td><td align=center><b>Telefon</b></td><td align=center><b>Geburtsdatum</b></td><td align=center colspan=4><b>Kurs</b></td><td align=center><b>Wie oft?</b></td></tr>";



   if ($num>0)

   {

        while(LIST($payment_id,$client_id,$client_name,$client_address,$client_zip,$client_city,$client_phone1,$product,$client_school,$birthday,$year,$counts,$timep)=@mysql_fetch_row($rs))

         {
             if ($zeit>0) {$view="<td align=right>&nbsp;$product&nbsp;</td>";}
             else {$view="<td align=right>&nbsp;$product&nbsp;</td><td align=right>&nbsp;$timep&nbsp;</td>";}

			 $html_output.="<tr height=15>
             <td>&nbsp; <a href=kurs_form.php?id=$payment_id target=_blank>KF</a> <br>
		         &nbsp; <a href=kursblatt_form.php?id=$payment_id target=_blank>KB</a> </td>

             <td>&nbsp; <a href=client_form.php?id=$client_id target=_blank>$client_name</a></td>

			 <td align=right>&nbsp;$client_address&nbsp;</td>

			 <td align=right>&nbsp;$client_zip&nbsp;</td>

			 <td align=right>&nbsp;$client_city &nbsp;</td>
			 <td align=right>&nbsp;$client_school &nbsp;</td>

			 <td align=right>&nbsp;$client_phone1&nbsp;</td>
			 <td align=right>&nbsp;$birthday&nbsp;</td>
			 <td></td>

			 $view
			 <td align=right>&nbsp;$year&nbsp;</td>
			 <td align=right>&nbsp;$counts&nbsp;</td>
			 </tr>
			";

         }

   }

		$html_output.="</TABLE>";



return $html_output;

}

function LostClients($year_from,$year_to,$zeit,$school_id)

{

	global $html_output;

	global $query_text;

    if ($zeit>0) {
	              $tp = "a.timeperiods_id=$zeit and ";
                      $tp_select = ", a.timeperiods_id";
                      $tp_from = "timeperiods f, ";
                      $tp.= "a.timeperiods_id = f.id and ";

                 }
                 else {
                      $tp_select = ", f.name";
                      $tp_from = "timeperiods f, ";
                      $tp="a.timeperiods_id = f.id and";
                      }
    if ($school_id>0) {$school="b.school_id=$school_id and ";}

    $rs_now = getrs("select
                   a.id as Cid,
                   b.id,
                   CONCAT(b.lastname,' ',b.firstname),
                   b.address,
                   b.zip,
                   b.city,
                   b.phone1,
                   e.name,
	 	       g.name,
			 b.birthdate,
			 a.year
                   $tp_select
             from courses a,
                  clients b,
                  coursetimes c,
                  coursetimes_clients d,
                  $tp_from
                  products e,
		  institutions g
             where
                  a.year=$year_to and
                  $tp
			$school
                  a.id=c.courses_id and
                  d.clients_id=b.id and
                  d.coursetimes_id=c.id and
                  e.id=a.products_id and
                  a.status in ('Aktiv', 'Inaktiv') and
                  b.status in ('Aktiv', 'Inaktiv') and
			b.school_id = g.id
             group by b.id, f.name, a.year order by b.lastname asc, b.firstname asc",0);

    	$num=@mysql_num_rows($rs_now);
		 $i=1;

		 while(LIST($courid,$client)=mysql_fetch_row($rs_now))

         {

             if($i<$num)

			 $ciiis.=" $client, ";

			 else

			 $ciiis.=" $client)";

			 $i++;

         }

    $rs_last = getrs("select
                   a.id as Cid,
                   b.id,
                   CONCAT(b.lastname,' ',b.firstname),
                   b.address,
                   b.zip,
                   b.city,
                   b.phone1,
                   e.name,
	 	       g.name,
			 b.birthdate,
			 a.year
                   $tp_select
             from courses a,
                  clients b,
                  coursetimes c,
                  coursetimes_clients d,
                  $tp_from
                  products e,
		  institutions g
             where
                  a.year>=$year_from and a.year<$year_to and
                  $tp
			$school
                  a.id=c.courses_id and
                  d.clients_id=b.id and
                  d.coursetimes_id=c.id and
                  e.id=a.products_id and
                  a.status in ('Aktiv', 'Inaktiv') and
                  b.status in ('Aktiv', 'Inaktiv') and
        		  b.school_id = g.id and b.id not in ($ciiis
             group by b.id, f.name, a.year order by b.lastname asc, b.firstname asc",0);


    $html_output="";

	$num=@mysql_num_rows($rs_last);

	$html_output="<div align=center>Anzahl der Datens￿e: $num</div>";

    $html_output.="<TABLE width=700   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1><tr><td height=40 align=center><b>Kurs ID</b></td><td height=40 align=center><b>Name</b></td><td align=center><b>Adresse</b></td><td align=center><b>Plz</b></td><td align=center><b>Ort</b></td><td align=center><b>Schule</b></td><td align=center><b>Telefon</b></td><td align=center><b>Geburtsdatum</b></td><td align=center colspan=4><b>Kurs</b></td></tr>";



   if ($num>0)

   {

        while(LIST($payment_id,$client_id,$client_name,$client_address,$client_zip,$client_city,$client_phone1,$product,$client_school,$birthday,$year,$timep)=@mysql_fetch_row($rs_last))

         {
             if ($zeit>0) {$view="<td align=right>&nbsp;$product&nbsp;</td>";}
             else {$view="<td align=right>&nbsp;$product&nbsp;</td><td align=right>&nbsp;$timep&nbsp;</td>";}

			 $html_output.="<tr height=15>
             <td><a href=kurs_form.php?id=$payment_id target=_blank>&nbsp; $payment_id</a></td>

             <td><a href=client_form.php?id=$client_id target=_blank>&nbsp; $client_name</a></td>

			 <td align=right>&nbsp;$client_address&nbsp;</td>

			 <td align=right>&nbsp;$client_zip&nbsp;</td>

			 <td align=right>&nbsp;$client_city &nbsp;</td>
			 <td align=right>&nbsp;$client_school &nbsp;</td>

			 <td align=right>&nbsp;$client_phone1&nbsp;</td>
			 <td align=right>&nbsp;$birthday&nbsp;</td>
			 <td></td>

			 $view
			 <td align=right>&nbsp;$year&nbsp;</td>
			 </tr>
			";

         }

   }

		$html_output.="</TABLE>";



return $html_output;

}


// Berechnet Tagessaldo



function DaySaldo($selection_date)
{
	global $html_output;
	global $query_text;

	$rs=getrs("select a.name, concat(c.firstname,' ', c.lastname),d.amount,d.status

			   from products a,

				    courses  b,

				    clients  c,

				    payments d

			   where d.billdate    = '$selection_date' and

				     d.courses_id  = b.id and

				     b.products_id = a.id and

				     d.clients_id  = c.id",0,1);

	$num=$rs->num_rows;

	$html_output="<div align=center>Anzahl der Datens&auml;tze: $num</div>";

	$html_output.="<TABLE width=700   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1><tr ><td height=40 align=center><b>Was</b></td><td align=center><b><font color=green>Eingang</font></b></td><td align=center><b><font color=red align=center>Ausgang</font></b></td><td align=center><b>Zweck</b></td></tr>";

	 if ($num>0)

     {
		 while(LIST($product_name,$client_name,$amount,$status)=$rs->fetch_row())

        {

             if     ($status=="E")

			 {

				  $payment=1;

				  $payment_name="Eingang";

				  $was     = $product_name.'-'.$client_name;

				  $eingang = "&euro; ".$amount;

				  $ausgang = "";

				  $zweck   = "";

				  $summaryplus+=$amount;

			 }

			 elseif ($status=="F")

			 {

				  $payment=2;

				  $payment_name="Forderung";

				  $was     = "";

				  $eingang = "";

				  $ausgang = "&euro; ".$amount;

				  $zweck   = $product_name.'-'.$client_name;

				  $summaryminus+=$amount;

			 }

			 $html_output.="<tr height=15><td>&nbsp; $was&nbsp;</td><td align=right>&nbsp;$eingang&nbsp;</td><td align=right>&nbsp;$ausgang&nbsp;</td><td>&nbsp; $zweck&nbsp;</td></tr>";

        }

		$html_output.="<tr height=30>

		<td><b>&nbsp; Summe Eing&auml;nge/Ausg&auml;nge</b></td>

		<td align=right><b><font color=green>&nbsp; &euro;  $summaryplus</font></b></td>

		<td align=right><b><font color=red>&nbsp; &euro; $summaryminus</font></b></td>

		<td>&nbsp;</td></tr>";

		$dat=explode("-",$selection_date);

		$tagessaldo=$summaryplus-$summaryminus;

		if     ($tagessaldo>0) {$fontcolor="green";}

		elseif ($tagessaldo<0) {$fontcolor="red";}

		else                   {$fontcolor="black";}

		$html_output.="<tr height=60>

		<td><b>&nbsp; Tagessaldo vom $dat[2].$dat[1].$dat[0]</b></td>

		<td align=center colspan=2><b><font color=$fontcolor>&nbsp; &euro;  $tagessaldo</font></b></td>

		<td>&nbsp;</td></tr>";

		$html_output.="</TABLE>";

     }
}



// Gibt Auswahlfelder f￿icht Zahlungen zur￿n
function CoursesWithSpecialnumber($sql_view,$paymenttype,$course,$selection_date,$selection_date_to,$course_year_from,$course_year_to,$timeperiod_from,$timeperiod_to,$register_date_from,$register_date_to,$date_choose,$date_legal)
{
	global $html_output;
	global $query_text;
	
	$sql_view=1;

	if    ($paymenttype=="Alle" || $paymenttype==""){$paymenttype_select="";}
	elseif($paymenttype<>"")    {$paymenttype_select="a.status='".$paymenttype."' and ";}

	if ($course==0)
	{
		$course_select=" 1=1 and ";
	}
	else           
	{	
		$course_select=" a.courses_id=".$course." and ";
	}

	switch($date_choose)
	{
		case "1": //Anmeldedatum ausgew￿hlt
		{
			$date_select=" ((a.reg_date>= '".$register_date_from."'";
			$date_select.=" and a.reg_date<= '".$register_date_to."')";
			if (($paymenttype=="F" || $paymenttype=="Alle") && ($date_legal!="ein"))
			{
				$date_select.=" or a.billdate='0000-00-00'";
			}
			$date_select .=") ";
			break;
		}
		case "2": //Zahlungseingangsdatum ausgew￿hlt
		{
			$date_select=" ((a.billdate>= '".$selection_date."'";
			$date_select.=" and a.billdate<= '".$selection_date_to."')";
			if (($paymenttype=="F" || $paymenttype=="Alle") && ($date_legal!="ein"))
			{
				$date_select.=" or a.billdate='0000-00-00'";
			}
			$date_select .=") ";
			break;
		}
		case "3":
		{
			$date_select = " (d.year='$course_year_from' and d.timeperiods_id='$timeperiod_from') ";
			break;
		}
	}

	$rs=getrs("select 
		a.rechnung_id,
        d.id,
		sum(a.amount),
		a.reg_date,
		a.billdate,
	    (a.rab_earlybook+a.rab_lastminute+a.rab_stammkunde+a.rab_geschwister+a.rab_kombi1+a.rab_kombi2+a.rab_verlaengerung+a.rab_halbtag+a.rab_firmen+a.rab_sonder), 
		(i.sel_verpflegung+i.sel_nachmittag+i.sel_lernmodul+i.sel_modul3+i.sel_modul4+i.sel_flughafen_hin+i.sel_flughafen_ret+i.sel_flughafen_hin_minor+i.sel_flughafen_ret_minor+i.sel_bahnhof_hin+i.sel_bahnhof_ret+i.sel_zertifikat+i.sel_tennis),
		b.lastname,
		b.firstname,
		a.status,
		a.mahnung_sent1,
		a.mahnung_sent2,
		a.mahnung_sent3,
		a.mahnung_comment,
		c.name as Produkt,
		d.year as Jahr,
		f.name as Zeitperiode,
		concat(left(e.name,20),'...'),
		concat(left(g.name,20),'...'),
		d.type,
		e.name,
		b.zip,
		b.address,
		b.city,
		b.phone1,
		b.phone2,
		b.email,
		d.price,
		h.name,
		d.id,
		b.id,
		Trainer.lastname
		FROM 
		(((payments a,
		clients b,
		products c,
		courses d,
		institutions e,
		timeperiods f,
		institutions g,
		kursinfo h)
		LEFT JOIN employees Trainer ON ( d.standard_employee = Trainer.id ))
		LEFT JOIN payments_opt_camps i ON (i.rechnung_id=a.rechnung_id))
		WHERE
		(a.status!='Entfernt' and a.status!='Storno') and b.status!='Entfernt' and d.status!='Entfernt' and h.id=d.type and
		a.clients_id=b.id and a.courses_id=d.id and d.products_id=c.id and d.timeperiods_id=f.id and institutions_id=g.id and locations_id=e.id and $paymenttype_select $course_select $date_select 
		group by a.rechnung_id,b.id, d.id order by a.billdate asc,b.lastname asc",$sql_view,1);
	$sum=$rs->num_rows;
	echo mysql_error();
	$html_output="<SPAN>Anzahl der Datensaetze: $sum</SPAN>";

	$html_output.="<TABLE width=100%   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1> 
					<tr>
						<td>#</td>
						<td><b>Besuchte<br>Einheiten<b></td>
						<td align=center><b>Kursort</b></td>
						<td align=center><b>Produkt</b></td>
						<td align=center><b>Zeit</b></td>
						<td height=40 align=center><b>Nachname</b></td>
						<td height=40 align=center><b>Vorname</b></td>
						<td align=center><b>Adresse</b></td>
						<td align=center><b>Ort</b></td>
						<td align=center><b>Tel.1</b></td>
						<td align=center><b>Tel.2</b></td>
						<td align=center><b>Email</b></td>
						<td align=center><b>Datum</b></td>
						<td align=center><b>Originalpreis</b></td>
						<td align=center><b>Kundenpreis</b></td>
						<td align=center><b><></b></td>
						<td align=center><b>Betrag</b></td>
						<td align=center><b>Skontowert</b></td>
						<td align=center><b>Optionswert</b></td>
						<td align=center><b>Typ</b></td>
						<td align=center><b>Mahnung verschickt?</b></td>
						<td align=center><b>Kommentar</b></td>
						<td align=center><b>Kursleiter</b></td>
					</tr>";

	if ($sum>0)
   {
   		$num=0;
         while(LIST($p_id,$course_id,$p_amount,$p_reg_date,$p_billdate,$p_scontoamount,$p_opt_amount,$p_lastname,$p_firstname,$p_status,$p_mahnung_sent1,$p_mahnung_sent2,$p_mahnung_sent3,$p_mahnung_comment,$prod,$year,$timeperiod,$inst,$loc,$kursinfo,$location,$zip,$address,$city,$phone1,$phone2,$email,$orig_price,$kursinfo,$courseid,$k_id,$employee)=$rs->fetch_row())
         {	
			 $num++;
			 if     ($p_status=="E")
			 {$payment_name="Eingang";}
			 elseif ($p_status=="F")
			 {$payment_name="Forderung";}
			 elseif ($p_status=="Z")
			 {$payment_name="Zahlung offen";}
			 elseif ($p_status=="A")
			 {$payment_name="Ausgang";}
			 $mahnung="";
			 if ($p_mahnung_sent1>0) {$mahnung="1.M verschickt";} 
			 if ($p_mahnung_sent2>0) {$mahnung="<br>2.M verschickt";} 
			 if ($p_mahnung_sent3>0) {$mahnung="<br>RA verschickt";} 
			 switch($kursinfo)
			 {
			 	case "1":$kursinfo_text="KG";break;
			 	case "2":$kursinfo_text="VS 1./2.Kl";break;			 	
				case "3":$kursinfo_text="VS 3./4.Kl";break;
			 	case "4":$kursinfo_text="VS 1.-4.Kl";break;			 	
			 	case "5":$kursinfo_text="Krippe";break;			 	
			 	case "6":$kursinfo_text=">10 J";break;			 	
			 }

/* Abfrage bez $count_visited !!!! */

	$rs_besuche=getrs("SELECT 
	count(coursetimes_clients.id) as Besucht 
	FROM `coursetimes_clients`, `coursetimes` 
	WHERE   coursetimes_clients.coursetimes_id=coursetimes.id and 
			coursetimes_clients.value='on' and 
			coursetimes_clients.clients_id=$k_id and 
			coursetimes.courses_id=$courseid"
	,0,1);
	LIST($count_visited)=$rs_besuche->fetch_row();
		 
			 if ($count_visited>"0") 
			 { 
			 	$count_visited="<b>$count_visited</b>";
			 }
			 $html_output.="<tr height=25>
			 <td><a href='../kursblatt_form.php?id=$course_id' target=blank>$num</a></td>
			 <td align=center>$count_visited</td>
			 <td>$location&nbsp;</td>
			 <td>$prod&nbsp;</td>
			 <td>$year/$timeperiod/$kursinfo</td>
			 <td><a href='../payment_form.php?id=$p_id' target=blank>$p_lastname</a>&nbsp;</td>
			 <td><a href='../payment_form.php?id=$p_id' target=blank>$p_firstname</a>&nbsp;</td>
			 <td>$address&nbsp;</td>
			 <td>$zip-$city</td>
			 <td>$phone1&nbsp;</td>
			 <td>$phone2&nbsp;</td>			
			 <td>$email&nbsp;</td>";
			 
			 if ($date_choose==1) { $html_output.="<td align=right>$p_reg_date&nbsp;</td>"; }
			 if ($date_choose==2) { $html_output.="<td align=right>$p_billdate&nbsp;</td>"; }
			 if ($date_choose==3) { $html_output.="<td align=right>$course_year_from&nbsp;</td>"; }
			 
			 if ($p_opt_amount=="") $p_opt_amount="0.00";
			 if ($orig_price-$p_scontoamount+$p_opt_amount > $p_amount) { $farbe = "bgcolor='red'"; $compare=">"; }
			 if ($orig_price-$p_scontoamount+$p_opt_amount == $p_amount) { $farbe = "bgcolor='green'";$compare="="; }
			 if ($orig_price-$p_scontoamount+$p_opt_amount < $p_amount) { $farbe = "bgcolor='yellow'";$compare="<"; }
			 
			 $kundenpreis=$orig_price-$p_scontoamount+$p_opt_amount;
			 $html_output.="<td align=right>&euro; $orig_price&nbsp;</td>
			 <td align=right>&euro; $kundenpreis&nbsp;</td>
		     <td>$compare</td>
			 <td align=right $farbe>&euro; $p_amount&nbsp;</td>
		     <td align=right>&euro; $p_scontoamount&nbsp;</td>
			 <td>&euro; $p_opt_amount&nbsp;</td>
			 <td>$payment_name&nbsp;</td>
			 <td>$mahnung&nbsp;</td>
			 <td>$p_mahnung_comment&nbsp;</td>
			 <td>$employee&nbsp;</td>
			 </tr>";
         }
   }
	    $html_output.="</Table>";

return;
	
}




function CoursesWithSpecialnumber_DONT_USE($paymenttype,$course,$selection_date)

{

	global $html_output;

	global $query_text;



	if    ($paymenttype=="Alle" || $paymenttype==""){$paymenttype_select="";}

	elseif($paymenttype<>"")    {$paymenttype_select="and a.status='".$paymenttype."' ";}



	if ($product==-1){$prod_select=" ";}

	else           {$prod_select="and c.products_id=".$product." ";}



	if ($timeperiod==-1){$tp_select=" ";}

	else           {$tp_select="and c.timeperiods_id=".$timeperiod." ";}



	if ($year==0){$year_select=" ";}

	else           {$year_select="and c.year='".$year."' ";}



	if ($institution==-1){$inst_select=" ";}

	else           {$inst_select="and c.institutions_id=".$institution." ";}



	if ($place==-1){$place_select=" ";}

	else           {$place_select="and c.locations_id=".$place." ";}



	if ($info==0){$info_select=" ";}

	else           {$info_select="and c.info='".$info."'";}



	if ($remark==""){$remark_select=" ";}

	else           {$remark_select="and a.remarks like '".$remark."'";}

//	echo substr($selection_date,0,1);



	if ((substr($selection_date,0,1)==0)) {$date_select=" and 3=3 ";}

	else                   {$date_select=" and (a.billdate>= '".$selection_date."' or a.billdate='0000-00-00')";

	}

	if ((substr($selection_date_end,0,1)==0)) {$date_select_end=" and 4=4 ";}

	else                   {$date_select_end=" and (a.billdate<= '".$selection_date_end."')";

	}


	if ($kcode==0){$kcode_select=" ";}

	else

	{

		$kcode_select="and CONCAT(c.products_id,c.year,c.timeperiods_id,c.institutions_id,c.locations_id,'-',c.info)='$kcode' ";

	}



	for($aa=0;$aa<sizeof($felder);$aa++)

    {

//		echo $felder[$aa];

		$felder[$aa]=strtr($felder[$aa],"|","'");

		if($aa<(sizeof($felder)-1))

		$felder_select.= $felder[$aa].",";

		elseif($aa==(sizeof($felder)-1))

	    $felder_select.=$felder[$aa]." ";

		if ($felder[$aa]=="d.name as Kursort") { $ort=", institutions d "; $ort_select=" and c.locations_id=d.id ";}

	}



	$sql="select a.id,a.sconto_id,a.sconto_amount,c.price,a.amount,$felder_select

			from

		payments a,

		clients b,

		courses c

		$ort

			where

		a.clients_id=b.id and

		a.courses_id=c.id and

		a.status!='Entfernt' and

		c.status='Aktiv' and

		b.status='Aktiv'

		$paymenttype_select

		$prod_select

		$tp_select

		$year_select

		$inst_select

		$place_select

		$info_select

		$remark_select

		$ort_select

		$kcode_select ";

	if ($paymenttype!="F") $sql.=$date_select.$date_select_end;

	$sql.=" order by $sort1, $sort2, $sort3 $direction";

	$rs=getrs($sql,$sql_view);

	$num=@mysql_num_rows($rs);

	$html_output="$sql<div align=center>Anzahl Datens￿e: $num</div>";

	$html_output.="<TABLE width=600   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1>

	<tr><td><b>Nr.</b></td><td height=40 align=center><b>ID</b></td>";

	for ($i=0;$i<=4;$i++) 	    $fieldname = @mysql_fetch_field ($rs);



	for($aa=0;$aa<sizeof($felder);$aa++)

    {

	    $fieldname = @mysql_fetch_field ($rs);

		$html_output.="<td height=40 align=center><b>".$fieldname->name."</b></td>";

	}



	$html_output.="<td><b>gez. Betrag</b></td><td><b>Erm￿gung</b></td><td><b>Preis(inkl.Rabatt)</b></td><td><b>offener Betrag</b></td></tr>";

	$loop=0;

	if ($num>0)

   {

         while($ergebnis=@mysql_fetch_row($rs))

         {

			 if     (($ergebnis[1]==0) || ($ergebnis[1]==3))	{$minus=0; $price=$ergebnis[3];}

			 elseif ($ergebnis[1]==1)	{$minus=$ergebnis[2]; $price=$ergebnis[3]-$minus;}

			 elseif ($ergebnis[1]==2)	{$minus=$ergebnis[3]*($ergebnis[2]/100); $price=$ergebnis[3]-$minus;}



			 if     ($p_status=="E")

			 {$payment_name="Eingang";}

			 elseif ($p_status=="F")

			 {$payment_name="Forderung";}

			 elseif ($p_status=="Z")

			 {$payment_name="Zahlung offen";}

			 elseif ($p_status=="A")

			 {$payment_name="Ausgang";}

			$loop++;

			$html_output.="<tr height=25>";

			$html_output.="<td>$loop</td><td height=40 align=center><a target=_blank href='payment_form.php?id=".$ergebnis[0]."'>".$ergebnis[0]."</a></td>";

			for($aa=5;$aa<sizeof($ergebnis);$aa++)

    		{

				$html_output.="<td height=40 align=center>".$ergebnis[$aa]."</td>";

			}

			$open=$price-$ergebnis[4];

			$pay=number_format($ergebnis[4],2,",",".");

			$min=number_format($minus,2,",",".");

			$pr=number_format($price,2,",",".");

			$op=number_format($open,2,",",".");

			$html_output.="<td>$pay</td><td>$min</td><td>$pr</td><td>$op</td></tr>";

         }

   }

	    $html_output.="</Table>";



return;



}



function MonthlyCalculation($employee,$select_month,$select_year,$viewsoz)
{
	global $html_output;
	$rs=getrs("
		(select a.id,
	                  a.title,
					  a.firstname,
					  a.lastname,
					  a.commission,
					  a.contract_id,
					  c.date as date_shown,
					  c.time,
					  c.durance,
					  concat(e.name,'-',d.year,'-',f.name,'-',g.name,'-',h.name,'-',d.info),
					  b.hourcost,
					  (b.hourcost*c.durance)
		from employees a,
			coursetimes_employees b,
			coursetimes c,
			courses d,
			products e,
			timeperiods f,
			institutions g,
			institutions h
	    where $employee=a.id and
		     a.status in('Aktiv','Inaktiv') and
			 d.status='Aktiv' and
		     b.employees_id=a.id and
		     b.coursetimes_id=c.id and
			 d.timeperiods_id=f.id and
			 d.institutions_id =g.id and
			 d.locations_id = h.id and
		     c.courses_id=d.id and
			 d.products_id=e.id and
			 left(c.date,4)='$select_year' and
			 substring(c.date,6,2)='$select_month'
) UNION (
select a.id,
	                  a.title,
					  a.firstname,
					  a.lastname,
					  a.commission,
					  a.contract_id,
					  c.date as date_shown,
					  c.time,
					  c.durance,
					  concat(e.name,'-',d.year,'-',f.name,'-',g.name,'-',h.name,'-',d.info),
					  c.employee4_hc,
					  (c.employee4_hc*c.durance)
		from employees a,
			coursetimes c,
			courses d,
			products e,
			timeperiods f,
			institutions g,
			institutions h
	    where $employee=a.id and
		     a.status in('Aktiv','Inaktiv') and
			 d.status='Aktiv' and
		     c.employee4_id=a.id and
			 d.timeperiods_id=f.id and
			 d.institutions_id =g.id and
			 d.locations_id = h.id and
		     c.courses_id=d.id and
			 d.products_id=e.id and
			 left(c.date,4)='$select_year' and
			 substring(c.date,6,2)='$select_month'
)
ORDER BY date_shown asc			 
",0,1);


	$rs_name=getrs("select a.title,a.firstname,a.lastname,a.bank_account,a.bank_code from employees a where a.id=$employee",0,1);

	LIST($e_title,$e_firstname,$e_lastname,$kontonr,$blz)=$rs_name->fetch_row();



	switch ($select_month)

    {

        case "01": $month="J￿er";break;

		case "02": $month="Februar";break;

		case "03": $month="M￿";break;

		case "04": $month="April";break;

		case "05": $month="Mai";break;

		case "06": $month="Juni";break;

		case "07": $month="Juli";break;

		case "08": $month="August";break;

		case "09": $month="September";break;

		case "10": $month="Oktober";break;

		case "11": $month="November";break;

		case "12": $month="Dezember";break;

	}

	$html_output="";

	$html_header="

		<A HREF='javascript:window.print()'><IMG src='../../images/buttons/drucken.gif' BORDER=0></A>

		<TABLE width=650   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1>

		<tr><td height=40 align=center><font size=4><b>Monatsabrechnung</b></font></td><td align=center><b>Monat:&nbsp;&nbsp; <font size=2>$month / $select_year</font></b></td></tr></table>

		<TABLE width=650   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=0>

		<tr><td height=20 align=left>Mitarbeiter: <b>$e_lastname $e_firstname</b> </td><td align=right>BLZ/Kontonr: <b>$blz / $kontonr</b> </td></tr>

		</table>

		<TABLE width=650   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=0>

		<tr>

		<td height=40 align=left width=20%><b>Datum</b></td>

		<td align=left width=50%><b>Kurs</b></td>

		<td align=left></td>

		<td align=right width=10%><b>Dauer</b></td>

		<td align=right width=10%><b>￿/h</b></td>

		<td align=right width=10%><b>Entgelt</b></td></tr>";

		if ($rs->num_rows>0)

        {   $html_body="";

			while(LIST($employee_id,$employee_title,$employee_firstname,$employee_lastname,$employee_commission,$employee_contract,$employee_coursedate,$employee_coursetime,$employee_coursedurance,$employee_coursecode,$standart_courserate,$employee_entgeltsummary)=$rs->fetch_row())

			{

				      ////////////////////////

					 // STEUERSATZ WIRD DEFINIERT !!!!

					////////////////////////

					switch ($employee_contract)

					{

						// Arbeiter

						case "0": $steuersatz=30.7000000;break;

						// Vollzeit

						case "1": $steuersatz=30.7000000;break;

						// Freier Mitarbeiter

						case "2": $steuersatz=100.0000000;break;

						// Werkvertrag

						case "3": $steuersatz=100.0000000;break;

					}

				  $newdate=explode("-",$employee_coursedate);


				  $html_body.="<tr>

					<td align=left height=30>$newdate[2].$newdate[1].$newdate[0]</td>

					<td align=left>$employee_coursecode</td>

					<td align=left></td>

					<td align=right>$employee_coursedurance</td>

					<td align=right>$standart_courserate</td>

					<td align=right>￿ ".round($employee_entgeltsummary,2)."</td></tr>";



					$summary_durance+=$employee_coursedurance;

					$summary_entgeltsummary+=$employee_entgeltsummary;

					$brutto=(($summary_entgeltsummary/100)*$steuersatz)+$summary_entgeltsummary;

			$commission=$employee_commission;

			}
		}

		$color="#FFFF80";

		$commission=="" ? $commission=0 : $commission;

		$commission_summary=$commission;

		$complete_summary=$commission_summary+$summary_entgeltsummary;

		$html_body.="<tr>

		<td height=40 align=left><font size=2><b>Summe Training / Monat</b></font></td>

		<td align=left width=>&nbsp;</td>

		<td align=left>&nbsp;</td>

		<td align=right><font size=2><b>$summary_durance</b></font></td>

		<td align=right>&nbsp;</td>

		<td align=right><font size=2><b>￿ ".round($summary_entgeltsummary)."</b></font></td>

		</tr>

		<tr>

		<td align=left height=30><font size=2><b>Provision</b></font></td>

		<td align=left width=>&nbsp;</td>

		<td align=left>&nbsp;</td>

		<td align=right><font size=2><b></b></font></td>

		<td align=right>&nbsp;</td>

		<td align=right><font size=2><b>￿ ".round($commission_summary)."</b></font></td>

		</tr>

		<tr>

		<td align=left height=30><font size=3><b><u>Gesamtsumme</u></b></font></td>

		<td align=left width=>&nbsp;</td>

		<td align=left>&nbsp;</td>

		<td align=right><font size=2><u><b></b></u></font></td>

		<td align=right>&nbsp;</td>

		<td align=right><font size=2><u><b>￿ ".round($complete_summary)."</b></u></font></td>

		</tr>

		</table><br>

		<TABLE width=650   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=0>

		<tr>

		<td height=10 align=center><font size=2><b>Mit der vorliegenden Monatsabrechnung bin ich einverstanden.</b></font></td>

		</tr>

		<tr>

		<td height=10 align=center><font size=2><b>Die Gesamtstunden und Ihre Verteilung sind in der vorgelegten Form<br> von mir geleistet worden.</b></font></td>

		</tr>

		</table><br><br>

		<TABLE width=650   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1>

		<tr>

		<td height=40 align=center valign=bottom><font size=2><b>Unterschrift:  ________________________</b></font></td>

		</tr></table>

		<br><br>

		<TABLE width=650   CELLPADDING=0 CELLSPACING=0 bordercolor=black border=0><tr>

		<td align=left height=30><font size=2><b>Intern nach Produkten</b></font></td>

		<td align=left width=>&nbsp;</td>

		<td align=left>&nbsp;</td>

		<td align=right><font size=2><b>Monat $select_month/$select_year <br><br>Netto</b></font></td>

		</tr>

		";

		$brutto=round($brutto,2);

		$rs2=getrs("
				(select a.id,
	                  a.title,
					  a.firstname,
					  a.lastname,
					  a.commission,
					  a.contract_id,
					  c.date,
					  c.time,
					  sum(c.durance),
					  concat(d.products_id,d.year,d.timeperiods_id,d.institutions_id,d.locations_id,'-',left(d.info,7) )
					 as coursecode,
					 d.id,
					 b.hourcost,
					(b.hourcost*sum(c.durance))
		from employees a,
			coursetimes_employees b,
			coursetimes c,
			courses d,
			products e
 	    where $employee=a.id and
				     a.status in('Aktiv','Inaktiv') and
					 d.status='Aktiv' and
				     b.employees_id=a.id and
				     b.coursetimes_id=c.id and
				     c.courses_id=d.id and
					 d.products_id=e.id and
					 left(c.date,4)='$select_year' and
					 substring(c.date,6,2)='$select_month' group by coursecode
			) UNION (
				select a.id,
	                  a.title,
					  a.firstname,
					  a.lastname,
					  a.commission,
					  a.contract_id,
					  c.date,
					  c.time,
					  sum(c.durance),
					  concat(d.products_id,d.year,d.timeperiods_id,d.institutions_id,d.locations_id,'-',left(d.info,7) )
					 as coursecode,
					 d.id,
					 c.employee4_hc,
					(c.employee4_hc*sum(c.durance))
		from employees a,
			coursetimes c,
			courses d,
			products e
 	    where $employee=a.id and
				     a.status in('Aktiv','Inaktiv') and
					 d.status='Aktiv' and
				     c.employee4_id=a.id and
				     c.courses_id=d.id and
					 d.products_id=e.id and
					 left(c.date,4)='$select_year' and
					 substring(c.date,6,2)='$select_month' group by coursecode )			
",0,1);

			while(LIST( $employee_id,

						$employee_title,

						$employee_firstname,

						$employee_lastname,

						$employee_commission,

						$employee_contract,

						$employee_coursedate,

						$employee_coursetime,

						$employee_coursedurance,

						$employee_coursecode,

						$employee_courseid,

						$employee_satz,

						$employee_entgeltsum)=$rs2 -> fetch_row())

			{

				  $newdate=explode("-",$employee_coursedate);

			$sql_kurs="	select concat(a.name,'/',b.year,'/',c.name,'/',d.name,'/',e.name,'/',b.info)

						from products a, courses b, timeperiods c, institutions d, institutions e

						where $employee_courseid=b.id and b.products_id=a.id and b.timeperiods_id=c.id

								and b.institutions_id=d.id and b.locations_id=e.id";

			$rs_kurs=getrs($sql_kurs,0,1);

			list($kursname)=$rs_kurs -> fetch_row();

				  $html_bruttonetto.="<tr>

					<td align=left height=30></td>

					<td align=left>$kursname</td>

					<td align=left></td>

					<td align=right>￿ $employee_entgeltsum</td></tr>";

			}

		$abgaben_steuer=round((($complete_summary/100)*$steuersatz),2);

		$bruttogehalt=round(($complete_summary+$abgaben_steuer),2);

      	$html_output=$html_header.$html_body.$html_bruttonetto."</table>";

return;

}

function loanthings($modus,$modus_id){

	global $html_output;

	global $query_text;

			// ￿ersicht Ger￿

			if($modus==1){

				$rs=getrs("select a.id,a.name,b.pieces,b.begin,b.end,b.status,concat(c.lastname,' ',c.firstname),c.id
				from objects a, loan_objects b, employees c
				where b.name_id=$modus_id and b.employees_id=c.id and a.id=b.name_id",0,1);
			}

			// ￿ersicht Mitarbeiter

			elseif($modus==2){
				$rs=getrs("select a.id,a.name,b.pieces,b.begin,b.end,b.status,concat(c.lastname,' ',c.firstname),c.id
				from objects a, loan_objects b, employees c
				where b.employees_id=$modus_id and b.employees_id=c.id and a.id=b.name_id",0,1);
			}
			$nr=0;
			$html_output="<table border=1 bordercolor=black CELLPADDING=0 CELLSPACING=0><tr><td>Nr</td><td height=40 align=center><b>&nbsp;Entlehner bzw. Objekt&nbsp;</b></td><td align=center><b>&nbsp;St￿p;nbsp;</b></td><td align=center><b>&nbsp;Beginn&nbsp;</b></td><td align=center><b>&nbsp;Ende&nbsp;</b></td><td align=center><b>&nbsp;Zur￿eben&nbsp;</b></td></tr>";
			while(LIST($object_id,$object_name,$loan_pieces,$loan_begin,$loan_end,$loan_status,$loan_name,$loan_name_id)=$rs->fetch_row())
			{

				$nr++;

				if($modus==1){

					$html_output.="<tr height=25><td>$nr</td><td align=right><nobr>&nbsp;<a href=loan_things_form.php?id=$loan_name_id&e_id=$loan_name_id target=_blank> $loan_name&nbsp;-&nbsp;$object_name&nbsp;</nobr></td>

					 <td><nobr>&nbsp; $loan_pieces</nobr></td>

					 <td align=right>&nbsp; $loan_begin&nbsp;</td>

					 <td align=right>&nbsp; $loan_end&nbsp;</td>

				     <td align=right>&nbsp; $loan_status&nbsp;</td>

					 </tr>";

				}

				elseif($modus==2){

					$html_output.="<tr height=25><td>$nr</td><td align=right><nobr>&nbsp;<a href=loan_things_form.php?id=$loan_name_id&e_id=$loan_name_id  target=_blank> $loan_name&nbsp;-&nbsp;$object_name&nbsp;</nobr></td>

					 <td><nobr>&nbsp; $loan_pieces</nobr></td>

					 <td align=right>&nbsp; $loan_begin&nbsp;</td>

					 <td align=right>&nbsp; $loan_end&nbsp;</td>

				     <td align=right>&nbsp; $loan_status&nbsp;</td>

					 </tr>";

				}

			}

			?></table><?

			return $html_output;
}
?>

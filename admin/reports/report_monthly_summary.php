<?
require_once("../include/session.php");

require_once("../include/html.php");

require_once("../include/checkfunction.php");

require_once("../include/littlefunctions1.php");
?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>Auszahlungsübersicht</title>

<link rel="stylesheet" href="../css/ta.css">
</head>
<body>

<div align=middle">

<table border=0 width=100% cellspacing=0 cellpadding=0>

<tr><td height=12></td></tr>

<tr><td width=80% height=27 align=center valign=top background='../images/underlineheader.gif'>

<SPAN class="headline">Auszahlungsübersicht</SPAN><br>

</td></tr>

<tr><td height=10></td></tr>

</table>

<table width=100% border=0>

<tr>
<td>&nbsp;</td>
<td width=80%>
<?
$DEBUG = 0;
?>

<?
if ($save)
{
          $m = date('m');
        $y = date('Y');
            $yy = $y;
            if ($m==1) $yy=$y-1;
            
		$sql_select="select

			employees.id,employees.firstname, employees.lastname,max(coursetimes_employees.hourcost)*count(coursetimes_employees.coursetimes_id)

		from

			employees, coursetimes, coursetimes_employees

		where

                         coursetimes_employees.employees_id=employees.id

			and

			employees.status in ('Aktiv','Neu')

			and

			coursetimes_employees.coursetimes_id=coursetimes.id

			and

			left(coursetimes.date,4)='$yy'

		group by id

		order by lastname asc,hourcost desc";

  		$rs_select=getrs($sql_select,0);

		while(($rs_select>0) && (list($eid_,$fn,$ln,$ss)=mysql_fetch_row($rs_select)))
		{
          $y = date('Y');
          $m = date('m');
          for ($ll=1; $ll<=7; $ll++)
          {
//            echo $eid_."|".${"b".$eid_.$m.$y}."-";
            $sql_sum="select id,status from bills where employee_id='$eid_' and month_id='$m' and year='$y'";
            $rs_sum = getrs($sql_sum,0);
//            echo mysql_error();
            list($bid,$bstatus)=mysql_fetch_row($rs_sum);
//            echo $bid;
            if ($bid)
            {
//                echo $bid;
                $sql_upd="update bills set status='".${"b".$eid_.$m.$y}."' where id=$bid";
                $rs_upd = getrs($sql_upd,$DEBUG);
            } else
            {
                if (${"b".$eid_.$m.$y}=="Ok")
                {
                    $sql_ins="insert into bills (employee_id,month_id,year,status) values ('$eid_','$m','$y','Ok')";
                    $rs_ins = getrs($sql_ins,$DEBUG);
                }
            }
            //echo "b$eid$m$y-".${"b".$eid.$m.$y};
            $m--;
            if ($m<=0) { $m=12; $y--;}

		  }

        }
}
?>
<FORM action="<? echo $PHP_SELF?>" method=post name=bills>

<input type="hidden" name="mid" value="<?print($mid)?>">

<input type=hidden name=billchange value=0>

<input type=hidden name=showform value=true>

<input type=hidden name=id value=5>

	<TABLE width=600 border=0 cellpadding=0 cellspacing=0>

    <tr>
    <td colspan=6>
    <div align=middle><input type=submit value="abspeichern" name=save></div>
    </td>
    </tr>
	<TR>

		<TD colspan=3 height=50>

		<TABLE WIDTH=100% HEIGHT=100%>

		<tr>

            <?
            $m = date('m');
            $y = date('Y');
            $yy = $y;
            if ($m==1) $yy=$y-1;
            if (isset($year)) $yy=$year;
            ?>
			<td>Name (Verdienst <?print($yy)?> )</td>

			<?$i=0;

            for ($ll=1; $ll<=7; $ll++)
            {

/*			$sql_monat="select id,month,year from months order by year desc,month desc limit 7";

			$rs_monat=getrs($sql_monat);

			while(($rs_monat>0) && (list($mid,$monat,$year)=mysql_fetch_row($rs_monat)))

			{
*/
                if (strlen($m)==1) $m='0'.$m;
                ?>
				<td><?print($m."-".$y)?></td>

           <? $m--;
              if ($m<=0) { $m=12; $y--;}
           }?>

		</tr>
		<?

 /*
select

			employees.id,employees.firstname, employees.lastname,max(coursetimes_employees.hourcost)*count(coursetimes_employees.coursetimes_id)

		from

			employees, coursetimes, coursetimes_employees

		where

                         coursetimes_employees.employees_id=employees.id

			and

			employees.status in ('Aktiv','Neu')

			and

			coursetimes_employees.coursetimes_id=coursetimes.id

			and

			left(coursetimes.date,4)='2005'
group by id
		order by lastname asc, hourcost desc
  */
		$sql="select

			employees.id,employees.firstname, employees.lastname,max(coursetimes_employees.hourcost)*count(coursetimes_employees.coursetimes_id)

		from

			employees, coursetimes, coursetimes_employees

		where

                         coursetimes_employees.employees_id=employees.id

			and

			employees.status in ('Aktiv','Neu')

			and

			coursetimes_employees.coursetimes_id=coursetimes.id

			and

			left(coursetimes.date,4)='$yy'

		group by id

		order by lastname asc,hourcost desc";

		$rs=getrs($sql,0);

		while(($rs>0) && (list($eid,$firstname,$lastname,$sum)=mysql_fetch_row($rs)))

		{?>

		<TR>

			<TD ALIGN=left>

				<?print($eid." ".$lastname." ".$firstname."(".$sum.")");?>

			</TD>

			<?

            $m = date('m');
            $y = date('Y');
        for ($ll=1; $ll<=7; $ll++)
        {
            print("<td>");
            $sql_checkbox="select status from bills where employee_id=$eid and month_id=$m and year=$y";
   		    $rs_checkbox=getrs($sql_checkbox,0);
		    list($status)=mysql_fetch_row($rs_checkbox);
            if ($status) $text="checked";
            if (${"b".$eid.$m.$y}=="Ok") $text="checked";
            print("<input type=checkbox $text name=b$eid$m$y value='Ok'>");
            //print("b$eid$m$y");
            print("</td>");
            $text="";
            $m--;
            if ($m<=0) { $m=12; $y--;}

		}

	?>

		</TR>

		<?}?>

		</TABLE>

		</form>

</td>
<td></td>
</tr>
</table>
</body>
</html>

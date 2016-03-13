<?
/* ID einlesen, die von_GET und/oder _POST kommen kann */
if ((empty($_GET['id']) || $_GET['id'] == ""))
{ $id = $_POST['id']; }
else
{ $id = $_GET['id']; 
}
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/* Dateiname: loan_things_form.php
*  Zweck: Formular zur Verwaltung des Geräteverleihs
*/

/* Debuginfo und Errorinfo einlesen */
$print_debug = $_GET['print_debug'];
$print_error = $_GET['print_error'];
isAllow(isAdmin() || isSecretary());

/* _POST einlesen */
$delete_x = $_POST['delete_x'];
$save_x = $_POST['save_x'];
$kopie = $_POST['kopie'];
$confirm = $_POST['confirm'];
$update = $_POST['update'];
$change_date = date('Y-m-d H:m:s');
$change_user = $_SESSION['username'];
$reload = $_POST['reload'];
$num = $_POST['num'];
$newrow = $_POST['newrow'];
$loan_obj_id = $_POST['loan_obj_id'];
$pieces = $_POST['pieces'];
$pieces_add = $_POST['pieces_add'];
$n_id = $_POST['n_id'];
$description = $_POST['description'];
$begindate = $_POST['begindate'];
$returndate = $_POST['returndate'];
$status = $_POST['status'];
$begindate_day = $_POST['begindate_day'];
$begindate_month = $_POST['begindate_month'];
$begindate_year = $_POST['begindate_year'];
$returndate_day = $_POST['returndate_day'];
$returndate_month = $_POST['returndate_month'];
$returndate_year = $_POST['returndate_year'];
$max = $_POST['max'];
$mit_id = $_POST['mit_id'];
$loop = $_POST['loop'];

if ($loop=="") $loop=0;

?>

<HTML>
<HEAD>
	<link rel="stylesheet" href="../css/ta.css">
	<script type="text/javascript">
		function delete_form()
		{
		  document.formular.confirm.value = confirm("Wollen Sie diesen Eintrag wirklich löschen?");
		}
		
		function newLoad()
		{
		  document.formular.reload.value=1;
		  document.formular.submit();
		}
		
		function newEmployee()
		{
		  document.formular.reload.value=1;
		  document.formular.newrow.value=0;
		  document.formular.submit();
		}
		
		function addform(nr)
		{
			nr=(document.formular.newrow.value*1)+1;
			document.formular.newrow.value=nr;
			document.formular.reload.value=1;
			document.formular.submit();
		}

		function show_message()
		{
			document.getElementById('light').style.display='block';
			document.getElementById('fade').style.display='block';
		}
		
	</script>
</HEAD>

<?
// Delete-Button wird gedrückt
  $back_url="../admin/loan_things_list.php";

if (isset($delete_x))
{
        if ($confirm=="true" && $id>0)
        {
           $sql_delete="update objects set status='Entfernt' where id=".$id;
           $rs=getrs($sql_delete,$print_debug,$print_error);

        }
        else
        {
           error_site($back_url);
        }
}

// Back-Button wird gedrückt

elseif (isset($back_x))
{
  header("Location: $back_url");
}

// Save-Button wird gedrückt

elseif (isset($save_x))
{
  	$no_error=true;

//	if (!$id && $status) {$no_error=false; $error_status='Ausborgen u. Zurückgeben gleichzeitig nicht möglich !';}
//	echo "Speicherung";

for ($loop=0;$loop<=$max;$loop++)
{
  $begindate[$loop]  = $begindate_year[$loop]."-".$begindate_month[$loop]."-".$begindate_day[$loop];
  $returndate[$loop] = $returndate_year[$loop]."-".$returndate_month[$loop]."-".$returndate_day[$loop];

  if ($status[$loop]=="")
    $status[$loop]="Nein";
  if (isset($loan_obj_id[$loop]) && $loan_obj_id[$loop]!="" && $loan_obj_id[$loop]>0 && $no_error && ($n_id[$loop]>0))
  {
	$p[$loop]=$pieces[$loop]+$pieces_add[$loop];
     $sql_update="update loan_objects set
                 employees_id = '$id',
                 pieces       = '$p[$loop]',
                 name_id         = '$n_id[$loop]',
                 description     = '$description[$loop]',
                 begin        = '$begindate[$loop]',
                 end          = '$returndate[$loop]',
                 status       = '$status[$loop]'
       where id=".$loan_obj_id[$loop];
            $rs_update=getrs($sql_update,$print_debug,$print_error);

		if ($status[$loop]=='Ja') 
		{
			$sql_objects="update objects set loan = loan-$pieces[$loop], available  = available+$pieces[$loop] where id=$n_id[$loop]";
		} else
		{
			$sql_objects="update objects set loan = loan+$pieces_add[$loop], available  = available-$pieces_add[$loop] where id=$n_id[$loop]";
		}
		$rs_objects=getrs($sql_objects,$print_debug,$print_error);
  }
  elseif($no_error)
  {
       	if ($n_id[$loop]!=0)
		{
			$begindate[$loop]  = $begindate_year[$loop]."-".$begindate_month[$loop]."-".$begindate_day[$loop];
           $returndate[$loop] = $returndate_year[$loop]."-".$returndate_month[$loop]."-".$returndate_day[$loop];
			$p[$loop]=$pieces[$loop]+$pieces_add[$loop];
          $sql_loan_insert="insert into loan_objects
                                     (employees_id,pieces,name_id,description,begin,end,status)
                             values
               ('$id','$p[$loop]','$n_id[$loop]','$description[$loop]','$begindate[$loop]','$returndate[$loop]','$status[$loop]')";
            $rs_loan_insert=getrs($sql_loan_insert,$print_debug,$print_error);
          
            $sql_obj="update objects set loan=loan+$pieces_add[$loop], available=available-$pieces_add[$loop] where id=$n_id[$loop]";
            $rs_obj=getrs($sql_obj,$print_debug,$print_error);  
		}
  }
 }
 $reload=0;
 $newrow=0;

if ($no_error) $command="show_message();";
?>	

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
<? } else {?>
<BODY>
<?
}
if (!isset($newrow)) $newrow=0;
$text_hidden="hidden";
if ($print_debug==1) 
{
	var_dump($_POST);
	$text_hidden="text";
}
?>
<center>
<table border=0 cellspacing=0 cellpadding=0>
<tr><td height=12></td></tr>
<SPAN class="headline">Geräteverleih</SPAN><br>
</td></tr>
<tr><td height=10></td></tr>
</table>

<BR><BR>
<FORM  action="<? echo $PHP_SELF?>" method="POST" name=formular>
<TABLE width=700 border=0 CELLPADDING=0 CELLSPACING=0>
<tr>
   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td colspan=3 HEIGHT=1  class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <td rowspan=100 WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD colspan=3 ALIGN=left   class="form_header"><table><tr><td>Mitarbeiter:</td></tr></table>
   <SELECT NAME=id>
	 <OPTION VALUE=0></OPTION>
   <?
   $rs_name=getrs("select id, firstname, lastname from employees where status in ('Aktiv', 'Inaktiv') order by lastname asc",$print_debug,$print_error);
   while(LIST($eid,$empl_firstname,$empl_lastname)=$rs_name -> fetch_row())
   {
       if ($eid==$id)
               echo "<OPTION selected VALUE=$eid>$empl_lastname $empl_firstname</OPTION>";
       else
         echo "<OPTION VALUE=$eid>$empl_lastname $empl_firstname</OPTION>";
   }
           ?>

   </SELECT>
	</TD>
</TR>
<tr>
	<td colspan=3 height=5 class=form_header></td>
</tr>
<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>
<TR>
   <td>
   <TABLE width=100% border=0 CELLPADDING=0 CELLSPACING=0>

   <tr>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD  ALIGN=left   class=form_header>
   	<table><tr><td>&nbsp;&nbsp;Gerätebezeichnung</td></tr></table>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD  ALIGN=left   class=form_header>
   	<table><tr><td>&nbsp;&nbsp;Menge</td></tr></table>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD  ALIGN=left   class=form_header>
   	<table><tr><td>&nbsp;&nbsp;Leihdatum</td></tr></table>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD  ALIGN=left   class=form_header>
   	<table><tr><td>&nbsp;&nbsp;Rückgabedatum</td></tr></table>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD  ALIGN=left   class=form_header>
   	<table><tr><td>&nbsp;&nbsp;Beschreibung</td></tr></table>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD  ALIGN=left   class=form_header>
   	<table><tr><td>&nbsp;&nbsp;zurückgegeben</td></tr></table>
   </TD>
  </tr>

<?// Daten aus DB Laden

if (1==1)
{
    if ((isset($id) && $id!=""))
	{
		$sql_objects_select="select * from loan_objects where employees_id=$id and status='Nein'";
    	$rs_objects_select=getrs($sql_objects_select,$print_debug,$print_error);
    	if (!$reload) $num=$rs_objects_select -> num_rows;
	}
	if ($num==0) $num=0;
//	echo "-".$num."+".$newrow;
	$max=$num+$newrow;
	for ($loop=0;$loop<=$num+$newrow;$loop++)
	{
//  	  echo "".$loop."-";
	  if (!$reload)
	  {
		if (($loop<$num) && ((isset($id) && $id!="")))
		{
			LIST($loan_obj_id[$loop],$employees_id,$pieces[$loop],$n_id[$loop],$description[$loop],$begindate[$loop],$returndate[$loop],$status[$loop])=$rs_objects_select -> fetch_row();
		} else
		{
			$loan_obj_id[$loop]=0;
			$employees_id=$id;
			$pieces[$loop]=0;
			$n_id[$loop]=0;
			$description[$loop]="";
			$begindate[$loop]=date("Y-m-d");
			$returndate[$loop]="";
			$status[$loop]="Nein";
		}
	  } else
	  {
		if (($begindate_year==0) || ($begindate_year=="")) { $begindate[$loop]=date("Y-m-d"); }
      }
		$dat=explode("-",$returndate[$loop]);
		$returndate_year[$loop]=$dat[0];
		$returndate_month[$loop]=$dat[1];
		$returndate_day[$loop]=$dat[2];

		$dat=explode("-",$begindate[$loop]);
		$begindate_year[$loop]=$dat[0];
		$begindate_month[$loop]=$dat[1];
		$begindate_day[$loop]=$dat[2];

?>
  <tr>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row>
   <INPUT TYPE="<?print($text_hidden)?>" NAME="loan_obj_id[<?print($loop)?>]" value=<?echo $loan_obj_id[$loop]?>>
   <INPUT TYPE="<?print($text_hidden)?>" NAME="begindate[<?print($loop)?>]" value=<?echo $begindate[$loop]?>>
   <INPUT TYPE="<?print($text_hidden)?>" NAME="returndate[<?print($loop)?>]" value=<?echo $returndate[$loop]?>>
   <SELECT NAME=n_id[<?print($loop)?>] onblur=newLoad()>
	 <OPTION VALUE=0></OPTION>
<?
   
   	$rs_obj=getrs("select id,name from objects where status in ('Aktiv') and available>=0 order by name asc",1,$print_error);
      
	   while(LIST($name_id,$name_name)=$rs_obj->fetch_row())
	   {
	       if ($n_id[$loop]==$name_id)
	               echo "<OPTION selected VALUE=$name_id>$name_name</OPTION>";
	       else
	         echo "<OPTION VALUE=$name_id>$name_name</OPTION>";
	   }
?>

   </SELECT>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=400  class=form_row>
   <? echo "&nbsp;&nbsp;<b>".$pieces[$loop]."+</b>";?>
       <? 
       
          	echo display_error($error_pieces);
          	echo"<SELECT NAME='pieces_add[]'>";
         
         if (!$things_id){$things_id=$name_id;}
                  	
           $sql_pi="select available from objects where id='".$n_id[$loop]."'";
           $rs_pi=getrs($sql_pi,$print_debug,$print_error);
           LIST($available_pieces)=$rs_pi -> fetch_row();          
        for($i=0;$i<=$available_pieces;$i++)
		{
             if ($pieces_add[$loop]==$i) {$text="selected";} else { $text=""; }
			 if (strlen($i)==1) $i="0".$i;
             echo"<OPTION VALUE=$i $text>$i</OPTION>";
        }
        ?>
      
	</SELECT> 
   <INPUT TYPE="hidden" NAME="pieces[]" value=<?echo $pieces[$loop]?>>
	</TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
	<TD>
       <TABLE>
       <TR><TD >
       <?echo display_error($error_begindate_day);?>
       <SELECT NAME=begindate_day[]>
         <option value=0>00</option>
		 <?for($i=1;$i<=31;$i++){
             if (strlen($i)==1)
             $i="0".$i;
             ?><OPTION <?if ($i==$begindate_day[$loop]) echo "selected" ?> VALUE=<?echo $i?>><?echo $i?></OPTION>
		<?        }?>
		</SELECT> </TD>
		 <TD >
		 <?echo display_error($error_begindate_month);?>
		 <SELECT NAME=begindate_month[]>
         <option value=0>00</option>
         <?for($i=1;$i<=12;$i++){
             if (strlen($i)==1)
             $i="0".$i;?>
               <OPTION <?if ($i==$begindate_month[$loop]) echo "selected" ?> VALUE=<?echo $i?>><?echo $i?></OPTION>
		<?        }?>
		</SELECT></TD>
		<TD>
		<?echo display_error($error_begindate_year);?>
		<SELECT NAME=begindate_year[]>
         <option value=0>0000</option>
         <? echo date('m.d.Y');
         for($i=(date('Y')-2);$i<=date('Y');$i++){

             if (strlen($i)==1)
             $i="0".$i;?>
              <OPTION <?if ($i==$begindate_year[$loop]) echo "selected" ?> VALUE=<?echo $i?>><?echo $i?></OPTION>
<?        }
        ?>
		</SELECT></TD></TR></TR>
		</TABLE>
	</TD>   
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD class=form_row>
       <TABLE>
       <TR><TD >
       <?echo display_error($error_returndate_day);
	   if (($returndate_day[$loop]=="0") || ($returndate_day[$loop]=="")) $returndate_day[$loop]="31";?>
       <SELECT NAME=returndate_day[]>
         <option value=0>00</option>
         <?for($i=1;$i<=31;$i++){
             if (strlen($i)==1)
             $i="0".$i;?>
             <OPTION  <?if ($i==$returndate_day[$loop]) echo "selected" ?> VALUE=<?echo $i?>><?echo $i?></OPTION>
	<?        }?>
	</SELECT> </TD>
	 <TD >
 	<?echo display_error($error_returndate_month);
	   if (($returndate_month[$loop]=="0") || ($returndate_month[$loop]=="")) $returndate_month[$loop]="12";?>
	 <SELECT NAME=returndate_month[]>
         <option value=0>00</option>
         <?for($i=1;$i<=12;$i++){
             if (strlen($i)==1)
             $i="0".$i;?>
             <OPTION <?if ($i==$returndate_month[$loop]) echo "selected" ?> VALUE=<?echo $i?>><?echo $i?></OPTION>
	<?        }?>
	</SELECT></TD>
	<TD>
	<?echo display_error($error_returndate_year);
	   if (($returndate_year[$loop]=="0") || ($returndate_year[$loop]=="")) $returndate_year[$loop]=date('Y')+5;?>
	<SELECT NAME=returndate_year[]>
         <option value=0>0000</option>
         <? echo date('m.d.Y');
         for($i=date('Y');$i<=(date('Y')+5);$i++){

             if (strlen($i)==1)
             $i="0".$i;?>
             <OPTION <?if ($i==$returndate_year[$loop]) echo "selected" ?> VALUE=<?echo $i?>><?echo $i?></OPTION>
	<?        }
	        ?>
	</SELECT></TD></TR>
	</TABLE>
   </TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row>
	   <TEXTAREA ROWS=2 COLS=40 NAME=description[]><?echo $description[$loop]?></TEXTAREA>
	</TD>
   <td WIDTH=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250  class=form_row align=center><? echo display_error($error_status);?>
	<select name=status[]>
		<option value='Nein' <?   if ($status[$loop]=="Nein") echo "selected";?>>Nein</option>
		<option value='Ja' <?   if ($status[$loop]=="Ja") echo "selected";?>>Ja</option>
	</select>
</TR>
<tr>
   <td colspan=15 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<?
	}
}
	?>
</table>
</td></tr>

<tr>
   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../images/black.gif" HEIGHT=1 WIDTH=1></TD>
</TR>

<TR>
   <TD colspan=5 height=50 class=form_footer>
   <TABLE WIDTH=100% HEIGHT=100%>
   <tr><td colspan=5 align=center><a href="javascript:addform(<?print($newrow+1)?>)">weiteren Gegenstand ausborgen</a></td></tr>
   <TR><TD ALIGN=CENTER>
   <INPUT TYPE="IMAGE" NAME="save" src="../images/buttons/send.gif"></TD > 
   <TD ALIGN=CENTER>
<? if (isAdmin())
{?>
   <INPUT TYPE="IMAGE" NAME="delete" src="../images/buttons/delete.gif" onClick="delete_form()">
<?} ?>
   <INPUT TYPE="hidden" NAME="newrow" value=<?echo ($newrow)?>>
   <INPUT TYPE="HIDDEN" NAME="confirm" value=0>
   <INPUT TYPE="HIDDEN" NAME="modus" value=<?echo $modus?>>
   <INPUT TYPE="HIDDEN" NAME="reload" value=0>
   <INPUT TYPE="HIDDEN" NAME="newstatus" value=0>
   <INPUT TYPE="HIDDEN" NAME="max" value=<?echo $max?>>
   <INPUT TYPE="HIDDEN" NAME="num" value=<?echo $num?>>
   <INPUT TYPE="HIDDEN" NAME="loop" value=<?echo $loop?>>
   <INPUT TYPE="HIDDEN" NAME="price_hidden" value=<?echo $price_hidden?>>
    </TD></TR></TABLE></TD>    </FORM>
</TR>

</TABLE>
</CENTER>

</BODY>
</HTML>
<?

require_once("../../include/session.php");

require_once("../../include/autotable.php");

isAllow(isAdmin() || isSecretary());



?>



<HTML>

<HEAD>

<link rel="stylesheet" href="../../css/ta.css">

</HEAD>

<BODY>

<center>

<table border=0 cellspacing=0 cellpadding=0>

<tr><td height=12></td></tr>

<tr><td width=200 height=27 align=center valign=top>

<SPAN class="headline">Berichte</SPAN><br>

</td></tr>

<tr><td height=10></td></tr>

</table>

<BR><BR>



 



<?



 if (isAdmin())

	 $sql  = "SELECT id,id AS 'ID', name AS 'Bericht',description AS 'Beschreibung' FROM reports where flag=1 or flag=2";

 elseif (isSecretary())

     $sql  = "SELECT id,id AS 'ID',name AS 'Bericht',description AS 'Beschreibung' FROM reports where flag=2";

// echo $sql;





HtmlTableFromSQLGlobalsInit(2);

$links[0] = new stdClass();
$colformat[4] = new stdClass();

$links[0]->url = "report_form.php?nb=1&noblank=1&id=";

$links[0]->column = "ID";

$links[0]->param = "";

$links[0]->target = "_blank";

$links[0]->reports = "reports";

$tableparam = "";

$colformat[4]->nosortlink=true;

HtmlTableFromSQL ($sql, $field,$links, $sortcol, true,$tableparam,$rowpos,$pagesize);

?>

</CENTER>



</BODY>

</HTML>

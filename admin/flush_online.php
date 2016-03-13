<?
require_once("../include/session.php");
require_once("../include/html.php");
require_once("../include/checkfunction.php");

/*
CREATE TEMPORARY TABLE tmptable
select e.id from __payments_20160214 a,__clients_20160214 b,courses c,products d,coursetimes e where e.courses_id=c.id and c.id>600 and a.courses_id=c.id and c.products_id=d.id and a.clients_id=b.id and a.status in ('F','Entfernt') and a.courses_id in (select a.id from courses a, timeperiods b where a.timeperiods_id=b.id and b.camp=1) group by c.id;
delete coursetimes_clients from coursetimes_clients inner join tmptable on coursetimes_clients.coursetimes_id=tmptable.id

*/
// Payment_IDs ermitteln
	    $rs_search=getrs("CREATE TEMPORARY TABLE tmppayments select a.id from payments a,clients b,courses c,products d where c.id>600 and a.courses_id=c.id and c.products_id=d.id and a.clients_id=b.id and a.status in ('F','Entfernt') and a.courses_id in (select a.id from courses a, timeperiods b where a.timeperiods_id=b.id and b.camp=1)",1,1);
	    $num_result=$rs_search->num_rows;

		$payment_id="0";
		while(LIST($pid)=$rs_search -> fetch_row())
		{
			$payment_id.=";".$pid;
		} 

		print("Anzahl an Payments: ".$num_result."<br>");
		print($payment_id);
		print("<br>");
		
		
// Client_IDs ermitteln

	    $rs_search=getrs("select b.id,b.lastname,b.firstname from payments a,clients b,courses c,products d where c.id>600 and a.courses_id=c.id and c.products_id=d.id and a.clients_id=b.id and a.status in ('F','Entfernt') and a.courses_id in (select a.id from courses a, timeperiods b where a.timeperiods_id=b.id and b.camp=1) group by b.id",1,1);
	    $num_result=$rs_search->num_rows;

		$client_id="0";
		while(LIST($cid)=$rs_search -> fetch_row())
		{
			$client_id.=";".$cid;
		} 
		print("Anzahl an Kunden: ".$num_result."<br>");
		print($client_id);
		print("<br>");

// coursetimes_IDs ermitteln

	    $rs_search=getrs("select e.id from payments a,clients b,courses c,products d,coursetimes e where e.courses_id=c.id and c.id>600 and a.courses_id=c.id and c.products_id=d.id and a.clients_id=b.id and a.status in ('F','Entfernt') and a.courses_id in (select a.id from courses a, timeperiods b where a.timeperiods_id=b.id and b.camp=1) group by c.id",1,1);
	    $num_result=$rs_search->num_rows;

		$coursetimes_id="0";
		while(LIST($ctid)=$rs_search -> fetch_row())
		{
			$coursetimes_id.=";".$ctid;
		} 
		print("Anzahl an Kurszeiteinträgen: ".$num_result."<br>");
		print($coursetimes_id);
		print("<br>");

		print("Löschen...<br>");
		
/*	    $rs_search=getrs("delete payments from payments inner join tmppayments on payments.id=tmppayments.id",1,1);
	    $num_result=$rs_search->affected_rows;

		print("Anzahl gelöschter Payments: ".$num_result."<br>");
		
	    $rs_search=getrs("delete from clients where id in (".$client_id.")",1,1);
	    $num_result=$rs_search->affected_rows;

		print("Anzahl gelöschter Kunden: ".$num_result."<br>");

	    $rs_search=getrs("delete from coursetimes_clients where coursetimes_id in (".$coursetimes_id.")",1,1);
	    $num_result=$rs_search->affected_rows;

		print("Anzahl gelöschter Kurszeit-Kunden Einträge: ".$num_result."<br>");
*/
?>

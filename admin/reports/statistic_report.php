<?
require_once("../../include/session.php");
require_once("../../include/html.php");
require_once("../../include/checkfunction.php");

$nb=$_GET["nb"];
$sort1=$_POST["sort1"];
$sort2=$_POST["sort2"];
$sort3=$_POST["sort3"];
$order=$_POST["order"];
$sql_view=$_POST["sql_view"];
$x_achse=$_POST["x_achse"];
$y_achse=$_POST["y_achse"];
$print_debug=$_POST["print_debug"];
$sql_view=$print_debug;

// Statistik function ///

function statistic_report ($x,$y,$sort1,$sort2,$sort3,$order,$sql_view)
{
	global $html_output;
	global $query_text;
	
	if ($y!="Trainer")
	{
		if ($sort1=="Jahre") 
		{ 
			$sorting=" a.year $order,e.name asc";
		} else
		{
			$sorting=" e.name asc,a.year $order";
		}
	} else
	{
		if ($sort1=="Jahre") 
		{ 
			$sorting=" a.year $order,e.lastname asc,e.firstname asc";
		} else
		{
			$sorting=" e.lastname asc,e.firstname asc,a.year $order";
		}
	}

	switch($x)
	{
		case "Wochentage":
			{
				switch($y)
				{
					case "Gesamt":
					{
						// SQL Abfrage
						$headline[0]="Jahr";
						$headline[1]="Wochentag";
						$headline[2]="Anzahl Kinder";
						$headline[3]="Anzahl Kurse";
						$sql_statistic = "select 
								a.id, 
								a.year,
								c.weekday,
								count(distinct b.clients_id) 
							from 	courses a, 
									payments b, 
									weekdays c, 
									clients d 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									c.id=a.weekday 
							group by a.year,a.weekday
							order by a.year $order";
							break;

					}
                    case "Camps":
					{ /* NUR EINE KOPIE VON SEMESTER/KURSE, da nach Wochentage keinen Sinn macht */
						$headline[0]="Produkt";
						$headline[1]="Zeit";
						$headline[2]="Institution";
						$headline[3]="Anzahl Kinder";
						$headline[4]="";
						$headline[5]="";
						$sorting_1=$sorting;

						/* spezial Sortierung f￿r Kurse */
						if ($sort1=="Y-Achse") 
						{ 
							$sorting1=" f.name asc,a.year $order,e.name $order";
						} else
						{
							$sorting1 = $sorting;
						}
						if ($y=="Camps") {$select_camps=" and e.id=5 "; }
						$sql_statistic = "select 
								a.id, 
								f.name,
								concat(a.year,'-',e.name),
								g.name,
								count(distinct b.clients_id),
								h.name
							from 	courses a, 
									payments b, 
									clients d,products f,timeperiods e,institutions g, kursinfo h
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.products_id=f.id and
									a.timeperiods_id=e.id and
									a.institutions_id=g.id and
									h.id=a.type
                                                                       $select_camps
							group by a.id,a.products_id,a.timeperiods_id,a.institutions_id
							order by $sorting1,g.name";
						$x_achse = "Semester";
						break;
					}

					case "Kurse":
					{ /* NUR EINE KOPIE VON SEMESTER/KURSE, da nach Wochentage keinen Sinn macht */
						$headline[0]="Produkt";
						$headline[1]="Zeit";
						$headline[2]="Institution";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Kursinfo";
						$headline[5]="Trainer";
						$sorting_1=$sorting;

						/* spezial Sortierung f￿r Kurse */
						if ($sort1=="Y-Achse") 
						{ 
							$sorting1=" f.name asc,a.year $order,e.name $order";
						} else
						{
							$sorting1 = $sorting;
						}
						if ($y=="Camps") {$select_camps=" and e.id=5 "; }
						$sql_statistic = "select 
								a.id, 
								f.name,
								concat(a.year,'-',e.name),
								g.name,
								count(distinct b.clients_id),
								h.name,
								concat(i.lastname,' ',i.firstname) 
							from 	courses a, 
									payments b, 
									clients d,products f,timeperiods e,institutions g, kursinfo h, employees i
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.products_id=f.id and
									a.timeperiods_id=e.id and
									a.institutions_id=g.id and
									h.id=a.type and
									i.id=a.standard_employee
                                                                       $select_camps
							group by a.id,a.products_id,a.timeperiods_id,a.institutions_id
							order by $sorting1,g.name";
						$x_achse = "Semester";
						break;
					}
					case "Schule":
					{
						$headline[0]="Institution";
						$headline[1]="Jahr";
						$headline[2]="Tag";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Anzahl Kurse";
						$headline[5]="Trainer";
						$sql_statistic = "select 
								a.id, 
								e.name,
								a.year,
								c.weekday,
								count(distinct b.clients_id),
								e.id,
								concat(f.lastname,' ',f.firstname)
							from 	courses a, 
									payments b, 
									weekdays c, 
									clients d, 
									institutions e,
									employees f 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.institutions_id=e.id and 
									c.id=a.weekday and
									a.standard_employee=f.id
							group by e.id,a.year,a.weekday
							order by $sorting";
							break;						
					}
					case "Sportart":
					{
						$headline[0]="Produkt";
						$headline[1]="Jahr";
						$headline[2]="Tag";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Anzahl Kurse";
						$sorting_1=$sorting;

						$sql_statistic = "select
								a.id, 
								e.name,
								a.year,
								c.weekday,
								count(distinct b.clients_id),
								e.id 
							from 	courses a, 
									payments b, 
									weekdays c, 
									clients d, 
									products e 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									e.id=a.products_id and 
									c.id=a.weekday 
							group by a.year,a.weekday,a.products_id
							order by $sorting";						
							break;
					}
					case "Trainer":
					{
						$headline[0]="Trainer";
						$headline[1]="Jahr";
						$headline[2]="Tag";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Anzahl Kurse";
						$sql_statistic = "select 
								a.id, 
								concat(e.lastname,' ',e.firstname),
								a.year,
								c.weekday,
								count(distinct b.clients_id),
								e.id 
							from 	courses a, 
									payments b,
									weekdays c, 
									clients d, 
									employees e 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									e.status!='Entfernt' and 
									a.standard_employee=e.id and
									c.id=a.weekday 
							group by e.id,a.year,a.weekday
							order by $sorting";
							break;						
					}
				}
				break;
			}

/* Pro schule
 * select a.id, a.year,c.weekday,e.name,count(b.clients_id) from courses a, payments b, weekdays c, clients d, institutions e where b.courses_id=a.id and a.status!='Entfernt' and b.status!='Entfernt' and d.id=b.clients_id and d.status!='Entfernt' and a.institutions_id=e.id and c.id=a.weekday group by e.id,a.year,a.weekday
 * 
 * Pro Sportart
 * select a.id, a.year,c.weekday,e.name,count(b.clients_id) from courses a, payments b, weekdays c, clients d, products e where b.courses_id=a.id and a.status!='Entfernt' and b.status!='Entfernt' and d.id=b.clients_id and d.status!='Entfernt' and e.id=a.products_id and c.id=a.weekday group by a.year,a.timeperiods_id,a.products_id
 */			

		case "Semester":
		{
				switch($y)
				{
					case "Gesamt":
					{
						$headline[0]="Jahr";
						$headline[1]="Semester";
						$headline[2]="Anzahl Kinder";
						$headline[3]="Anzahl Kurse";
						// SQL Abfrage
						$sql_statistic = "select 
								a.id, 
								a.year,
								c.name,
								count(distinct b.clients_id) 
							from 	courses a, 
									payments b, 
									timeperiods c, 
									clients d 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									c.id=a.timeperiods_id 
							group by a.year,a.timeperiods_id
							order by a.year $order";
							break;
					}
                    case "Camps":
					{ /* ORIGINAL */
						$headline[0]="Produkt";
						$headline[1]="Zeit";
						$headline[2]="Institution";
						$headline[3]="Anzahl Kinder";
						$headline[4]="";
						$headline[5]="";
						$sorting_1=$sorting;

						/* spezial Sortierung f￿r Kurse */
						if ($sort1=="Y-Achse") 
						{ 
							$sorting1=" f.name asc,a.year $order,e.name $order";
						} else
						{
							$sorting1 = $sorting;
						}
						if ($y=="Camps") {$select_camps=" and e.id=5 "; }
						
						$sql_statistic = "select 
								a.id, 
								f.name,
								concat(a.year,'-',e.name),
								g.name,
								count(distinct b.clients_id),
								h.name
							from 	courses a, 
									payments b, 
									clients d,products f,timeperiods e,institutions g, kursinfo h
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.products_id=f.id and
									a.timeperiods_id=e.id and
									a.institutions_id=g.id and
									h.id=a.type
                                                                       $select_camps
							group by a.id,a.products_id,a.timeperiods_id,a.institutions_id
							order by $sorting1,g.name";
						$x_achse = "Semester";
						break;
					}

					case "Kurse":
					{ /* ORIGINAL */
						$headline[0]="Produkt";
						$headline[1]="Zeit";
						$headline[2]="Institution";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Kursinfo";
						$headline[5]="Trainer";
						$sorting_1=$sorting;

						/* spezial Sortierung f￿r Kurse */
						if ($sort1=="Y-Achse") 
						{ 
							$sorting1=" f.name asc,a.year $order,e.name $order";
						} else
						{
							$sorting1 = $sorting;
						}
						if ($y=="Camps") {$select_camps=" and e.id=5 "; }
						
						$sql_statistic = "select 
								a.id, 
								f.name,
								concat(a.year,'-',e.name),
								g.name,
								count(distinct b.clients_id),
								h.name,
								concat(i.lastname,' ',i.firstname) 
							from 	courses a, 
									payments b, 
									clients d,products f,timeperiods e,institutions g, kursinfo h, employees i
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.products_id=f.id and
									a.timeperiods_id=e.id and
									a.institutions_id=g.id and
									h.id=a.type and
									i.id=a.standard_employee
                                                                       $select_camps
							group by a.id,a.products_id,a.timeperiods_id,a.institutions_id
							order by $sorting1,g.name";
						$x_achse = "Semester";
						break;
					}
					case "Sportart":
					{
						$headline[0]="Produkt";
						$headline[1]="Jahr";
						$headline[2]="Tag";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Anzahl Kurse";
						$sql_statistic = "select
										a.id, 
										e.name,
										a.year,
										c.name,
										count(distinct b.clients_id),
										e.id 
									from 	courses a, 
											payments b, 
											timeperiods c, 
											clients d, 
											products e 
									where 	b.courses_id=a.id and 
											a.status!='Entfernt' and 
											b.status!='Entfernt' and 
											d.id=b.clients_id and
											d.status!='Entfernt' and 
											e.id=a.products_id and 
											c.id=a.timeperiods_id 
									group by a.year,a.timeperiods_id,a.products_id
									order by $sorting";						
									break;
					}
					case "Schule":
					{
						$headline[0]="Institution";
						$headline[1]="Jahr";
						$headline[2]="Semester";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Anzahl Kurse";
						$headline[5]="Trainer";
						$sql_statistic = "select 
								a.id, 
								e.name,
								a.year,
								c.name,
								count(distinct b.clients_id),
								e.id,
								concat(f.lastname,' ',f.firstname)
							from 	courses a, 
									payments b, 
									timeperiods c, 
									clients d, 
									institutions e,
									employees f 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.institutions_id=e.id and 
									c.id=a.timeperiods_id and
									f.id=a.standard_employee 
							group by e.id,a.year,a.timeperiods_id
							order by $sorting";
							break;						
					}
					case "Trainer":
					{
						$headline[0]="Trainer";
						$headline[1]="Jahr";
						$headline[2]="Tag";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Anzahl Kurse";
						$sql_statistic = "select 
								a.id, 
								concat(e.lastname,' ',e.firstname),
								a.year,
								c.name,
								count(distinct b.clients_id),
								e.id 
							from 	courses a, 
									payments b,
									timeperiods c, 
									clients d, 
									employees e 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									e.status!='Entfernt' and 
									a.standard_employee=e.id and
									c.id=a.timeperiods_id 
							group by e.id,a.year,a.timeperiods_id						
							order by $sorting";
							break;						
					}
				}
				break;
		}

		case "Jahre":
		{
				switch($y)
				{
					case "Gesamt":
					{
						$headline[0]="Jahr";
						$headline[1]="Anzahl Kinder";
						$headline[3]="Anzahl Kurse";
						// SQL Abfrage
						$sql_statistic = "select 
								a.id, 
								a.year,
								count(distinct b.clients_id) 
							from 	courses a, 
									payments b, 
									clients d 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt'
							group by a.year
							order by a.year $order";
							break;
					}
                                        case "Camps":
					{
						$headline[0]="Produkt";
						$headline[1]="Zeit";
						$headline[2]="Institution";
						$headline[3]="Anzahl Kinder";
						$headline[4]="";
						$headline[5]="";
						$sorting_1=$sorting;

						/* spezial Sortierung f￿r Kurse */
						if ($sort1=="Y-Achse") 
						{ 
							$sorting1=" f.name asc,a.year $order,e.name $order";
						} else
						{
							$sorting1 = $sorting;
						}
						
						if ($y=="Camps") {$select_camps=" and e.id=5 "; }

						$sql_statistic = "select 
								a.id, 
								f.name,
								concat(a.year,'-',e.name),
								g.name,
								count(distinct b.clients_id),
								h.name
							from 	courses a, 
									payments b, 
									clients d,products f,timeperiods e,institutions g, kursinfo h
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.products_id=f.id and
									a.timeperiods_id=e.id and
									a.institutions_id=g.id and
									h.id=a.type
                                                                       $select_camps
							group by a.id,a.products_id,a.timeperiods_id,a.institutions_id
							order by $sorting1,g.name";
						$x_achse = "Semester";
						break;
					}

					case "Kurse":
					{
						$headline[0]="Produkt";
						$headline[1]="Zeit";
						$headline[2]="Institution";
						$headline[3]="Anzahl Kinder";
						$headline[4]="Kursinfo";
						$headline[5]="Trainer";
						$sorting_1=$sorting;

						/* spezial Sortierung f￿r Kurse */
						if ($sort1=="Y-Achse") 
						{ 
							$sorting1=" f.name asc,a.year $order,e.name $order";
						} else
						{
							$sorting1 = $sorting;
						}
						
						if ($y=="Camps") {$select_camps=" and e.id=5 "; }

						$sql_statistic = "select 
								a.id, 
								f.name,
								concat(a.year,'-',e.name),
								g.name,
								count(distinct b.clients_id),
								h.name,
								concat(i.lastname,' ',i.firstname) 
							from 	courses a, 
									payments b, 
									clients d,products f,timeperiods e,institutions g, kursinfo h, employees i
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.products_id=f.id and
									a.timeperiods_id=e.id and
									a.institutions_id=g.id and
									h.id=a.type and
									i.id=a.standard_employee
                                                                       $select_camps
							group by a.id,a.products_id,a.timeperiods_id,a.institutions_id
							order by $sorting1,g.name";
						$x_achse = "Semester";
						break;
					}
					case "Sportart":
					{
						$headline[0]="Produkt";
						$headline[1]="Jahr";
						$headline[2]="Anzahl Kinder";
						$headline[3]="";
						$headline[4]="Anzahl Kurse";
						$headline[5]="";
						$sql_statistic = "select
										a.id, 
										e.name,
										a.year,
										count(distinct b.clients_id),
										'',
										e.id 
									from 	courses a, 
											payments b, 
											clients d, 
											products e 
									where 	b.courses_id=a.id and 
											a.status!='Entfernt' and 
											b.status!='Entfernt' and 
											d.id=b.clients_id and
											d.status!='Entfernt' and 
											e.id=a.products_id 
									group by a.year,a.products_id
									order by $sorting";						
									break;
					}
					case "Schule":
					{
						$headline[0]="Institution";
						$headline[1]="Jahr";
						$headline[2]="Anzahl Kinder";
						$headline[3]="";
						$headline[4]="Anzahl Kurse";
						$headline[5]="Trainer";
						$sql_statistic = "select 
								a.id, 
								e.name,
								a.year,
								count(distinct b.clients_id),
								'',
								e.id,
 								concat(f.lastname,' ',f.firstname)
							from 	courses a, 
									payments b, 
									clients d, 
									institutions e,
									employees f 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									a.institutions_id=e.id and
									f.id=a.standard_employee
							group by e.id,a.year
							order by $sorting";
							break;						
					}
					case "Trainer":
					{
						$headline[0]="Trainer";
						$headline[1]="Jahr";
						$headline[2]="Anzahl Kinder";
						$headline[3]="";
						$headline[4]="Anzahl Kurse";
						$sql_statistic = "select 
								a.id, 
								concat(e.lastname,' ',e.firstname),
								a.year,
								count(distinct b.clients_id),
								'',
								e.id 
							from 	courses a, 
									payments b, 
									clients d, 
									employees e 
							where 	b.courses_id=a.id and 
									a.status!='Entfernt' and 
									b.status!='Entfernt' and 
									d.id=b.clients_id and 
									d.status!='Entfernt' and 
									e.status!='Entfernt' and 
									a.standard_employee=e.id 
							group by e.id,a.year						
							order by $sorting";
							break;						
					}
				}
				break;
				
		}
/* Pro Sportart
 * select a.id, a.year,c.name,e.name,count(b.clients_id) from courses a, payments b, timeperiods c, clients d, products e where b.courses_id=a.id and a.status!='Entfernt' and b.status!='Entfernt' and d.id=b.clients_id and d.status!='Entfernt' and e.id=a.products_id and c.id=a.timeperiods_id group by a.year,a.timeperiods_id,a.products_id
 */
	}
		
		$rs_statistic=getrs($sql_statistic,$sql_view,1);
		$num=$rs_statistic->num_rows;
		$loop=0;
		$html_output="<br><div align=center>Anzahl der Datens&auml;tze: $num</div><br>";
		$html_output.="<TABLE width=500 CELLPADDING=0 CELLSPACING=0 bordercolor=black border=1>
		<tr>";
                      if ($sort1=="Jahre")  
			{
                                 $html_output.="
                                          <td>$headline[1]</td>
                                          <td>$headline[0]</td>";
                         }
                         else
			{
                                 $html_output.="
                                          <td>$headline[0]</td>
                                          <td>$headline[1]</td>";
                         }
                      $html_output.=" <td>$headline[2]</td>
                      <td>$headline[3]</td>";
		if ($y!="Camps")
		{    
			$html_output.="<td>$headline[4]</td>"; 
		}
		if (($y=="Kurse") || ($y=="Schule")) 
		{ 
			$html_output.="<td>$headline[5]</td>"; 
		}
		
		$html_output.="</tr>";

		while($stat_result=$rs_statistic->fetch_row())
		{
			if (($y!="Kurse") && ($y!="Camps"))
			{
				switch($x)
				{
					case "Wochentage":
						{
							$filter_x="c.weekday";
							$group_x="a.year,a.weekday";
							break;
						}
					case "Semester":
						{
							$filter_x="d.name";
							$group_x="a.year,a.timeperiods_id";
							break;
						}
					case "Jahre":
						{
							$filter_x="";
							$group_x="a.year";
							break;
						}
				}
				switch($y)
				{
					case "Gesamt":
					{
						$sorting_1=" a.year ".$order;
						$specific_where=" a.year='$stat_result[1]'";
						if ($filter_x!="")
						{
							$specific_where.=" and ".$filter_x."='$stat_result[2]'";
						}
						$specific_group=$group_x;
						break;
					}
					case "Schule":
					{
						$sorting_1=$sorting;
						$specific_where=" e.id='$stat_result[5]' and a.year='$stat_result[2]' and e.id=a.institutions_id ";
						if ($filter_x!="")
						{
							$specific_where.=" and ".$filter_x."='$stat_result[3]'";
						}
						$specific_from=",institutions e";
						$specific_group="e.id,".$group_x;
						break;
					}
					case "Sportart":
					{
						$sorting_1=$sorting;
						$specific_where=" e.id='$stat_result[5]' and a.year='$stat_result[2]' and e.id=a.products_id ";
						if ($filter_x!="")
						{
							$specific_where.=" and ".$filter_x."='$stat_result[3]'"; 	
						}
						$specific_from=",products e";
						$specific_group=$group_x.",e.id";
						break;
					}
					case "Trainer":
					{
						$sorting_1=$sorting;
						$specific_where=" e.id='$stat_result[5]' and a.year='$stat_result[2]' and a.standard_employee = e.id";
						if ($filter_x!="")
						{
							$specific_where.=" and ".$filter_x."='$stat_result[3]'"; 	
						}
						$specific_from=",employees e";
						$specific_group=$group_x.",e.id";
						break;
					}

				}
				$count_courses="select 
						count(distinct a.id) 
				from 
						courses a, 
						weekdays c, 
						timeperiods d
						$specific_from
				where 	a.status!='Entfernt' and 
						c.id=a.weekday and 
						a.timeperiods_id=d.id and
						$specific_where
				group by $specific_group order by $sorting_1";
				$rs_stat_courses=getrs($count_courses,$sql_view,1);
				$stat_cours=$rs_stat_courses->fetch_row();
			}
			$html_output.="
				<tr>";
                                if ($sort1=="Jahre")  
				{
                                 $html_output.="
				<td align=center>$stat_result[2]</td> <!-- Jahr -->
                                <td align=center>$stat_result[1]</td> ";
                                }
				else
				{
                                 $html_output.="
                                <td align=center>$stat_result[1]</td> 
				<td align=center>$stat_result[2]</td> <!-- Jahr -->";
                                }
                                $html_output.="
                                <td align=center>$stat_result[3]</td> <!-- Anzahl Kinder -->";
				
                                if ($y!="Gesamt") { $html_output.="<td align=center>$stat_result[4]</td> <!-- Anzahl Kinder -->"; }
				if (($y!="Kurse") && ($y!="Camps")) { $html_output.="<td align=center>$stat_cours[0]</td> <!-- Anzahl Kurse -->"; }
				if ($y=="Kurse") { 
					$html_output.="
						<td align=center>$stat_result[5]</td> <!-- Kursinfo -->
						<td align=center>$stat_result[6]</td> <!-- Trainer -->
						"; }
				if ($y=="Schule") { 
					$html_output.="
						<td align=center>$stat_result[6]</td> <!-- Trainer -->
						"; }
				$html_output.="</tr>";
		}

return $html_output;

}

// Body of this php file

if ($nb==0)
{
    statistic_report ($x_achse,$y_achse,$sort1,$sort2,$sort3,$order,$sql_view);
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
<SPAN class="headline">Statistiken</SPAN>
</td></tr>
</table>


<FORM action="statistic_report.php" method="POST" name=formular>

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
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Zeitangaben (x-Achse)</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
			<select name="x_achse" class="input_text">
				<option class="input_text" <?if ($x_achse=="Wochentage") {echo " selected ";}?>value="Wochentage" <?print($selected)?>>Wochentage</option>
				<option class="input_text" value="Semester" <?if ($x_achse=="Semester") {echo " selected ";}?>>Semester</option>
				<option class="input_text" value="Jahre" <?if ($x_achse=="Jahre") {echo " selected ";}?>>Jahre</option>
			</select>

   </TD></TR></TABLE></TD>
</TR>

<tr>

   <td colspan=3 HEIGHT=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>

</TR>

<TR>
   <TD width=150 ALIGN=RIGHT   class=form_header><TABLE><TR><TD WIDTH=100% HEIGHT=100%>Art (y-Achse)</TD></TR></TABLE></TD>
   <td WIDTH=1 class=news_border><IMG SRC="../../images/black.gif" HEIGHT=1 WIDTH=1></TD>
   <TD width=250 class=form_row><TABLE><TR><TD WIDTH=100% HEIGHT=100%>
			<select name="y_achse" class="input_text">
				<option class="input_text" <?if ($y_achse=="Gesamt") {echo " selected ";}?> value="Gesamt" <?print($selected)?>>Gesamt</option>
				<option class="input_text" value="Kurse" <?if ($y_achse=="Kurse") {echo " selected ";}?>>pro Kurse</option>
				<option class="input_text" value="Camps" <?if ($y_achse=="Camps") {echo " selected ";}?>>pro Camps</option>
				<option class="input_text" value="Schule" <?if ($y_achse=="Schule") {echo " selected ";}?>>pro Schule</option>
				<option class="input_text" value="Sportart" <?if ($y_achse=="Sportart") {echo " selected ";}?>>pro Sportart</option>
				<option class="input_text" value="Trainer" <?if ($y_achse=="Trainer") {echo " selected ";}?>>pro Trainer</option>
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

   <select name='sort1'>

   	<option value="Y-Achse" <?if ($sort1=="Y-Achse") echo "selected";?>>Y-Achse</option>
   	<option value="Jahre" <?if ($sort1=="Jahre") echo "selected";?>>Jahre</option>

   </select>

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
						<? if (!isset($sql_view)) $sql_view=0; ?>
						<input type=hidden name=sql_view value=<?print($sql_view);?>>
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

	
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<link href="../css/ta.css" type="text/css" rel="stylesheet">

<html>
<head>
</head>
<body>
<?
require_once("../include/session.php");
	$gene=1;
	$ergebnis1 = getrs("SELECT id,username,title,lastname,firstname,email,password FROM employees",1,1);
	while($row = mysqli_fetch_object($ergebnis1)) 
	{
		$passwd = crypt($row->password,"activities");
		$ergebnis2 = getrs("UPDATE employees set password='".$passwd."' WHERE id=".$row->id,1,1);
	}
	echo mysql_error();
?>
</body>
</html>
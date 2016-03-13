<?

include("littlefunctions.php");

require_once("../../include/session.php");

require_once("../../include/html.php");

$employee=$_POST["employee"];
$payment_month=$_POST["payment_month"];
$payment_year=$_POST["payment_year"];
$viewsoz=$_POST["viewsoz"];

?>

<style type="text/css">

.pb {

        page-break-after: avoid;

        height: 25.5 cm;

}

</style>

<?

for ($i=0;$i<sizeof($employee);$i++)

{
        echo "<html><head></head><link rel='stylesheet' href='../../css/ta.css'><body>";

        $html_output ="";

        MonthlyCalculation($employee[$i],$payment_month,$payment_year,$viewsoz);

        echo "<p class=pb>";

        echo $html_output;

        echo "</p>";

        echo "</body></html>";

}

?>

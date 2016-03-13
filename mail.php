<?php
$empfaenger = "riegler.ewald@gmx.net";
$betreff = "Die Mail-Funktion";
$from = "From: Camp Teamactivities <camp@teamactivities.at>\n";
$from .= "Reply-To: antwort@domain.de\n";
$from .= "Content-Type: text/html\n";
$text = "Dear Mr. Gashi,<br>
<br>
Thank you for booking our “GERMAN ACTIVE” Language Course Holidays Summer Camp !<br>
Attached you find the booking confirmation for Uke as well as our brochure “Tips and<br>information” in German and English.<br>
<br>
We wish Uke a lot of fun at our Language Course Holidays Summer Camp !<br>
If you have any questions please don’t hesitate to contact me.<br>
<br>
Best regards from Vienna,<br>
<br>
Boris Duniecki<br>
<font size-1>Camps Coordinator</font><br>
<br>
Team Activities<br>
Stachegasse 17<br>
1120 Wien / Vienna<br>
AUSTRIA<br>
Tel./Fax: +43 1 786 67 39<br>
<a href=mailto:camps@teamactivities.at>camps@teamactivities.at</a><br>
www.teamactivities.at<br>
<br>";
mail($empfaenger, $betreff, htmlentities($text), $from);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/RockTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Student Drills</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="Newstyles.css" rel="stylesheet" type="text/css" />
<link href="Sty.css" rel="stylesheet" type="text/css" />
<link href="Styles23.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
	background-color: #000;
}
.LinkImage {
	width: 0px;
}
a:link {
	color: #FBF920;
}
a:visited {
	color: #D5482C;
}
a:active {
	color: #D5402C;
}
-->
</style></head>


<body onLoad="document.answerForm.myAnswer.focus()">
<div id="header"><a href="index.html"><img src="images/MYlogo.png" alt="RockLogo" width="211" height="67" border="0" /></a></div>
<div id="menu"><a href="index.html"><img src="images/RockHome.jpg" alt="Home" width="95" height="32" border="0" /></a><a href="about-us.html"><img src="images/RockAB.jpg" alt="About Us" width="95" height="32" border="0" /></a><a href="teachers.html"><img src="images/ForTeach.jpg" alt="For Teachers" width="95" height="32" border="0" /></a><a href="parents.html"><img src="images/ForPnts.jpg" alt="For Parents" width="95" height="32" border="0" /></a><a href="students.html"><img src="images/ForStdts.jpg" alt="For Students" width="95" height="32" border="0" /></a><a href="teacherLogin.php"><img src="images/TeachLogin.jpg" alt="Teachers (:" width="95" height="32" border="0" /></a><a href="studentLogin.php"><img src="images/StudLogin.jpg" alt="Students (:" width="95" height="32" border="0" /></a></div>
<div id="middle">
  <p>&nbsp;</p>
  <div class="bodytext" id="submenu">
    <p>&nbsp;</p>
  </div>
  <div class="bodytext" id="content"><!-- InstanceBeginEditable name="Content" -->
  <?php
require 'arithClasses.inc';
require 'dbConnect.inc';

error_reporting(E_ALL);
require 'sessionStart.inc';

$db = dbConnect();

$studentFirstName = $_GET['sfn'];
$studentLastName = $_GET['sln'];
$studentID = $_GET['sid'];
printf ("<h1>Drills for %s %s</h1>", $studentFirstName, $studentLastName);
// Get drills for this student
$sid = $db->real_escape_string($studentID);
$queryStr = sprintf("SELECT * FROM drill where studentID = %d ORDER BY ts DESC", $sid);
$result = $db->query($queryStr);
//var_dump($result);

if ($result->num_rows == 0) {
  echo "<p>No drills found for this student.<br><p><a href='teacherHome.php'>Back to Teacher Home</a><br/>";
  $result->close();
  $db->close();
  exit();
}
?>

<table>
<table border="2" cellpadding="3">
<tr>
<th>Date/Time</th>
<th>Duration</th>
<th>#Right</th>
<th>#Total</th>
<th>%Right</th>
<th></th>
</tr>
<?php
while ($aDrill = $result->fetch_object("Drill")) {
  printf("<tr><td>%s</td>\n", substr($aDrill->ts, 0, 16));
  $dDuration = $aDrill->duration;
  if ($dDuration == 0) {
    printf("<td>Incomplete</td>\n");
  } else {
    printf("<td>%s</td>\n", date('i:s', $dDuration));
  }
  $numCorrect = $aDrill->getNumCorrectAnswers($aDrill->did, $db);
  printf("<td>%d</td>\n", $numCorrect);
  $numTotal = $aDrill->getNumTotalAnswers($aDrill->did, $db);
  printf("<td>%d</td>\n", $numTotal);
  $pctCorrect = round(($numCorrect / $numTotal * 100), 1);
  echo '<td>' . $pctCorrect . '%</td>';
  printf("<td><a href='drillDetails.php?did=%d&sid=%s&sfn=%s&sln=%s&cor=%d&tot=%d'>Details</a></td></tr>\n", $aDrill->did, $studentID, $studentFirstName, $studentLastName, $numCorrect, $numTotal);
}

$result->close();
printf("</table>");
printf("<p><a href='teacherHome.php'>Back to Teacher Home</a><br/>");
printf("<p><a href='teacherLogout.php'>Logout</a>\n");

$db->close();
?><!-- InstanceEndEditable --></div>
</div>
<div id="footer">Thanks for visiting Multiplcation Rocks -- &copy;  2009 Audra Barton</div>
</body>
<!-- InstanceEnd --></html>

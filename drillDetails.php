<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/RockTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Student Drill Details</title>
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
  <p class="bodytext">&nbsp;</p>
  <?php
require 'arithClasses.inc';
require 'dbConnect.inc';

error_reporting(E_ALL);
require 'sessionStart.inc';
$aTeacher = $_SESSION['teacher'];

$studentFirstName = $_GET['sfn'];
$studentLastName = $_GET['sln'];
$studentID = $_GET['sid'];
$drillID = $_GET['did'];
$numCorrect = $_GET['cor'];
$numTotal = $_GET['tot'];

$db = dbConnect();

printf ("<h1>Drill Details for %s %s</h1>", $studentFirstName, $studentLastName);
// Get this particular drill
$queryStr = sprintf("SELECT * FROM drill where did = %d", $drillID);
$result = $db->query($queryStr);
if ($result->num_rows == 0) {
  echo "<p>No drill found.<br><p><a href='teacherHome.php'>Back to Teacher Home</a><br/>";
  $_SESSION['teacher'] = $aTeacher;
  $result->close();
  $db->close();
  exit();
}
$aDrill = $result->fetch_object("Drill");
printf("<h3>Time Started:<br>%s</h3>\n", substr($aDrill->ts, 0, 16));
printf("<h3>Duration: %s</h3>\n", date('i:s', $aDrill->duration));
$pctCorrect = round(($numCorrect / $numTotal * 100), 1);
echo '<h3>' . $numCorrect . ' of ' . $numTotal . ' Correct (' . $pctCorrect . '%)</h3>';

// Get incorrect answers for this drill
$queryStr = sprintf("SELECT * FROM problem WHERE drillID = %d AND answer != userAnswer ORDER BY operator, op2, op1 ASC", $drillID);
$result = $db->query($queryStr);
if ($result->num_rows == 0) {
  echo "<h4>No Incorrect Answers</h4>";
 } else {
  echo "<h4>" . $result->num_rows . " Incorrect Answers</h4>";
  echo '<table border="2" cellpadding="3">';
  printf("<tr><th>Problem</th><th>Student Answer</th></tr>\n");
 }
while ($aProb = $result->fetch_object("DBProblem")) {
  echo $aProb->asStringWithHTMLUserAnswer();
}
echo '</table>';
$result->close();

// Get correct answers for this drill
$queryStr = sprintf("SELECT * FROM problem WHERE drillID = %d AND answer = userAnswer ORDER BY operator, op2, op1 ASC", $drillID);
$result = $db->query($queryStr);
if ($result->num_rows == 0) {
  echo "<h4>No Correct Answers</h4>";
} else {
  echo "<h4>" . $result->num_rows . " Correct Answers</h4>";
}
while ($aProb = $result->fetch_object("DBProblem")) {
  echo $aProb->asString() . '<br>';
}
$result->close();

printf("<p><a href='studentDrills.php?sid=%d&sfn=%s&sln=%s'>Back to Drills for %s %s</a><br/>", $studentID, $studentFirstName, $studentLastName, $studentFirstName, $studentLastName);
printf("<p><a href='teacherHome.php'>Back to Teacher Home</a><br/>");
printf("<p><a href='teacherLogout.php'>Logout</a>\n");
$_SESSION['teacher'] = $aTeacher;

$db->close();
?><!-- InstanceEndEditable --></div>
</div>
<div id="footer">Thanks for visiting Multiplcation Rocks -- &copy;  2009 Audra Barton</div>
</body>
<!-- InstanceEnd --></html>

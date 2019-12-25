<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/RockTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>MultiplicationRocks Teacher Home</title>
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
  <h1>MultiplicationRocks Teacher Home</h1>

<?php
require 'arithClasses.inc';
require 'dbConnect.inc';

error_reporting(E_ALL);
require 'sessionStart.inc';
$db = dbConnect();

  $aTeacher = NULL;
  if (empty($_SESSION['teacher'])) { // Teacher is not logged in yet
    $loggedIn = TRUE;
    if (isset($_POST['email'], $_POST['password'])) {
      $email = $db->real_escape_string($_POST['email']);
      $password = $db->real_escape_string($_POST['password']);
      $queryStr = sprintf("SELECT * FROM teacher where email = '%s' AND password = '%s'", $email, $password);
      $result = $db->query($queryStr);
      if ($result->num_rows == 0) {
        $loggedIn = FALSE;
        $result->close();
        $db->close();
      }
    } else {
      $loggedIn = FALSE;
    }
    if (!$loggedIn) {
      echo "Username/Password combination not found or your session has expired.<p><a href='teacherLogin.php'>Try Again</a><br/>";
      exit();
    }

    $aTeacher = $result->fetch_object("Teacher");

    $result->close();
    $_SESSION['teacher'] = $aTeacher;

  } else { // Teacher is logged in
    $aTeacher = $_SESSION['teacher'];

  }

//var_dump($aTeacher);
printf("<h2>%s %s</h2>\n", $aTeacher->firstName, $aTeacher->lastName);
printf("%s<br>\n", $aTeacher->email);
printf("%s<p>\n", $aTeacher->school);

// Get students for this teacher
$email = $db->real_escape_string($aTeacher->email);
$queryStr = sprintf("SELECT * FROM student where teacherEmail = '%s' ORDER BY lastName, firstName", $email);
$result = $db->query($queryStr);
//echo 'good query result numRows = ' . $result->num_rows . '<br>';
//var_dump($result);

if ($result->num_rows == 0) {
  echo "No students found<p><a href='studentCreate.php'>Add Student</a><br/>";
  printf("<p><a href='teacherLogout.php'>Logout</a>\n");
  $result->close();
  $db->close();
  exit();
}
?>

<h3>Students</h3>
<table border="7" cellpadding="10">
<tr>
<th>UserName</th>
<th>Student Name</th>
<th>Password</th>
<th>Points</th>
<th></th>
<th></th>
</tr>
<?php
while ($aStudent = $result->fetch_object("Student")) {
  //var_dump($aStudent);
  printf("<tr><td>%s</td>\n", $aStudent->userName);
  printf("<td>%s %s</td>\n", $aStudent->firstName, $aStudent->lastName);
  printf("<td>%s</td>\n", $aStudent->password);
  printf("<td>%d</td>\n", $aStudent->points);
  printf("<td><a href='studentDrills.php?sid=%d&sfn=%s&sln=%s'>Drills</a></td>\n", $aStudent->sid, $aStudent->firstName, $aStudent->lastName);
  printf("<td><a href='studentRemove.php?sid=%d'>Remove</a></td></tr>\n", $aStudent->sid);
}
printf("</table>");

echo "<p><a href='studentCreate.php'>Add Student</a>";
printf("<p><a href='classStandingsTeacher.php?tid=%s&sid=0'>Class Point Standings</a>\n", $aTeacher->email);
printf("<p><a href='teacherLogout.php'>Logout</a>\n");

$result->close();
$db->close();
?><!-- InstanceEndEditable --></div>
</div>
<div id="footer">Thanks for visiting Multiplcation Rocks -- &copy;  2009 Audra Barton</div>
</body>
<!-- InstanceEnd --></html>

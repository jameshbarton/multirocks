<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/RockTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>MultiplicationRocks Student Home</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
a:link {
	color: #FBF920;
}
a:visited {
	color: #D43B2D;
}
a:hover {
	color: #D6D6D6;
}
a:active {
	color: #D4472C;
}
-->
</style><!-- InstanceEndEditable -->
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
  <h1>MultiplicationRocks Student Home</h1>
  <p>
    <?php
require 'arithClasses.inc';
require 'dbConnect.inc';

error_reporting(E_ALL);
require 'sessionStart.inc';

  $db = dbConnect();

  $aTeacher = NULL;
  unset($_SESSION['drill']);

  if (empty($_SESSION['student'])) { // Student is not logged in yet
    $loggedIn = TRUE;
    if (isset($_POST['username'], $_POST['password'])) {
      $username = $db->real_escape_string($_POST['username']);
      $password = $db->real_escape_string($_POST['password']);
      $queryStr = sprintf("SELECT * FROM student where userName = '%s' AND password = '%s'", $username, $password);
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
      echo "Username/Password combination not found or your session has expired. <p><a href='studentLogin.php'>Try Again</a><br/>";
      exit();
    }

    $aStudent = $result->fetch_object("Student");
    $result->close();
    $_SESSION['student'] = $aStudent;

  } else { // Student is logged in
    $aStudent = $_SESSION['student'];
    // refresh volatile values
    $queryStr = sprintf("SELECT * FROM student where userName = '%s'", $aStudent->userName);
    $result = $db->query($queryStr);
    if ($result->num_rows == 0) {
      echo "Student not found. <br><a href='studentLogin.php'>Login Again</a><br/>";
      $result->close();
      $db->close();
      exit();
    }
    $aStudent = $result->fetch_object("Student");
  }

  //var_dump($aStudent);
  printf("<h2>%s %s</h2>\n", $aStudent->firstName, $aStudent->lastName);
  printf("<p>You've Earned %d Points<br>\n", $aStudent->points);
  printf("You Are Ranked #%d In Your Class<br>\n", getStudentRankings($aStudent->teacherEmail, $aStudent->sid, FALSE, $db));
  printf("<a href='classStandingsStudent.php?tid=%s&sid=%s'>Full Class Standings</a>\n", $aStudent->teacherEmail, $aStudent->sid);

  printf("<p>Achievement Level: %s<br>\n", $aStudent->getAchievementLevel());
  $nextLevelPoints = $aStudent->pointsToNextLevel();
  if ($nextLevelPoints > 0) {
    printf("You need %d points to reach the %s level.</p>\n", $nextLevelPoints, $aStudent->nextAchievementLevel());
  }
$db->close();
?>
  </p>
  <p>
    <a href='mrDrill.php?op=multiply'>Take a Multiplication Proficiency Test</a>
    <br/>
    <a href='mrDrill.php?op=divide'>Take a Division Proficiency Test</a>
    <br/>
    <a href='mrDrill.php?op=random'>Take an All-Operations Proficiency Test</a>
</p>

<p>
<h2>Or Make Your Own Drill</h2>
<form action="mrDrill.php?custom=Y" method="post">
Number of Problems:<span class="BlackText">llllll</span>
<input type="text" name="numProblems" value="90"/>
  <br />
  <br/>
Operations:
<span class="BlackText">llllllllllllllllllllllll</span>
<select name="drillOperation">
  <option value="random">All Operations</option>
  <option value="multiply">Multiply</option>
  <option value="divide">Divide</option>
</select>
<br />
<br/>
Minimum Factor:<span class="BlackText">lllllllllllllll</span>
<input type="text" name="minFactor" value="0"/> (Multiplication/Division Only)
<br />
<br/>
Maximum Factor:<span class="BlackText">llllllllllllll</span>
<input type="text" name="maxFactor" value="12"/> (Multiplication/Division Only)
<br />
<br/>
<input type="submit" value="Start the Drill" />
</form>

<p><a href='studentLogout.php'>Logout</a></p><!-- InstanceEndEditable --></div>
</div>
<div id="footer">Thanks for visiting Multiplcation Rocks -- &copy;  2009 Audra Barton</div>
</body>
<!-- InstanceEnd --></html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/RockTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Your Class Standings</title>
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
    <h1>Your Class Standings        </h1>
<p>
      <?php
require 'arithClasses.inc';
require 'dbConnect.inc';

error_reporting(E_ALL);
require 'sessionStart.inc';

$db = dbConnect();

getStudentRankings($_GET['tid'], $_GET['sid'], TRUE, $db);

$db->close();
?>
    </p>
    <p>&nbsp;</p>
<p><a href='studentHome.php'>Back to Student Home</a>
<p><a href='studentLogout.php'>Logout</a>
<p>
<p>
<p>
<p>
<p>
  <!-- InstanceEndEditable --></div>
</div>
<div id="footer">Thanks for visiting Multiplcation Rocks -- &copy;  2009 Audra Barton</div>
</body>
<!-- InstanceEnd --></html>

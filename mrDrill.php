<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/RockTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Arithmetic Drill</title>
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
  <p>&nbsp;    </p>
  <p>
    <?php 
  require 'arithClasses.inc';
  require 'dbConnect.inc';

  error_reporting(E_ALL);
  require 'sessionStart.inc';
  $aStudent = $_SESSION['student'];

  $db = dbConnect();

$correctResponses = array("You're Right!", "Correct!", "You Rock, Dude!", "Brilliant!", "Flawless!", "Perfect!", "Very Nice!", "Way to Go!", "Awesome!", "Fantastic!");
  $drill = NULL;
  $lastAnswer = NULL;
  if (empty($_SESSION['drill'])) {
    $numProblems = 90;
    $useDefaultFactors = TRUE;
    $minFactor = 0;
    $maxFactor = 12;
    //get operator
    if (empty($_GET['op'])) {
      $theOperator = "random";
    } else {
      $theOperator = $_GET['op'];
    }
    if (isset($_GET['custom'])) {
      // this is a custom drill
      $theOperator = $_POST['drillOperation'];
      $numProblems = (int)($_POST['numProblems']);
      if ($numProblems <= 0) $numProblems = 90;
      $minFactor = (int)($_POST['minFactor']);
      if ($minFactor <= 0) $minFactor = 0;
      $maxFactor = (int)($_POST['maxFactor']);
      if ($maxFactor <= 0) $maxFactor = 12;
      if ($minFactor > $maxFactor) {
	$minFactor = 0;
        $maxFactor = 12;
      }
      $useDefaultFactors = FALSE;
    }
    $drill = new ArithmeticDrill($aStudent->sid, $theOperator, $numProblems, $useDefaultFactors, $minFactor, $maxFactor, $db);
  } else {
    $drill = $_SESSION['drill'];
    $lastAnswer = (int)$_POST['myAnswer'];
    if ($drill->submitUserAnswer($lastAnswer, $db)) {
      echo "<h1>" . $correctResponses[array_rand($correctResponses)] .  "</h1>";
    } else {
      $aProb = $drill->previousProblem();
      echo "<h1>Sorry! <br>" . $aProb->asStringWithAnswer() . "</h1>";
    }
  }

  if (!$drill->isTestFinished()) {
    if ($drill->getProblemCount() > 0) {
      echo "<h3>You've answered " . $drill->numCorrectAnswers() . " of " . $drill->getProblemCount() . " correctly<br/>";
      $remaining = $drill->numRemainingProblems();
      $probStr = ($remaining == 1) ? " more problem" : " more problems";
      echo $remaining . $probStr . " to go in this drill</h3>";
    }
  } else {
    echo "<h2>You finished with " . $drill->numCorrectAnswers() . " correct answers</h2>";
    $db->close();
    unset($_SESSION['drill']);
    $_SESSION['student'] = $aStudent;
    echo "<p><a href='studentHome.php'>Return to Student Home</a>";
    exit();
  } 

  $prob = $drill->nextProblem();
  //echo 'This is problem #', $drill->getProblemCount(), ' in this drill.';
  $elapsedTime = $drill->timeElapsed();
  if ($elapsedTime > 0) {
    echo "<h3>Elapsed Time " . date('i:s', $elapsedTime)  . "</h3>";
  }
?>
  </p>
  <h1>
    <form id="answerForm" name="answerForm" action="mrDrill.php" method="post">
<?php 
    echo $prob->asString();
?>
      <input type="text" id="myAnswer" name="myAnswer" />
      <input type="submit" value="Answer!" />
      </form>
</h1>

<?php
  $db->close();
  $_SESSION['drill'] = $drill;
?>

<p><a href='studentHome.php'>Back to Student Home</a></p>
<p><a href='studentLogout.php'>Logout</a></p><!-- InstanceEndEditable --></div>
</div>
<div id="footer">Thanks for visiting Multiplcation Rocks -- &copy;  2009 Audra Barton</div>
</body>
<!-- InstanceEnd --></html>

<?php 
class Problem
{
  private static $ops = array("random", "add", "subtract", "multiply", "divide");
  private static $opChar = array("add" => "\x2b", "subtract" => "\x2d", "multiply" => "\xd7", "divide" => "\xf7");
  private static $opImage = array("add" => "images/plus.gif", "subtract" => "images/minus.gif", "multiply" => "images/times.gif", "divide" => "images/divide.gif");
  private static $maxMD = 12; // max operand for division and multiplication
  private static $maxAS1 = 16;
  private static $maxAS2 = 9;
  private static $maxASAnswer = 19;

  private $problemID;
  private $drillID;
  private $studentID;
  private $probMap;

  private function writeToDB($db) {
    $map = $this->probMap;

    $pointString = "- 1";
    if ($this->isAnswerCorrect()) {
      $pointString = "+ 2";
    }
    $queryStr = sprintf("INSERT into problem (op1, op2, operator, answer, userAnswer, drillID) VALUES (%d, %d, '%s', %d, %d, %d)", $map["operand1"], $map["operand2"], $map["operator"], $map["answer"], $map["userAnswer"], $this->drillID);
    if (!$db->query($queryStr)) {
       printf("Failed to create Problem in database");
       exit();
    }
    $this->problemID = $db->insert_id;
    //echo '<br>problem ID ' . $this->problemID;

    $queryStr = sprintf("UPDATE student SET points = points %s WHERE sid = %d", $pointString, $this->studentID);
    if (!$db->query($queryStr)) {
       printf("Failed to update student point total in database");
       exit();
    }
  }

  public function getProblem($did, $sid, $drillOp, $useDefaultFactors, $minFactor, $maxFactor) {
    $this->drillID = $did;
    $this->studentID = $sid;
    $nextOp = $this->nextOperator($drillOp);
    $op1 = 0;
    $op2 = 0;
    $answer = 0;
    if ($nextOp == "divide") {
      $mxF = ($useDefaultFactors) ? self::$maxMD : $maxFactor;
      $mnF2 = ($useDefaultFactors) ? 1 : $minFactor;
      if ($mnF2 <= 0) $mnF2 = 1; // ensure no division by zero
      $op1 = rand(0, self::$maxMD);
      $op2 = rand($mnF2, $mxF);
      $answer = $op1;
      $op1 = $op2 * $answer;
    } else if ($nextOp == "add") {
      $answer = 10000;
      //$mxF1 = ($useDefaultFactors) ? self::$maxAS1 : $maxFactor;
      //$mxF2 = ($useDefaultFactors) ? self::$maxAS2 : $maxFactor;
      //$mnF = ($useDefaultFactors) ? 0 : $minFactor;
      while ($answer > self::$maxASAnswer) {
        $op1 = rand(0, self::$maxAS1);
        $op2 = rand(0, self::$maxAS2);
        $answer = $op1 + $op2;
      }
    } else if ($nextOp == "subtract") {
      //$mxF1 = ($useDefaultFactors) ? self::$maxAS1 : $maxFactor;
      //$mxF2 = ($useDefaultFactors) ? self::$maxAS2 : $maxFactor;
      //$mnF = ($useDefaultFactors) ? 0 : $minFactor;
      $op1 = rand(0, self::$maxAS1);
      $op2 = rand(0, self::$maxAS2);
      if ($op1 < $op2) {
	$temp = $op2;
	$op2 = $op1;
	$op1 = $temp;
      }
      $answer = $op1 - $op2;
    } else { // $nextOp == "multiply"
      $mxF = ($useDefaultFactors) ? self::$maxMD : $maxFactor;
      $mnF = ($useDefaultFactors) ? 0 : $minFactor;
      $op1 = rand($mnF, $mxF);
      $op2 = rand(0, self::$maxMD);
      $answer = $op1 * $op2;
    }

    $this->probMap = array("operator" => $nextOp,  "operatorChar" => self::$opChar[$nextOp], "operatorImage" => self::$opImage[$nextOp], "operand1" => $op1, "operand2" => $op2, "answer" => $answer, "userAnswer" => (int)(-1));  

    return $this;
  }

  public function isAnswerCorrect() {
    $userAns = $this->probMap["userAnswer"];
    if (is_null($userAns)) return false;
    return ($this->probMap["answer"] == $userAns);
  }

  public function submitUserAnswer($userAnswer, $db) {
    $this->probMap["userAnswer"] = $userAnswer;
    $this->writeToDB($db);
    return ($this->isAnswerCorrect());
  }

  public function getProblemMap() {
    return $this->probMap;
  }

  private function getRandomOperator() {
    return self::$ops[rand(1,4)];
  }

  private function nextOperator($anOperator) {
    $retval = $anOperator;
    if ($retval == "random") {
      $retval = $this->getRandomOperator();
    }
    return $retval;
  }

  public function asString() {
    //$probStr = $this->probMap["operand1"] . " " . $this->probMap["operatorChar"] . " " . $this->probMap["operand2"] . ' = ';
    $probStr = $this->probMap["operand1"] . " <img src=\"" . $this->probMap["operatorImage"] . "\" /> " . $this->probMap["operand2"] . " <img src=\"images/equal.gif\" /> ";
    return $probStr;
  }

  public function asStringWithAnswer() {
    $probStr = $this->asString() . $this->probMap["answer"] . '<br>Your answer was ' . $this->probMap["userAnswer"] . '.';
    return $probStr;
  }
}

class ArithmeticDrill
{
  private $numProblems;
  private $problems;
  private $iCurrentProblem = 0;
  private $startTime;
  private $drillID;
  private $studentID;

  public function __construct($student, $theOperator, $numP, $useDefaultFactors, $minFactor, $maxFactor, $db) {

    $this->numProblems = $numP;

    $queryStr = sprintf("INSERT into drill (studentID) VALUES (%d)", $student);
    if (!$db->query($queryStr)) {
      printf("Failed to create drill");
      exit();
    }
    $this->drillID = $db->insert_id;
    $this->studentID = $student;

    for ($i = 0; $i < $this->numProblems; $i++) {
      $this->problems[] = new Problem;
      $this->problems[$i]->getProblem($this->drillID, $student, $theOperator, $useDefaultFactors, $minFactor, $maxFactor);
      //echo '<br>Problems: ' . var_dump($this->problems[$i]) . '<br>';
    }
    
    $this->startTime = time();
  }

  public function getDrillID() {
    return $this->drillID;
  }

  public function getStudentID() {
    return $this->studentID;
  }

  public function nextProblem() {
    $i = $this->iCurrentProblem++;
    if ($i >= $this->numProblems) return NULL;
    return $this->problems[$i];  
  }

  public function previousProblem() {
    return $this->problems[$this->iCurrentProblem-1];
  }

  public function getProblemCount() {
    return $this->iCurrentProblem;
  }

  public function submitUserAnswer($anAnswer, $db) {
    $currentProblem = $this->problems[($this->iCurrentProblem)-1];
    if ($this->isTestFinished()) {
      $queryStr = sprintf("UPDATE drill SET duration = %d WHERE did = %d", $this->timeElapsed(), $this->drillID);
      if (!$db->query($queryStr)) {
        printf("<p>Failed to write test duration to drill database");
        exit();
      }
    }
    return $currentProblem->submitUserAnswer($anAnswer, $db);
  }

  public function numCorrectAnswers() {
    $cnt = 0;
    if (is_null($this->problems)) return $cnt;
    foreach ($this->problems as $prob) {
      if ($prob->isAnswerCorrect()) $cnt++;
    }
    return $cnt;
  }

  public function numTotalProblems() {
    return $this->numProblems;
  }

  public function numRemainingProblems() {
    return (int)($this->numTotalProblems() - $this->getProblemCount());
  }

  public function timeElapsed() {
    return (time() - $this->startTime);
  }

  public function isTestFinished() {
    return ($this->iCurrentProblem >= $this->numProblems);
  }

} // end class ArithmeticDrill

class Teacher {
  public $email;
  public $firstName;
  public $lastName;
  public $password;
  public $phoneNumber;
  public $school;
}

class Student {
  public $sid;
  public $userName;
  public $password;
  public $firstName;
  public $lastName;
  public $points;
  public $teacherEmail;

  public function getAchievementLevel() {
    if ($this->points >= 10000) return "PLATINUM";
    if ($this->points >= 5000) return "GOLD";
    if ($this->points >= 3000) return "SILVER";
    if ($this->points >= 1000) return "BRONZE";
    return "NEWBIE";
  }

  public function pointsToNextLevel() {
    if ($this->points >= 10000) return 0;
    if ($this->points >= 5000) return (10000 - $this->points);
    if ($this->points >= 3000) return (5000 - $this->points);
    if ($this->points >= 1000) return (3000 - $this->points);
    return (1000 - $this->points);
  }

  public function nextAchievementLevel() {
    if ($this->points >= 10000) return "none";
    if ($this->points >= 5000) return "Platinum";
    if ($this->points >= 3000) return "Gold";
    if ($this->points >= 1000) return "Silver";
    return "Bronze";
  }
}

class Drill {
  public $did;
  public $ts;
  public $duration;
  public $studentID;

  public function getNumCorrectAnswers($did, $db) {
    $queryStr = sprintf("SELECT COUNT(*) FROM problem where drillID = %d AND answer = userAnswer", $did);
    $result = $db->query($queryStr);
    if ($result->num_rows == 0) {
      echo "<p>Could not fetch number of correct answers for drill.<br> <a href='teacherHome.php'>Back to Teacher Home</a><br/>";
      $result->close();
      $db->close();
      exit();
    }
    $dbRow = $result->fetch_row();
    $correctAnswers = $dbRow[0];
    $result->close();
    return $correctAnswers;
  }

  public function getNumTotalAnswers($did, $db) {
    $queryStr = sprintf("SELECT COUNT(*) FROM problem where drillID = %d", $did);
    $result = $db->query($queryStr);
    if ($result->num_rows == 0) {
      echo "<p>Could not fetch number of total answers for drill.<br> <a href='teacherHome.php'>Back to Teacher Home</a><br/>";
      $result->close();
      $db->close();
      exit();
    }
    $dbRow = $result->fetch_row();
    $totalAnswers = $dbRow[0];
    $result->close();
    return $totalAnswers;
  }
}

class DBProblem {
  private static $opChar = array("add" => "\x2b", "subtract" => "\x2d", "multiply" => "x", "divide" => "/");
  //private static $opChar = array("add" => "\x2b", "subtract" => "\x2d", "multiply" => "\xd7", "divide" => "\xf7");

  public $pid;
  public $op1;
  public $op2;
  public $operator;
  public $answer;
  public $userAnswer;
  public $isCorrect;
  public $drillID;

  public function asString() {
    $probStr = $this->op1 . " " . self::$opChar[$this->operator] . " " . $this->op2 . ' = ' . $this->answer;
    //$probStr = $this->op1 . " " . $this->operator . " " . $this->op2 . ' = ' . $this->answer;
    //$probStr = 'prob string';
    return $probStr;
  }

  public function asStringWithUserAnswer() {
    $probStr = $this->asString() . ', Student Answer: ' . $this->userAnswer;
    return $probStr;
  }

  public function asStringWithHTMLUserAnswer() {
    $probStr = '<tr><td>' . $this->asString() . '</td><td>' . $this->userAnswer . '</td></tr>';
    return $probStr;
  }
}

function getStudentRankings($tid, $mySID, $doEcho, $db) {
  // Get students for this teacher
  $email = $db->real_escape_string($tid);
  $queryStr = sprintf("SELECT * FROM student where teacherEmail = '%s' ORDER BY points DESC", $email);
  $result = $db->query($queryStr);

  if ($doEcho) {
    echo '<table border="7" cellpadding="10">';
    printf("<tr><th>Rank</th><th>Name</th><th>Points</th></tr>\n");
  }
  $idx = 1;
  $studentRank = 0;
  while ($aStudent = $result->fetch_object("Student")) {
    $matched = FALSE;
    if ($mySID == $aStudent->sid) {
      $studentRank = $idx;
      $matched = TRUE;
    }
    if ($doEcho) {
      if ($matched) {
        printf("<tr><td><b>>>%d</b></td>\n", $idx);
        printf("<td><b>%s %s</b></td>\n", $aStudent->firstName, $aStudent->lastName);
        printf("<td><b>%d<<</b></td></tr>\n", $aStudent->points);
      } else {
        printf("<tr><td>%d</td>\n", $idx);
        printf("<td>%s %s</td>\n", $aStudent->firstName, $aStudent->lastName);
        printf("<td>%d</td></tr>\n", $aStudent->points);
      }
    }
    $idx++;
  }
  if ($doEcho) printf("</table>\n");
  $result->close();

  return $studentRank;
}

?>

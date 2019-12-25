<?php
function dbConnect() 
{
  $db = new mysqli("audra92.accountsupportmysql.com", "mrapp", "audra123", "multi");
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  return $db;
}
?>
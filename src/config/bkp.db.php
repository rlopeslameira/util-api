<?php
class db {
  public function connect(){

    $host = "ec2-54-160-96-70.compute-1.amazonaws.com";
    $dbname = "df22nategt0c44";
    $username = "fnfrifdjtampvn";
    $password = "28e0e7361822d51e1593f001f42d84092c837f2f65d2b1653103edea1595050f";
  
    try
    {
        if ($username)
        {
          $conn = new PDO("pgsql:host={$host};dbname={$dbname}", $username, $password);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          return $conn;
        }else{
          return null;
        }
    }
    catch(PDOException $ex)
    {
        echo "Failed to connect to the database: " . $ex->getMessage();
    }
  }

}
?>
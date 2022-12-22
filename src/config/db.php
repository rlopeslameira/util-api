<?php
class db {
  public function connect(){

    $host = "ec2-34-192-210-139.compute-1.amazonaws.com";
    $dbname = "d6nvg0bcpvng0e";
    $username = "ghevgbplzegtei";
    $password = "f1ad4e48570f863b7e667f3045a1f10b8db79d7a5cb8afe0857835a9b0963507";
  
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
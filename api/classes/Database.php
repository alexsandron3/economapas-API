<?php
  class Database {
    public function dbConnection () {
      try {
        // echo "mysql:host={$_ENV['DBhost']};DBname={$_ENV['DBname']}",$_ENV['DBuser'],$_ENV['DBpass'];
        $conn = new PDO("mysql:host={$_ENV['DBhost']};DBname={$_ENV['DBname']}",$_ENV['DBuser'],$_ENV['DBpass']);
        // $conn = new PDO('mysql:host='.$_ENV['DBhost'].';DBname='.$_ENV['DBname'],$_ENV['DBuser'],$_ENV['DBpass']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
      } catch (PDOException $error) {
        echo "Failed to connect".$error->getMessage();
      }
    }

  }

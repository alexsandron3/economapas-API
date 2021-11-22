<?php
  require_once('../config/head.php');
  require_once(__DIR__.'/classes/Database.php');
  header('Access-Control-Allow-Headers: access');
  header('Access-Control-Allow-Methods: GET, POST, UPDATE');
  header('Content-Type: application/json; charset=UTF-8');
  header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
  header('Access-Control-Allow-Origin: *');
  
  $dbConnection = new Database();
  $conn = $dbConnection->dbConnection();
  $data = json_decode(file_get_contents("php://input"));
  $returnData = [];
  // return print_r(json_encode(IsNullOrEmptyString($data->groupName)));
  
  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retirando espaços em branco
    $groupName = $data->groupName;
    $selectedCities = json_encode($data->selectedCities);
    $createdBy = $data->userId;

    // Preparando query
    $addGroup = "INSERT INTO economapas.citygroup (groupName, selectedCities, createdAt, createdBy) VALUES (:groupName, :selectedCities, NOW(), :createdBy)";
    $stmt = $conn->prepare($addGroup);
    try {
      //code...
      $stmt->bindValue(':groupName', $groupName, PDO::PARAM_STR);
      $stmt->bindValue(':selectedCities', $selectedCities, PDO::PARAM_STR);
      $stmt->bindValue(':createdBy', $createdBy, PDO::PARAM_STR);
      if($stmt->execute()) {
        $returnData = [
          "success" => 1,
          "message" => "Grupo criado com sucesso!",
          "grupo" => $conn->lastInsertId(),
        ];
      }else {
        $returnData = [
          "success" => 0,
          "message" => "Houve um erro!",
        ];
      }
    } catch (\Throwable $error) {
      $returnData = msg(0,500,$error->getMessage());
      
    }
  }else {
    $returnData = msg(0, 405, 'Inválid Method');
  }

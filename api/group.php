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
  // return print_r(json_encode($data->groupName));
  
  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificando se os dados estão sendo enviados
    if(!isset($data->groupName) || 
    !isset($data->selectedCities) || 
    !isset($data->userId)) {
      $returnData = msg(0, 422, 'Por favor, preencha os campos corretamente!');
    }else {
  
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
  }
  }elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
    $fetchGroups = "SELECT * FROM economapas.cityGroup";
    $stmt = $conn->prepare($fetchGroups);
    try {
      $stmt->execute();
      if($stmt->rowCount()){
          $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
          $returnData = [
            "success" => 1,
            "message" => 'Lista atualizada com sucesso!',
            "grupos" => $row
          ];
        }
        // else {
          // $returnData = msg(0, 422, 'Lista não atualizada!');
        // }
      } catch (\Throwable $error) {
        $returnData = msg(0,500,$error->getMessage());
  
      }
  }elseif($_SERVER['REQUEST_METHOD'] === 'UPDATE') {
    $updateGroup = "UPDATE economapas.citygroup SET groupName=:groupName, selectedCities=:selectedCities WHERE id=:id";

    $stmt = $conn->prepare($updateGroup);
    $groupName = $data->groupName;
    $selectedCities = json_encode($data->selectedCities);
    $id = $data->id;
    try {
      //code...
      $stmt->bindValue(':groupName', $groupName, PDO::PARAM_STR);
      $stmt->bindValue(':selectedCities', $selectedCities, PDO::PARAM_STR);
      $stmt->bindValue(':id', $id, PDO::PARAM_STR);
      $stmt->execute();
      if($stmt->rowCount()){
        $returnData = [
          "success" => 1,
          "message" => 'Grupo atualizado com sucesso!',
        ];
      }else{
        $returnData = [
          "success" => 0,
          "message" => 'Grupo não foi atualizado, tente novamente!',
        ];
      }
    } catch (\Throwable $error) {
      $returnData = msg(0,500,$error->getMessage());
  
    }

  }elseif($_SERVER['REQUEST_METHOD'] === 'DELETE') {

  }else {
    $returnData = msg(0, 405, 'Inválid Method');
  }

  echo json_encode($returnData);


  // {
//     "groupName": "",
//     "userId": "1",
//     "selectedCities": [
//         "Manaus - AM",
//         "Macapá - AP",
//         "Maceió - AL"
//     ]
// }

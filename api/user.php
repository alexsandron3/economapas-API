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

  // return print_r(json_encode($data));

  function msg ($success, $status, $message, $extra = []) {
	return array_merge([
		'success' => $success,
		'status' => $status,
		'message' => $message
	], $extra);
}

  
  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fetchUser = "SELECT * FROM economapas.users WHERE userName = :username";
    $stmt = $conn->prepare($fetchUser);

    $username = trim($data->username);
    $password = trim($data->password);
    // echo (isset($username));
    if(empty($username) || empty($password)) {
      $returnData = msg(0, 422, 'Por favor, preencha todos os campos!');
    } else {
      try {
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()) {
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          // Caso seja o pass esteja em formato de hash
            // $checkPass = password_verify($password, $row['password']);
          $checkPass = $row['password'] === $password;
          if($checkPass) {
            $returnData = [
            "success" => 1,
            "message" => 'Logado com sucesso!',
            "userId" => $row['id'],
            ];
          }else {
            $returnData = msg(0, 422, 'Senha inválida!');

          }
        }else {
          $returnData = msg(0, 422, 'Usuário inválido!');
        }
      } catch (\Throwable $error) {
        $returnData = msg(0, 500, $error->getMessage());
      }
    }


  }else {
    $returnData = msg(0, 405, 'Inválid Method');
  }

  echo json_encode($returnData);
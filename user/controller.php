<?php
//прием запросов с клиента
include_once 'repository.php';

$repository = new UserRepository();

if(isset($_GET['key'])){
    switch($_GET['key']){
        case 'sign-in':
            $data = json_decode(file_get_contents("php://input"));
            http_response_code(200);
            echo json_encode($repository->SignIn($data));
            break;
        case 'log-in':
            $data = json_decode(file_get_contents("php://input"));
            http_response_code(200);
            echo json_encode($repository->LogIn($data));
            break;
        case 'get-user':
            if(isset($_GET['token'])){
                http_response_code(200);
                echo json_encode($repository->GetUser($_GET['token']));
                return;
            }

            http_response_code(401);
            echo json_encode(array("message" => "Доступ запрещён."));
            break;
        case 'refresh-token':
            if(isset($_GET['token'])){
                // $repository->GetUser($_GET['token']);
                return;
            }

            http_response_code(401);
            echo json_encode(array("message" => "Доступ запрещён."));
            break;
    }
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Отсутствует ключ запроса."));
}
?>
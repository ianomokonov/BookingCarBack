<?php
//прием запросов с клиента
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization");

include_once 'repository.php';

$repository = new UserRepository();

if(isset($_GET['key'])){
    switch($_GET['key']){
        case 'sign-in':
            $data = json_decode(file_get_contents("php://input"));
            http_response_code(200);
            echo json_encode($repository->SignIn(AddUser::getInstanse($data)));
            break;
        case 'log-in':
            $data = json_decode(file_get_contents("php://input"));
            http_response_code(200);
            echo json_encode($repository->LogIn(LoginUser::getInstanse($data)));
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
                // TODO если будем делать динамические кодовые слова
                return;
            }

            http_response_code(401);
            echo json_encode(array("message" => "Доступ запрещён."));
            break;
        default: 
            echo json_encode(array("message" => "Неверный ключ запроса"));
    }
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Отсутствует ключ запроса."));
}
?>
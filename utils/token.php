<?php
    // требуется для декодирования JWT 
    use \Firebase\JWT\JWT;

    class Token{
    
        // переменные, используемые для JWT 
        private $key = "Hd31J34nH8k";

        public function __construct()
        {
            // установить часовой пояс по умолчанию 
            date_default_timezone_set('Europe/Moscow');

        }

        public function decode($jwt){
            // если JWT не пуст 
            if($jwt) {
            
                // если декодирование выполнено успешно, показать данные пользователя 
                try {
                    // декодирование jwt 
                    $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            
                    return $decoded->data;
            
                }
            
                // если декодирование не удалось, это означает, что JWT является недействительным 
                catch (Exception $e){
                
                    // код ответа 
                    http_response_code(401);
                
                    // сообщить пользователю отказано в доступе и показать сообщение об ошибке 
                    return array(
                        "message" => "Доступ закрыт.",
                        "error" => $e->getMessage()
                    );
                }
            }
            
            // показать сообщение об ошибке, если jwt пуст 
            else{
            
                // код ответа 
                http_response_code(401);
            
                // сообщить пользователю что доступ запрещен 
                echo json_encode(array("message" => "Доступ запрещён."));
            }
        }

        public function encode($data){
            
            try {
                // декодирование jwt 
                $token = JWT::encode($data, $this->key, array('HS256'));
        
                return $token;
        
            }
        
            // если декодирование не удалось, это означает, что JWT является недействительным 
            catch (Exception $e){
            
                // код ответа 
                http_response_code(401);
            
                // сообщить пользователю отказано в доступе и показать сообщение об ошибке 
                echo array(
                    "message" => "Ошибка расшифровки токена.",
                    "error" => $e->getMessage()
                );
            }
        }
    }

    
?>
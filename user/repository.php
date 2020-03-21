<?php
    //обработка запросов
    include_once '../utils/database.php';
    include_once '../utils/token.php';
    include_once 'model.php';
    class UserRepository{
        private $database;
        private $base_table = 'users';
        private $jwt;

        public function __construct()
        {
            $this->database = new DataBase();
            $this->jwt = new Token();
        }

        public function SignIn(AddUser $user = null){
            if($user){
                try{
                    if($this->EmailExists($user->email)){
                        http_response_code(400);
                        return array("message" => "Пользователь с таким email уже существует");
                    }
                    unset($user->id);
                    $user->password = password_hash($user->password, PASSWORD_BCRYPT);
                    $this->db->genInsertQuery($user, $this->base_table);
                    $fullUser = $this->GetUserById($this->database->db->lastInsertId());
                    if($fullUser){
                        return new UserResponse($this->jwt->encode($fullUser), $fullUser);
                    } else {
                        http_response_code(400);
                        return array("message" => "Ошбка добавления пользователя");
                    }
                } catch(Exception $e) {
                    http_response_code(400);
                    return array("message" => "Ошибка добавления пользователя", "error" => $e->getMessage());
                }
                
            } else {
                http_response_code(500);
                return array("message" => "Введите данные для регистрации");
            }
        }

        public function LogIn(LoginUser $user = null){
            if($user){
                try{
                    $sth = $this->database->db->prepare("SELECT id, name, email, phone FROM".$this->base_table." WHERE email = ? AND password = ? LIMIT 1");
                    $sth->setFetchMode(PDO::FETCH_CLASS, 'User');
                    $sth->execute(array($user->email, password_hash($user->password, PASSWORD_BCRYPT)));
                    $fullUser = $sth->fetch();
                    if($fullUser){
                        return new UserResponse($this->jwt->encode($fullUser), $fullUser);
                    } else {
                        http_response_code(400);
                        return array("message" => "Пользователь не найден");
                    }
                    
                } catch(Exception $e) {
                    return array("message" => "Ошибка входа пользователя", "error" => $e->getMessage());
                }
            } else {
                http_response_code(500);
                return array("message" => "Введите данные для входа");
            }
        }

        public function GetUser(string $token){
            $user = null;
            try{
                $user = $this->jwt->decode($token);
            } catch (Exception $e){
    
                // код ответа 
                http_response_code(401);
            
                // сообщение об ошибке 
                echo json_encode(array(
                    "message" => "Доступ закрыт",
                    "error" => $e->getMessage()
                ));
            }

            $user = $this->GetUserById($this->jwt->decode($token)['id']);

            if($user){
                return $user;
            } else {
                http_response_code(400);
                return array("message" => "Пользователь не найден");
            }
        }

        private function GetUserById(int $id){
            if($id){
                $sth = $this->database->db->prepare("SELECT * FROM ".$this->base_table." WHERE id = ?");
                $sth->setFetchMode(PDO::FETCH_CLASS, 'User');
                $sth->execute(array($id));
                return $sth->fetch();
            } else {
                http_response_code(500);
                return array("message" => "GetUserById -> id не может быть пустым");
            }
            
        }

        private function EmailExists(string $email){
            $query = "SELECT id, email FROM " . $this->base_table . "WHERE email = ?";
 
            // подготовка запроса 
            $stmt = $this->conn->prepare( $query );
            // инъекция 
            $email=htmlspecialchars(strip_tags($email));
            // выполняем запрос 
            $stmt->execute(array($email));
        
            // получаем количество строк 
            $num = $stmt->rowCount();

            return $num > 0;
        }

    }
?>

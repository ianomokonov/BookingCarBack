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
                    $s = $this->database->db->prepare("INSERT INTO ".$this->base_table." (name, email, password, phone) VALUES (?,?,?,?)");
                    $s->execute(array($user->name, $user->email, $user->password, $user->phone));
                    $fullUser = $this->GetUserById($this->database->db->lastInsertId());
                    if($fullUser){
                        return new UserResponse($this->jwt->encode($fullUser), $fullUser, "Пользователь зарегистрирован");
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
                    $sth = $this->database->db->prepare("SELECT id, name, email, phone, password FROM ".$this->base_table." WHERE email = ? LIMIT 1");
                    $sth->setFetchMode(PDO::FETCH_CLASS, 'User');
                    $sth->execute(array($user->email));
                    $fullUser = $sth->fetch();
                    
                    if($fullUser){
                        if(!password_verify($user->password, $fullUser->password)){
                            http_response_code(401);
                            return array("message" => "Неверный пароль");
                        }
                        unset($fullUser->password);
                        return new UserResponse($this->jwt->encode($fullUser), $fullUser, "Вход выполнен");
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
            $user = $this->GetUserById($user->id);

            if($user){
                return $user;
            } else {
                http_response_code(400);
                return array("message" => "Пользователь не найден");
            }
        }

        private function GetUserById(int $id){
            if($id){
                $sth = $this->database->db->prepare("SELECT id, email, phone, name FROM ".$this->base_table." WHERE id = ?");
                $sth->setFetchMode(PDO::FETCH_CLASS, 'User');
                $sth->execute(array($id));
                return $sth->fetch();
            } else {
                http_response_code(500);
                return array("message" => "GetUserById -> id не может быть пустым");
            }
            
        }

        private function EmailExists(string $email){
            $query = "SELECT id, email FROM " . $this->base_table . " WHERE email = ?";
 
            // подготовка запроса 
            $stmt = $this->database->db->prepare( $query );
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

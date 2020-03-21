<?php
    //обработка запросов
    include_once '../utils/database.php';
    include_once 'model.php';
    class UserRepository{
        private $database;
        private $base_table = 'users';

        public function __construct()
        {
            $this->database = new DataBase();
        }

        public function SignIn(AddUser $user = null){
            if($user){
                try{
                    unset($user->id);
                    $user->password = password_hash($user->password, PASSWORD_BCRYPT);
                    $this->db->genInsertQuery($user, $this->base_table);
                    return new UserResponse("token", $this->GetUserById($this->database->db->lastInsertId()));
                } catch(Exception $e) {
                    return array("message" => "Ошибка добавления пользователя", "error" => $e);
                }
                
            } else {
                http_response_code(500);
                return array("message" => "Пользователь не может быть пустым");
            }
        }

        public function LogIn(LoginUser $user = null){
            if($user){
                try{
                    $sth = $this->database->db->prepare("SELECT * FROM ".$this->base_table." WHERE email = ? AND password = ? LIMIT 1");
                    $sth->setFetchMode(PDO::FETCH_CLASS, 'User');
                    $sth->execute(array($user->email, password_hash($user->password, PASSWORD_BCRYPT)));
                    $fullUser = $sth->fetch();
                    return new UserResponse("token", $this->GetUserById($fullUser->id));
                } catch(Exception $e) {
                    return array("message" => "Ошибка входа пользователя", "error" => $e);
                }
            } else {
                http_response_code(500);
                return array("message" => "Введите данные для входа");
            }
        }

        public function GetUser(string $token){
            return "GetUser";
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

    }
?>

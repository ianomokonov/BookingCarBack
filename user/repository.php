<?php
    //обработка запросов
    include_once '../utils/database.php';
    include_once 'model.php';
    class UserRepository{
        private $db;
        private $base_table = 'users';

        public function __construct()
        {
            $this->db = new DataBase();
        }

        public function SignIn(AddUser $user = null){
            return "SignIn";
        }

        public function LogIn(LoginUser $user = null){
            return "LogIn";
        }

        public function GetUser(string $token){
            return "GetUser";
        }

    }
?>

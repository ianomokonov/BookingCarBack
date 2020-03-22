<?php
//описание моделей пользователей
    class User {
        public $id;
        public $name;
        public $phone;
        public $email;
    }

    class AddUser extends User {
        public $password;

        public static function getInstanse($user){
            $user = (array) $user;
            $result = new AddUser();
            $result->name = $user['name'];
            $result->phone = $user['phone'];
            $result->email = $user['email'];
            $result->password = $user['password'];
            return $result;
        }
    }

    class LoginUser {
        public $email;
        public $password;

        public static function getInstanse($user){
            $user = (array) $user;
            $result = new LoginUser();
            $result->email = $user['email'];
            $result->password = $user['password'];
            return $result;
        }
    }

    class UserResponse{
        public $token;
        public $user;

        public function __construct(string $token, User $user)
        {
            $this->token = $token;
            $this->user = $user;
        }
    }
?>
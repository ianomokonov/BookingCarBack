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
    }

    class LoginUser {
        public $email;
        public $password;
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
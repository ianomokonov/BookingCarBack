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
?>
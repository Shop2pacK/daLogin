<?php

    require 'signup_validation.php';

    if(isset($POST['submit'])) {

        $validation = new signupValidation($_POST);




    }


?>
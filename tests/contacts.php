<?php

    header("Content-Type: text/html");

    try {
   
        $db_name     = 'sample_db';
        $db_user     = 'test_user';
        $db_password = 'EXAMPLE_PASSWORD';
        $db_host     = 'localhost';

        $pdo = new PDO('mysql:host=' . $db_host . '; dbname=' . $db_name, $db_user, $db_password);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
        require_once 'Validator.php';
       
        $validator = new Validator();

        $validation_rules = ['first_name' => 'required|alphanumeric',
                             'last_name'  => 'required|alphanumeric',
                             'phone'      => 'required|phone',
                             'email'      => 'required|email',];

        $error = $validator->validate($_POST, $validation_rules);

        if ($error != '') {

            echo "Validation failed, plese review the following errors:\n" . $error. "\n";

            exit();

        }

        $sql = 'insert into contacts (first_name, last_name, phone, email) values (:first_name, :last_name, :phone, :email)';

        $data = ['first_name' => $_POST['first_name'],
                 'last_name'  => $_POST['last_name'],
                 'phone'      => $_POST['phone'],
                 'email'      => $_POST['email']];

        $stmt = $pdo->prepare($sql);

        $stmt->execute($data);

       echo " The record was saved without any validation errors.\n";

   } catch (PDOException $e) {

        echo 'Database error. ' . $e->getMessage();

   }

?>
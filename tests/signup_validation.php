<?php

    /**
     * Creates a user validator class to handle signup input
     * constructor which takes in POST data from form
     * and checks if required fields are present within data.
     * Creates method to validate indivial fields as well
     * -- a method to validate a full brazilian portuguese name
     * -- a method to validate an email
     * returns an error array once all checks are done
     *  */    

    class signupValidation {

        private $data; // will hold the POST data
        private $errors = []; // Erros that will be sent back
        private static $fields = ['fullname', 'email']; // required fields

        public function __construct($post_data) {
            // get the $data above and 
            $this->data = $post_data;
        }

        public function validateForm() {
            // What fields are required? Are they present? Do they exist?
            foreach(self::$fields as $field) {
                if(!array_key_exists($field, $this->data)) {
                    trigger_error("$field is not present in data");
                    return;                    
                }
            }
            
            $this->validateFullName();
            $this->validateEmail();

            return $this->errors;

        }

        private function validateFullName() {            
            $val = trim($this->data['fullname']);
            if(empty($val)) {
                $this->addError('fullname', 'Empty Full Name');
            } else {
                if(!preg_match('/^[a-zA-Z0-9]{6,12}$/', $val)) {
                    $this->addError('fullname', 'Invalid Full Name Format');
                }
            }
        }

        private function validateEmail() {
            $val = trim($this->data['email']);
            if(empty($val)) {
                $this->addError('email', 'Empty email');
            } else {
                if(!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $this->addError('email', 'Invalid email Format');
                }
            }            
        }

        private function addError($key, $val) {
            
            $this->errors[$key] = $val;

        }


    }

?>
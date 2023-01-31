<?php

    /**
     * Creates a user validation class to handle signup, verify, login and reset input.
     * Constructor takes in POST data from form and checks if required fields are present within data.
     * It also creates a method to validate individual fields as well
     * -- a method to validate a full brazilian portuguese name
     * -- a method to validate an email
     * returns an error array once all checks are done
    **/ 

    class signupValidation {

        private $data;        // this will hold the POST data
        private $errors = []; // Errors that will be sent back to the referrer

        // required fields. see validateForm method.
        private static $fields = [
                                    'fullname', 
                                    'phone',
                                    'email',
                                    'instagram',
                                    'pwd',
                                    'cpwd'
                                ];

        /* Called when "... =new signupValidation($_POST);" */
        public function __construct($post_data) {
            
            $this->data = $post_data; // gets the $data above
        
        }

        public function validateForm() {

            // What fields are required? Are they present? Do they exist?
            foreach(self::$fields as $field) {
                if(!array_key_exists($field, $this->data)) {
                    //trigger_error("$field is not present in data. // FIELD NOT EXPECTED, not it the list of fields above");
                    trigger_error("Dados inválidos");
                    return;                    
                }
            }
            
            $this->validatePtBrFullName();
            $this->validatePtBrPhone();
            $this->validateEmail();
            $this->validateInstagram();
            $this->validatePassword();

            return $this->errors;

        }

        private function validatePtBrFullName() {            
            $val = trim($this->data['fullname']);
            if(empty($val)) {
                $this->addError('fullname', 'Nome completo é necessário');
            } else {
                if(strlen($val) <= 3 || strlen($val) > 40 || !preg_match("/^[a-zA-Z-ÇçÑñÁÉÍÓÚáéíóúÃÕãõÂÊÔâêôÈèöÖ \']*$/", $val)) {
                    $this->addError('fullname', 'Nome completo inválido');
                } elseif(strlen($val) <= 3 || 
                         strlen($val) > 40 || 
                         !preg_match("/^[a-zA-Z-ÇçÑñÁÉÍÓÚáéíóúÃÕãõÂÊÔâêôÈèöÖ \']*$/", $val) ||
                         str_word_count($val) < 2) {
                    $this->addError('fullname', 'Nome com sobrenome é necessário');
                }
            }
        }

        private function validatePtBrPhone() {
            $val = trim($this->data['phone']);
            if(empty($val)) {
                $this->addError('phone', 'Telefone é necessário');
            } else {
                if(strlen($val) <= 8 || strlen($val) > 20 || !preg_match("/^\([0-9]{2}\) [0-9]?[0-9]{4}-[0-9]{4}$/", $val)) {
                    $this->addError('phone', 'Telefone inválido');
                }
            }
        }

        private function validateEmail() {
            $val = trim($this->data['email']);
            if(empty($val)) {
                $this->addError('email', 'Email é necessário');
            } else {
                if (strlen($val) <= 8 || strlen($val) > 40 || filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $domain = substr($val, strpos($val, '@') + 1);       
                    if (!checkdnsrr($domain, 'MX')) {        
                        $this->addError('email', 'Email inválido');        
                    }                    
                } else {        
                    $this->addError('email', 'Email inválido');        
                }
            }            
        }

        private function validateInstagram() {
            $val = trim($this->data['instagram']);
            if(empty($val)) {
                $this->addError('instagram', 'Instagram é necessário');
            } else {
                if(strlen($val) <= 3 || strlen($val) > 40 || !preg_match("/^[\w](?!.*?\.{2})[\w.]{1,28}[\w]$/", $val)) {
                    $this->addError('instagram', 'Instagram inválido');
                }
            }
        }

        private function validatePassword() {
            $p = trim($this->data['pwd']);
            $cp = trim($this->data['cpwd']);
            if(empty($p)) {
                $this->addError('password', 'Senha é necessária');
            } else {
                if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,40})/', $p) ) {
                    $this->addError('password', 'A senha deve ter letras, números e no mínimo 8 caracteres');        
                } else {        
                    if ($p !== $cp) {        
                        $this->addError('password', 'As senhas não são iguais');        
                    }        
                }
            }
        }        

        private function addError($key, $val) {
            
            $this->errors[$key] = $val;

        }


    }

    
?>
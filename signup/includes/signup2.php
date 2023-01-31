<?php

    session_start();

    require __DIR__ . '/../../assets/includes/utils.php';
    require __DIR__ . '/../../assets/setup/settings.php'; 
    require __DIR__ . '/../../assets/includes/session_authorization.php';
    require __DIR__ . '/../../assets/includes/security_functions.php';
    require __DIR__ . '/../../assets/includes/signupValidation.php';

    check_if_its_logged_out(); // If there is any authorization redirects to home. Otherwise, does nothing (stay here).

    if (isset($_POST['dosignup'])) {

        if (!_cktoken()) {

            errorExiting('signuperror', 'A solicitação não pode ser validada');

        }

        /* VALIDATION */
        $validation = new signupValidation($_POST);
        $errors = $validation->validateForm();

        if (empty($errors)) {

            $fullname  = preg_replace('/\s{2,}/', ' ', trim($_POST['fullname'])); // Removing double whitespaces such and such
            $phone     = trim($_POST['phone']);
            $email     = trim($_POST['email']);
            $instagram = trim($_POST['instagram']);
            $pwd       = trim($_POST['pwd']);
            $cpwd      = trim($_POST['cpwd']);

            // Save to the database
            require __DIR__ . '/../../assets/includes/database_hub.php'; // If everything is fine at this point we can move on and connect to the database

            $C = connect();

            if ($C) { // DATABASE CONNECTION

                $r = sqlSelect($C, 'SELECT 1 FROM users WHERE email=?', 's', $email); // CHECKING IF EMAIL IS NOT TAKEN

                if ($r && $r->num_rows === 0) {
                    
                    $C->autocommit(FALSE); // BEGIN TRANSACTION SINCE THERE ARE MORE THAN ONE DML THAT MUST PASS THROUGH

                    $usql = "INSERT INTO users (fullname, phone, instagram, email, pwd, created_at, last_login_at) VALUES (?, ?, ?, ?, ?, NOW(), NULL)";

                    $uid = sqlInsert($C, $usql, 'sssss', $fullname, $phone, $instagram, $email, password_hash($pwd, PASSWORD_DEFAULT));

                    if ($uid !== -1) {                     

                        //$_SESSION['STATUS']['signupstatus'] = 'Novo usuário gravado';
                        
                        $selector = bin2hex(random_bytes(8));

                        $utkn = _urltoken();    // This function generates a pair of one key and one token.
                        $tkey   = $utkn['key']; // The key will be saved on the database. The user never gets to know the key.
                        $ktoken = $utkn['tkn']; // The token will be sent in the URL (also stored on the database just for reference)
                
                        $url = VERIFY_ENDPOINT . "?selector=" . $selector . "&token=" . $ktoken;
            
                        $tsql = "INSERT INTO user_tokens (email, auth_type, token_key, keyed_token, selector, created_at) VALUES (?, ?, ?, ?, ?, DEFAULT)";
            
                        $tid = sqlInsert($C, $tsql, 'sssss', $email, "signup", $tkey, $ktoken, $selector);

                        if ($tid !== -1) {

                            //$_SESSION['STATUS']['signupstatus'] = 'Novo usuário e token gravados';

                            include __DIR__ . '/../../assets/includes/sendmail.php';
        
                            $subject = APP_NAME . ' | Verifique seu Email';
            
                            $mail_variables = array();
            
                            $mail_variables['APP_NAME'] = APP_NAME;
                            $mail_variables['fullname'] = $fullname;
                            $mail_variables['email'] = $email;
                            $mail_variables['url'] = $url;
            
                            $message = file_get_contents("./signup_email_template.php");
            
                            foreach($mail_variables as $key => $value) {
                                
                                $message = str_replace('{{ '.$key.' }}', $value, $message);
            
                            }
            
                            if (sendEmail($email, $fullname, $subject, $message)) { 

                                //$_SESSION['STATUS']['signupstatus'] = 'Novo usuário e token gravados e email de validação enviado';
                                
                                $C->autocommit(TRUE); // END TRANSACTION (IMPLICIT COMMIT)

                                $_POST = array(); // Clear form fields, not sure if I need this since there is a header/exit right below        
                                
                                header("Location: ../success.php");
                                exit();
            
                            } else { // EMAIL COULD NOT BE SENT FOR SOME REASON

                                $C->rollback(); // ROLLBACK TRANSACTION
                                $C->autocommit(TRUE);                            
            
                                errorExiting('signuperror', 'Erro ao enviar email de validação (SENDMAIL ERROR). Sua conta não foi cadastrada');
            
                            }
                    
                        } else { // DML ERROR (INSERT ON USER_TOKENS TABLE)

                            $C->rollback(); // ROLLBACK TRANSACTION
                            $C->autocommit(TRUE);                        
            
                            errorExiting('signuperror', 'Erro ao gravar token (DML ERROR). Sua conta não foi cadastrada');
            
                        }

                    } else { // DML ERROR (INSERT ON USERS TABLE)

                        $C->rollback(); // ROLLBACK TRANSACTION
                        $C->autocommit(TRUE);                    

                        errorExiting('signuperror', 'Erro ao cadastrar nova conta (DML ERROR)');

                    }
                    
                    //$res->free_result();
                    $C->autocommit(TRUE);

                } else { // EMAIL IS ALREADY IN USE

                    errorExiting('signuperror', 'Email já cadastrado');

                }
            
            } else { // DATABASE CONNECTION ERROR

                errorExiting('signuperror', 'Erro ao tentar conectar (CONN)');

            }

        } else { // VALIDATION ERROR

            errorExiting('signuperror', 'Erros com a validação de dados:');        
        
        }
   
    } else { // SOME ERROR WITH THE FORM

        errorExiting('signuperror', 'Erro com o formulário');

    }

?>
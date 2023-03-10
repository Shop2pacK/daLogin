<?php

    /* Does the email exist in the system? */
    function emailExists($C, $e) {

        $q = 'SELECT 1 FROM users WHERE email=?';

        $s = $C->prepare($q);
        $s->bind_param("s", $e);
        
        if($s->execute()) {

            $r = $s->get_result();
            $s->close();

            if ($r && $r->num_rows === 1) {

                return true;
            
            }

        }
        
        $s->close();
        return false;
    
    }

        /** 
     * Two DML commands, one transaction.
     * Updates information about user's last login and
     * clears unsuccessful user login attempts and
     * clears unused reset tokens
    */
    function recordLastUserLogin($C, $email) {

        $u = "UPDATE users SET last_login_at = NOW() WHERE email=?";
        $d1 = "DELETE FROM user_login_reset_attempts WHERE email=?";
        $d2 = "DELETE FROM user_tokens WHERE email=? AND auth_type = 'reset'"; // if the password was used to log in,
                                                                               // tokens that could possibly be used to reset it
                                                                               // are not necessary anymore

        $C->autocommit(FALSE);

        $su = $C->prepare($u);
        $su->bind_param("s", $email);

        if ($su->execute()) { // update user

            $sd1 = $C->prepare($d1);
            $sd1->bind_param("s", $email);

            if ($sd1->execute()) { // delete attempt(s)

                $sd2 = $C->prepare($d2);
                $sd2->bind_param("s", $email);

                if ($sd2->execute()) { // delete token(s)

                    $C->autocommit(TRUE); // All good, commit!
                    $sd2->close();
                    $sd1->close();
                    $su->close();
                    return true;

                } else { // user_tokens record(s) could not be deleted

                    $C->rollback();
                    $sd2->close();
                    $sd1->close();
                    $su->close();
                    $C->autocommit(TRUE);
                    return false;

                }

            } else { // user_login_reset_attempts record(s) could not be deleted

                $C->rollback();
                $sd1->close();
                $su->close();
                $C->autocommit(TRUE);
                return false;

            }
        
        } else { // users table could not be updated

            $C->rollback();
            $su->close();
            $C->autocommit(TRUE);
            return false;

        }

        $C->rollback();
        $su->close();
        $C->autocommit(TRUE);
		return false;

    }

    /**
     * Two DML commands, one transaction.
     * Updates information about user's verification time and
     * Clears tokens previously used
     */
    function recordUserWasVerified($C, $email) {

        $u = "UPDATE users SET verified_at = NOW() WHERE email=?";
        $d = "DELETE FROM user_tokens WHERE email=? AND auth_type IN ('signup','verify')";

        $C->autocommit(FALSE);

        $su = $C->prepare($u);
        $su->bind_param("s", $email);

        if ($su->execute()) { // update user

            $sd = $C->prepare($d);
            $sd->bind_param("s", $email);

            if ($sd->execute()) { // delete token

                $C->autocommit(TRUE); // All good, commit!
                $sd->close();
                $su->close();
                return true;

            } else {

                $C->rollback();
                $sd->close();
                $su->close();
                $C->autocommit(TRUE);
                return false;

            }
        
        } else {

            $C->rollback();
            $su->close();
            $C->autocommit(TRUE);
            return false;

        }

        $C->rollback();
        $su->close();
        $C->autocommit(TRUE);
		return false;

    }

    /**
     * Two DML commands, one transaction.
     * Updates user's password and 
     * Clears tokens previously used
     */
    function recordUserNewPassword($C, $email, $pwd) {

        $u = "UPDATE users SET pwd=? WHERE email=?";
        $d = "DELETE FROM user_tokens WHERE email=? AND auth_type='reset'";

        $C->autocommit(FALSE);

        $su = $C->prepare($u);
        $su->bind_param("ss", $pwd, $email);

        if ($su->execute()) { // update user

            $sd = $C->prepare($d);
            $sd->bind_param("s", $email);

            if ($sd->execute()) { // delete token

                $C->autocommit(TRUE); // All good, commit!
                $sd->close();
                $su->close();
                return true;

            } else {

                $C->rollback();
                $sd->close();
                $su->close();
                $C->autocommit(TRUE);
                return false;

            }
        
        } else {

            $C->rollback();
            $su->close();
            $C->autocommit(TRUE);
            return false;

        }

        $C->rollback();
        $su->close();
        $C->autocommit(TRUE);
		return false;

    }

    /**
     * Records unsuccessful login attempts
     */    
    function recordLoginAttempt($C, $email, $ip, $attempt_time, $attempt_type) {
        
        $la = 'INSERT INTO user_login_reset_attempts (email, ip, attempt_time, attempt_type) VALUES (?, ?, ?, ?)';

        $stmt = $C->prepare($la);
        $stmt->bind_param("ssss", $email, $ip, $attempt_time, $attempt_type);

        if($stmt->execute()) {

            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;

    }

?>
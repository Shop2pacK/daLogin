<?php

    class Validator {

        public function validate($params, $validation_rules) {

            try {

                $response = '';

                foreach ($validation_rules as $field => $rules) {

                    foreach (explode('|', $rules) as $rule) {

                        if ($rule == 'required' && array_key_exists($field, $params) == false) {

                            $response .= "The " . $field ." is required.\n";

                        }

                        if (array_key_exists($field, $params) == true) {

                            if ($rule == 'alphanumeric' && preg_match('/^[a-z0-9 .\-]+$/i', $params[$field]) == false) {

                                $response .= "The value of " . $field . " is not a valid alphanumeric value.\n";

                            }

                            if ($rule == 'phone' && preg_match('/^[0-9 \-\(\)\+]+$/i', $params[$field]) == false) {

                                $response .= "The value of " . $field . " is not a valid phone number.\n";

                            }

                            if ($rule == 'email' && filter_var($params[$field], FILTER_VALIDATE_EMAIL) == false) {

                                $response .= "The value of " . $field . " is not a valid email value.\n";

                            }

                        }

                    }

                }

                return $response;

            } catch (Exception $ex) {

                return $e->getMessage();

            } // try catch

        } // function

    } // class

?>
<?php

    /* Using fewer code lines this helps with error messages returned to user */
    function errorExiting($suberror, $errormsg) {

        $_SESSION['ERRORS'][$suberror] = $errormsg;
        
        if (isset($_SESSION['authorization'])) {
            
            unset($_SESSION['authorization']);
        
        }
        
        header("Location: ../");
        exit();

    }

    /**
     * Convert all applicable characters to HTML entities.
     * @param string $text The string being converted.
     * @return string The converted string.
     */
    function html($text) {
    
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    }

    /* Helps with Sanitization */
    function removeScripts($input) {
 
        $search = array(
          '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
          '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
          '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
          '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
        );
       
          $output = preg_replace($search, '', $input);
          return $output;
          
    }

    function removeBackslashes($input) {

        $output = preg_replace('/\\\\/', '', $input);          // removes backslashes
        
        return $output;

    }

    function removeDoubleSpaces($input) {

        $output = preg_replace('/\s{2,}/', ' ', trim($input)); // removes double spaces

        return $output;

    }

    function cleanText($input) {

        // Using str_replace() function
        // to replace the word
        $output = str_replace(array('~',
                                    '\!',
                                    '@',
                                    '#',
                                    '\$',
                                    '%',
                                    '^',
                                    '&',
                                    '*',
                                    '(', 
                                    ')',
                                    //'_', // since this function is going to be used with multiple text fields lets allow underscores
                                    '+', 
                                    '=',
                                    '\{', 
                                    '\}',
                                    '\[', 
                                    '\]', 
                                    '\'', 
                                    '|',
                                    ';',
                                    ':',
                                    '"', // pt-br names can have single quotes
                                    ',' , // pt-br names might have commas
                                    '<', 
                                    '>', 
                                    '?',
                                    '\\', 
                                    '/'), '', $input);
    
        // Returning the result
        return $output;
    
    }

?>
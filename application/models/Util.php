<?php
/**
 * This class contains general php functions.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Util {

    /**
     * Generate a random string.
     *
     * @param int $length The length of the generated string.
     *
     * @param boolean $special Whether to use special characters such as !@#$ or not.
     *
     * @param boolean $caseSensitive Whether to use both upper and lower case characters or not.
     * 
     * @return string The generated string.
     */
    public static function generateRandomString($length = 40, $special = false, $caseSensitive = false)
    {
        $characters = "abcdefghijklmnopqrstuvwxyz0123456789";
        if ($caseSensitive) {
            $characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        if ($special) {
            $characters .= "!@#$%^&*()}{[]?\\/.,";
        }
        $string = '';
        for($p = 0; $p < $length; $p++)
        {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
}


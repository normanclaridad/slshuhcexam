<?php 
// @session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class Helpers {

    /**
     * ------------------------------------------------------
     * checkSession
     * ------------------------------------------------------
     * It will check if session is expired or not
     * 
     * @return  boolean 
     */
    public function checkSession() {
        if(!isset($_SESSION['SESS_AUTH_EXAM'])) {
            return false;
        }
        return true;
    }
    
    public function redirectLogin() {
        header('Location: /login.php');
    }

    /**
     * ------------------------------------------------------
     * encryptDecrypt
     * ------------------------------------------------------
     * Allow integer to encrypt and decrypt
     * 
     * @param $string       string
     * @param $action       string
     * 
     * @return $encrypted   string
     */
    public function encryptDecrypt($string, $action = 'encrypt') {
        $encryptMethod = "AES-256-CBC";
        $secretKey     = 'AA74CDCC2BBRT935136HH7B63C27'; // user define private key
        $secretIv      = '5fgf5HJ5g27'; // user define secret key
        $key    = hash('sha256', $secretKey);
        $iv     = substr(hash('sha256', $secretIv), 0, 16); // sha256 is hash_hmac_algo
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encryptMethod, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encryptMethod, $key, 0, $iv);
        }

        return $output;
    }
    
    /**
     * Check active menu
     * @param uri
     * @param activekeyword
     * 
     * @return boolean
     */

     public function checkactivemenu($uri, $activekeyword) : bool {

        $spl = str_replace('/views/','',$uri);
        if(strpos($spl, $activekeyword) === true) {
            return true;
        }

        return false;
    }

    public function checkDevice() {
        // Check if the "mobile" word exists in User-Agent 
        $isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
        
        // Check if the "tablet" word exists in User-Agent 
        $isTab = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "tablet")); 
        
        // Platform check  
        $isWin = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "windows")); 
        $isAndroid = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "android")); 
        $isIPhone = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "iphone")); 
        $isIPad = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "ipad")); 
        $isIOS = $isIPhone || $isIPad; 
        $device = 'desktop';
        if($isMob) { 
            if($isTab) { 
                $device = 'tablet';
            } else { 
                $device = 'mobile';
            } 
        }
        
        return $device;
    }

    public function getCurrentUser() {
        $name = $_SESSION['SESS_FIRST_NAME'];
        $name .= ' '. $_SESSION['SESS_LAST_NAME'];

        return $name;
    }
}
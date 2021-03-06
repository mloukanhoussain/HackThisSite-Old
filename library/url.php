<?php
/**
 * Url cleaning.
 * 
 * @package Library
 */
class Url {
    
    static $populated = false;
    static $base;
    static $static;
    
    /**
     * Format a url.
     * 
     * @param string $url A partial/complete url.
     * @param bool $static True if the static server should be used.
     */
    public static function format($url, $static = false) {
        if (!self::$populated) self::populate();
        if (empty($url)) $url = '/';
        if (substr($url, 0, 4) == 'http')
            return $url;
        
        $base = ($static ? self::$static : self::$base);
        $base = (isset($_SERVER['SSL_CLIENT_RAW_CERT']) ? 'https' . $base : 'http' . $base);

        return ($base[strlen($base) - 1] == '/' && $url[0] == '/' ? substr($base, 0, -1) : $base) . $url;
    }
    
    private static function populate() {
        self::$populated = true;
        self::$base = str_replace('%P', $_SERVER['SERVER_PORT'], Config::get('other:baseUrl'));
        self::$static = str_replace('%P', $_SERVER['SERVER_PORT'], Config::get('other:staticUrl'));
    }
}

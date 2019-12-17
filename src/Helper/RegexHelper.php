<?php

namespace App\Helper;

/**
 * Class RegexHelper
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class RegexHelper
{
    public static function pregMatchBasename($filename)
    {
        $filename = basename($filename);
        $filenameNoExtension = preg_replace("/\.[^.]+$/", "", $filename);

        return $filenameNoExtension ?: false;
    }

    public static function pregMatchExtension($filename)
    {
        $except = array(
            "rar", "zip", "mp3", "mp4", "mp3", "mov",
            "flv", "wmv", "swf", "gif",
            "bmp", "avi");
        $imp = implode('|', $except);

        if (preg_match('/^.*\.(' . $imp . ')$/i', $filename, $matches)) {
            return isset($matches[1]) ? $matches[1] : false;
        }

        return false;
    }

    /**
     * @param [type] $str     [description]
     * @param array $noStrip [description]
     * @return string
     */
    public static function setCamelCase($str, array $noStrip = array())
    {
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return (string)$str;
    }
}

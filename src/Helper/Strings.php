<?php

namespace Matecat\Finder\Helper;

class Strings
{
    /**
     * @param string $string
     *
     * @return string
     */
    public static function cutNbsps($string)
    {
        return str_replace(['&nbsp;', ' '], ' ', $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function htmlEntityDecode($string)
    {
        return html_entity_decode($string, ENT_QUOTES|ENT_XHTML, 'UTF-8');
    }

    /**
     * @param string $string
     *
     * @return array|bool|string[]|null
     */
    public static function split($string)
    {
        return mb_str_split($string);
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public static function token($length = 8)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isMultibyte($string)
    {
        return ((strlen($string) - mb_strlen($string)) > 0);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function protectHTMLTags($string)
    {
        preg_match_all('/<[^>]*>|&lt;[^&gt;]*&gt;/sm', $string, $matches);

        if(!empty($matches[0])){
            foreach ($matches[0] as $tag){
                $protectedTag = str_replace(["<", ">", "&lt;", "&lt;"], ["__LT__","__GT__", "__##LT##__", "__##GT##__"], $tag);
                $string = str_replace($tag, $protectedTag, $string);
            }
        }

        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function unprotectHTMLTags($string)
    {
        $string = str_replace(["__LT__","__GT__", "__##LT##__", "__##GT##__"], ["<", ">", "&lt;", "&lt;"], $string);

        return $string;
    }
}
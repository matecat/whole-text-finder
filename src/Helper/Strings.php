<?php

declare(strict_types=1);

namespace Matecat\Finder\Helper;

class Strings
{
    /**
     * Removes non-breaking spaces and replaces them with regular spaces within a given string.
     *
     * @param string $string The input string that may contain non-breaking spaces.
     *
     * @return string The processed string with all non-breaking spaces replaced by regular spaces.
     */
    public static function cutNbsps(string $string): string
    {
        return str_replace(['&nbsp;', ' '], ' ', $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function htmlEntityDecode(string $string): string
    {
        return html_entity_decode($string, ENT_QUOTES|ENT_XHTML, 'UTF-8');
    }

    /**
     * @return array<int, string>
     */
    public static function split(string $string): array
    {
        return mb_str_split($string);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isMultibyte(string $string): bool
    {
        return ((strlen($string) - mb_strlen($string)) > 0);
    }

    /**
     * Checks if a given substring exists within a string.
     *
     * @param string $needle The substring to search for within the haystack.
     * @param string $haystack The string in which the search is performed.
     *                         If the haystack is empty, the method will return false.
     *
     * @return bool Returns true if the needle is found within the haystack,
     *              otherwise returns false. If the haystack is empty, it always returns false.
     */
    public static function contains(string $needle, string $haystack): bool
    {
        if(empty($haystack)){
            return false;
        }

        return str_contains($haystack, $needle);
    }

    public static function firstChar(string $string): string
    {
        return $string[0];
    }

    public static function lastChar(string $string): string
    {
        return $string[strlen($string) - 1];
    }

    /**
     * Protects HTML tags in a string by replacing specific characters with their mapped safe versions.
     * The method ensures that HTML tags are not executed or rendered by escaping angle brackets and other tag-related characters.
     *
     * @param string $string The input string containing potential HTML tags to be protected.
     *
     * @return string A string where the HTML tags have been replaced with their safe representations.
     */
    public static function protectHTMLTags(string $string): string
    {
        preg_match_all('/&lt;(.*?)&gt;|<(.*?)>/sm', $string, $matches);
        if(!empty($matches[0])){
            foreach ($matches[0] as $index => $element){
                $tag = explode(" ", $element);
                $tag = str_replace(["<", ">", "&lt;", "&gt;", "/"], "", $tag[0]);
                // opening tags
                if(!self::contains("/", $element)){
                    $tagMatch = false;
                    // check for the closing tag
                    for($i = ($index+1); $i < count($matches[0]); $i++){
                        $nextElement = $matches[0][$i];
                        $nextTag = explode(" ", $nextElement);
                        $nextTag = str_replace(["<", ">", "&lt;", "&gt;", "/"], "", $nextTag[0]);
                        if($nextTag === $tag){
                            $tagMatch = true;
                        }
                    }
                    if($tagMatch === false){
                        continue;
                    }
                }
                // self closing tag
                else {
                    $closingTag = explode(" ", $element);
                    $closingTag = str_replace(["<", ">", "&lt;", "&gt;"], "", $closingTag[0]);
                    if(empty($closingTag)){
                        continue;
                    }
                    if(!(self::firstChar($closingTag) === "/" or self::lastChar($closingTag) === "/")){
                        continue;
                    }
                }
                $charMap = self::charMap();
                $protectedTag = str_replace(["<", ">", "&lt;", "&gt;"], [$charMap["<"], $charMap[">"], $charMap["&lt;"], $charMap["&gt;"]], $element);
                $string = str_replace($element, $protectedTag, $string);
            }
        }
        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function unprotectHTMLTags(string $string): string
    {
        $charMap = self::charMap();
        return str_replace([$charMap["<"], $charMap[">"], $charMap["&lt;"], $charMap["&gt;"]], ["<", ">", "&lt;", "&gt;"], $string);
    }

    /**
     * @return array<string, string>
     */
    private static function charMap(): array
    {
        return [
            "<"    => "ʃʃʃʃ",
            ">"    => "¶¶¶¶",
            "&lt;" => "ɑɑɑɑ",
            "&gt;" => "ʒʒʒʒ",
        ];
    }
}
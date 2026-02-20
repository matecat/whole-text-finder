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
        return (strlen($string) - mb_strlen($string)) > 0;
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

        foreach ($matches[0] as $index => $element) {
            $shouldProtect = self::contains("/", $element)
                ? self::isSelfClosingTag($element)
                : self::hasMatchingClosingTag($element, array_slice($matches[0], $index + 1));

            if (!$shouldProtect) {
                continue;
            }

            $charMap = self::charMap();
            $protectedTag = str_replace(["<", ">", "&lt;", "&gt;"], [$charMap["<"], $charMap[">"], $charMap["&lt;"], $charMap["&gt;"]], $element);
            $string = str_replace($element, $protectedTag, $string);
        }

        return $string;
    }

    /**
     * Strips all angle-bracket variants and slashes from the first token of a tag string,
     * returning a bare tag name.
     */
    private static function normaliseTag(string $element): string
    {
        $firstToken = explode(" ", $element)[0];
        return str_replace(["<", ">", "&lt;", "&gt;", "/"], "", $firstToken);
    }

    /**
     * Returns true when at least one of the subsequent elements shares the same tag name,
     * indicating a matching closing tag exists for the current opening tag.
     *
     * @param array<int, string> $remainingElements
     */
    private static function hasMatchingClosingTag(string $element, array $remainingElements): bool
    {
        $tag = self::normaliseTag($element);

        foreach ($remainingElements as $next) {
            if (self::normaliseTag($next) === $tag) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true when the element is a valid self-closing tag,
     * i.e. its bare name starts or ends with a forward slash.
     */
    private static function isSelfClosingTag(string $element): bool
    {
        $closingTag = str_replace(["<", ">", "&lt;", "&gt;"], "", explode(" ", $element)[0]);

        if (empty($closingTag)) {
            return false;
        }

        return self::firstChar($closingTag) === "/" || self::lastChar($closingTag) === "/";
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
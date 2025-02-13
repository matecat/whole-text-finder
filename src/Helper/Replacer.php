<?php

namespace Matecat\Finder\Helper;

class Replacer
{
    /**
     * This method replaces content avoiding to replace html content.
     *
     * Example:
     * $haystack = "Beauty -> 2 Anti-Akne Gesichtsreiniger Schlankmacher <g id=\"2\">XXX</g>";
     * $needle = 2;
     * $replacement = "test";
     *
     * $expected = "Beauty -> test Anti-Akne Gesichtsreiniger Schlankmacher <g id=\"2\">XXX</g>";
     *
     * @param string $pattern
     * @param string $replacement
     * @param string $haystack
     *
     * @return string|string[]
     */
    public static function replace($pattern, $replacement, $haystack)
    {
        return preg_replace(self::getModifiedRegexPattern($pattern), $replacement, $haystack);
    }

    /**
     * Modifies regex pattern for avoiding replacement of html content
     *
     * This function appends to the original $pattern a new optional regex.
     *
     * Example:
     *
     * /ciao/iu
     *
     * is converted to:
     *
     * /(\|\|\|\||<.*?>|%{.*?})(*SKIP)(*FAIL)|ciao/iu
     *
     * @param string $pattern
     *
     * @return string
     */
    private static function getModifiedRegexPattern($pattern)
    {
        return '/(\|\|\|\||<.*?>|&lt;.*?&gt;|%{.*?})(*SKIP)(*FAIL)|'. ltrim($pattern, $pattern[0]);
    }
}

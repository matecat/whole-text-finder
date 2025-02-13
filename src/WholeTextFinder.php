<?php

namespace Matecat\Finder;

use Matecat\Finder\Helper\RegexEscaper;
use Matecat\Finder\Helper\Strings;
use Matecat\Finder\Helper\Replacer;

class WholeTextFinder
{
    /**
     * @param string $haystack
     * @param string $needle
     * @param bool   $skipHtmlEntities
     * @param bool   $exactMatch
     * @param bool   $caseSensitive
     * @param bool   $preserveNbsps
     *
     * @return array
     */
    public static function find($haystack, $needle, $skipHtmlEntities = true, $exactMatch = false, $caseSensitive = false, $preserveNbsps = false)
    {
        $patternAndHaystack = self::getPatternAndHaystack($haystack, $needle, $skipHtmlEntities, $exactMatch, $caseSensitive, $preserveNbsps);

        preg_match_all($patternAndHaystack['pattern'], $patternAndHaystack['haystack'], $matches, PREG_OFFSET_CAPTURE);

        if($skipHtmlEntities === true){
            $patternAndHaystack['haystack'] = Strings::unprotectHTMLTags($patternAndHaystack['haystack']);
        }

        self::mbCorrectMatchPositions($patternAndHaystack['haystack'], $matches);

        return $matches[0];
    }

    /**
     * Correct position for multi byte strings
     *
     * @param string $haystack
     * @param array $matches
     *
     * @return mixed
     */
    private static function mbCorrectMatchPositions( $haystack, &$matches)
    {
        if(!Strings::isMultibyte($haystack) ){
            return $matches[0];
        }

        foreach ($matches[0] as $index => $match){
            $word = $match[0];
            $position = $match[1];

            $correctPosition = self::mbFindTheCorrectPosition($haystack, $word, $position);
            $matches[0][$index][1] = $correctPosition;
        }
    }

    /**
     * @param string $haystack
     * @param string $word
     * @param int $position
     *
     * @return int
     */
    private static function mbFindTheCorrectPosition( $haystack, $word, &$position)
    {
        $wordCheck = mb_substr($haystack, $position, mb_strlen($word));

        if($wordCheck !== $word){
            $position = $position - 1;

            self::mbFindTheCorrectPosition($haystack, $word, $position);
        }

        return $position;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @param bool $skipHtmlEntities
     * @param bool $exactMatch
     * @param bool $caseSensitive
     * @param bool $preserveNbsps
     *
     * @return array
     */
    private static function getPatternAndHaystack($haystack, $needle, $skipHtmlEntities = true, $exactMatch = false, $caseSensitive = false, $preserveNbsps = false)
    {
        $pattern = self::getSearchPattern($needle, $skipHtmlEntities, $exactMatch, $caseSensitive, $preserveNbsps);
        $haystack = ($skipHtmlEntities) ? Strings::htmlEntityDecode(Strings::protectHTMLTags($haystack)) : $haystack;
        $haystack = (false === $preserveNbsps) ? Strings::cutNbsps($haystack) : $haystack;

        return [
            'pattern' => $pattern,
            'haystack' => $haystack,
        ];
    }

    /**
     * @param string $needle
     * @param bool   $skipHtmlEntities
     * @param bool   $exactMatch
     * @param bool   $caseSensitive
     * @param bool   $preserveNbsps
     *
     * @return string
     */
    private static function getSearchPattern($needle, $skipHtmlEntities = true, $exactMatch = false, $caseSensitive = false, $preserveNbsps = false)
    {
        $needle = (false === $preserveNbsps) ? Strings::cutNbsps($needle) : $needle;
        $needle = ($skipHtmlEntities) ? Strings::htmlEntityDecode($needle) : $needle;

        $pattern = '/';
        $pattern .= ($exactMatch) ? RegexEscaper::escapeWholeTextPattern($needle) : RegexEscaper::escapeRegularPattern($needle);
        $pattern .= '/';
        $pattern .= (false === $caseSensitive) ? 'i' : '';
        $pattern .= ($skipHtmlEntities) ? 'u' : '';

        return $pattern;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @param string $replacement
     * @param bool $skipHtmlEntities
     * @param bool $exactMatch
     * @param bool $caseSensitive
     * @param bool $preserveNbsps
     *
     * @return array
     */
    public static function findAndReplace($haystack, $needle, $replacement, $skipHtmlEntities = true, $exactMatch = false, $caseSensitive = false, $preserveNbsps = false)
    {
        $patternAndHaystack = self::getPatternAndHaystack($haystack, $needle, $skipHtmlEntities, $exactMatch, $caseSensitive, $preserveNbsps);
        $replacement = Replacer::replace($patternAndHaystack['pattern'], $replacement, $patternAndHaystack['haystack']);

        if($skipHtmlEntities === true){
            $replacement = Strings::unprotectHTMLTags($replacement);
        }

        return [
            'replacement' => $replacement,
            'occurrencies' => self::find($haystack, $needle, $skipHtmlEntities, $exactMatch, $caseSensitive, $preserveNbsps),
        ];
    }
}

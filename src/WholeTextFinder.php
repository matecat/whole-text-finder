<?php

declare(strict_types=1);

namespace Matecat\Finder;

use Matecat\Finder\Helper\RegexEscaper;
use Matecat\Finder\Helper\Strings;
use Matecat\Finder\Helper\Replacer;

class WholeTextFinder
{
    /**
     * Searches for occurrences of a specified substring (needle) in a given string (haystack).
     *
     * @param string $haystack The string in which to search for the needle.
     * @param string $needle The substring to find within the haystack.
     * @param bool $skipHtmlEntities If true, skips matching HTML entities in the haystack.
     * @param bool $exactMatch If true, ensures the needle exactly matches a portion of the haystack.
     * @param bool $caseSensitive If true, performs a case-sensitive search.
     * @param bool $preserveNbsps If true, preserves non-breaking spaces (nbsp) in the haystack.
     *
     * @return array<int, array{string, int}> An array of matches, where each match is a tuple containing the matched substring and its start position in the haystack.
     */
    public static function find(string $haystack, string $needle, bool $skipHtmlEntities = true, bool $exactMatch = false, bool $caseSensitive = false, bool $preserveNbsps = false): array
    {
        $patternAndHaystack = self::getPatternAndHaystack($haystack, $needle, $skipHtmlEntities, $exactMatch, $caseSensitive, $preserveNbsps);

        preg_match_all($patternAndHaystack['pattern'], $patternAndHaystack['haystack'], $matches, PREG_OFFSET_CAPTURE);
        /** @var array<int, list<array{0: string, 1: int}>> $matches */

        if($skipHtmlEntities === true){
            $patternAndHaystack['haystack'] = Strings::unprotectHTMLTags($patternAndHaystack['haystack']);
        }

        self::mbCorrectMatchPositions($patternAndHaystack['haystack'], $matches);

        return $matches[0];
    }

    /**
     * Corrects the byte-based match positions in a multibyte string to their proper character-based positions.
     *
     * @param string $haystack The multibyte string where matches were found.
     * @param array<int, list<array{0: string, 1: int}>> &$matches The array of matched substrings and their byte-based positions.
     *                                                             This array will be updated with corrected character-based positions.
     *
     * @return void
     */
    private static function mbCorrectMatchPositions(string $haystack, array &$matches): void
    {
        if(!Strings::isMultibyte($haystack) ){
            return;
        }

        foreach ($matches[0] as $index => $match){
            $word = $match[0];
            $position = $match[1];

            $correctPosition = self::mbFindTheCorrectPosition($haystack, $word, $position);
            $matches[0][$index][1] = $correctPosition;
        }
    }

    /**
     * Recursively adjusts the position in the haystack to correctly align with the given word.
     *
     * @param string $haystack The string in which to locate the specified word.
     * @param string $word The word to align the position against within the haystack.
     * @param int &$position The current position in the haystack, which is updated during recursion.
     *
     * @return int The corrected position in the haystack where the word is properly aligned.
     */
    private static function mbFindTheCorrectPosition(string $haystack, string $word, int &$position): int
    {
        $wordCheck = mb_substr($haystack, $position, mb_strlen($word));

        if($wordCheck !== $word){
            $position = $position - 1;

            self::mbFindTheCorrectPosition($haystack, $word, $position);
        }

        return $position;
    }

    /**
     * Prepares the search pattern and processes the haystack string for substring matching.
     *
     * @param string $haystack The input string in which the search will be conducted.
     * @param string $needle The substring for which a search pattern will be generated.
     * @param bool $skipHtmlEntities If true, processes the haystack to exclude HTML entities from matching and decodes it for search purposes.
     * @param bool $exactMatch If true, ensures that the search pattern matches the needle exactly within the haystack.
     * @param bool $caseSensitive If true, generates a search pattern that respects case sensitivity.
     * @param bool $preserveNbsps If false, removes non-breaking spaces (nbsp) from the haystack during preprocessing.
     *
     * @return array{pattern: string, haystack: string} An associative array containing:
     *         - `pattern` (string): The generated search pattern based on the provided needle and search parameters.
     *         - `haystack` (string): The processed haystack string ready for substring matching.
     */
    private static function getPatternAndHaystack(string $haystack, string $needle, bool $skipHtmlEntities = true, bool $exactMatch = false, bool $caseSensitive = false, bool $preserveNbsps = false): array
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
     * Constructs a regular expression search pattern based on the given parameters.
     *
     * @param string $needle The text or string to build the search pattern for.
     * @param bool $skipHtmlEntities If true, HTML entities in the input string are decoded before forming the pattern. Defaults to true.
     * @param bool $exactMatch If true, the generated pattern will match the entire string exactly. Defaults to false.
     * @param bool $caseSensitive If true, the pattern will be case-sensitive. If false, the pattern is case-insensitive. Defaults to false.
     * @param bool $preserveNbsps If true, non-breaking spaces in the input will be preserved. If false, they are removed. Defaults to false.
     *
     * @return string A regular expression pattern string configured according to the provided parameters.
     */
    private static function getSearchPattern(string $needle, bool $skipHtmlEntities = true, bool $exactMatch = false, bool $caseSensitive = false, bool $preserveNbsps = false): string
    {
        $needle = (false === $preserveNbsps) ? Strings::cutNbsps($needle) : $needle;
        $needle = ($skipHtmlEntities) ? Strings::htmlEntityDecode(Strings::protectHTMLTags($needle)) : $needle;

        $pattern = '/';
        $pattern .= ($exactMatch) ? RegexEscaper::escapeWholeTextPattern($needle) : RegexEscaper::escapeRegularPattern($needle);
        $pattern .= '/';
        $pattern .= (false === $caseSensitive) ? 'i' : '';
        $pattern .= ($skipHtmlEntities) ? 'u' : '';

        return $pattern;
    }

    /**
     * Searches for a pattern within a given string and replaces it with the specified replacement value.
     *
     * @param string $haystack The string in which the search will be performed.
     * @param string $needle The substring or pattern to search for within the haystack.
     * @param string $replacement The string to replace the found occurrences of the needle.
     * @param bool $skipHtmlEntities If true, HTML entities in the haystack and needle are decoded before processing. Defaults to true.
     * @param bool $exactMatch If true, the search will look for an exact match of the needle. Defaults to false.
     * @param bool $caseSensitive If true, the search will be case-sensitive. If false, it will be case-insensitive. Defaults to false.
     * @param bool $preserveNbsps If true, non-breaking spaces in the haystack and needle will be preserved. If false, they are removed. Defaults to false.
     *
     * @return array{replacement: string, occurrences: array<int, array{0: string, 1: int}>} An associative array with:
     *                   - 'replacement': The resulting string after replacements.
     *                   - 'occurrences': The list of occurrences (each as [matched string, byte offset]) found in the haystack.
     */
    public static function findAndReplace(string $haystack, string $needle, string $replacement, bool $skipHtmlEntities = true, bool $exactMatch = false, bool $caseSensitive = false, bool $preserveNbsps = false): array
    {
        $patternAndHaystack = self::getPatternAndHaystack($haystack, $needle, $skipHtmlEntities, $exactMatch, $caseSensitive, $preserveNbsps);
        $replacement = Replacer::replace($patternAndHaystack['pattern'], $replacement, $patternAndHaystack['haystack']);

        if($skipHtmlEntities === true){
            $replacement = Strings::unprotectHTMLTags($replacement);
        }

        return [
            'replacement' => $replacement,
            'occurrences' => self::find($haystack, $needle, $skipHtmlEntities, $exactMatch, $caseSensitive, $preserveNbsps),
        ];
    }
}

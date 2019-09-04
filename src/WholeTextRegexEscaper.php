<?php

namespace Matecat\Finder;

class WholeTextRegexEscaper
{
    /**
     * @param string $needle
     *
     * @return string
     */
    public static function obtain($needle)
    {
        $escapedNeedle = preg_quote($needle);
        $escapedNeedle = str_replace("/", "\/", $escapedNeedle);
        $splittedNeedle = mb_str_split($escapedNeedle);

        $final = '';

        foreach ($splittedNeedle as $index => $letter) {
            if ($index === self::getFirstBounduaryPosition($splittedNeedle)) {
                $final .= "\\b";
            }

            $final .= $letter;

            if ($index === self::getLastBounduaryPosition($splittedNeedle)) {
                $final .= "\\b";
            }
        }

        return $final;
    }

    /**
     * @param array $splittedNeedle
     *
     * @return int
     */
    private static function getFirstBounduaryPosition($splittedNeedle)
    {
        for ($i=0; $i < count($splittedNeedle); $i++) {
            if (self::isBoundary($splittedNeedle[$i])) {
                return $i;
            }
        }

        return -1;
    }

    /**
     * @param array $splittedNeedle
     *
     * @return int
     */
    private static function getLastBounduaryPosition($splittedNeedle)
    {
        for ($i=(count($splittedNeedle)-1); $i >= 0; $i--) {
            if (self::isBoundary($splittedNeedle[$i])) {
                return $i;
            }
        }

        return -1;
    }

    /**
     * @param string $letter
     *
     * @return bool
     */
    private static function isBoundary($letter)
    {
        return ( preg_match("/[A-Za-z0-9_]/", $letter) > 0 ) ? true : false;
    }
}
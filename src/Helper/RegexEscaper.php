<?php

declare(strict_types=1);

namespace Matecat\Finder\Helper;

class RegexEscaper
{
    /**
     * @param string $needle
     *
     * @return string
     */
    public static function escapeWholeTextPattern(string $needle): string
    {
        $escapedNeedle = self::escapeRegularPattern($needle);
        $splittedNeedle = Strings::split($escapedNeedle);

        $final = '';

        foreach ($splittedNeedle as $index => $letter) {
            if ($index === self::getFirstBoundaryPosition($splittedNeedle)) {
                $final .= "\\b";
            }

            $final .= $letter;

            if ($index === self::getLastBoundaryPosition($splittedNeedle)) {
                $final .= "\\b";
            }
        }

        return $final;
    }

    /**
     * @param array<int, string> $splitNeedle
     *
     * @return int
     */
    private static function getFirstBoundaryPosition(array $splitNeedle): int
    {
        for ($i=0; $i < count($splitNeedle); $i++) {
            if (self::isBoundary($splitNeedle[$i])) {
                return $i;
            }
        }

        return -1;
    }

    /**
     * @param array<int, string> $splitNeedle
     *
     * @return int
     */
    private static function getLastBoundaryPosition(array $splitNeedle): int
    {
        for ($i=(count($splitNeedle)-1); $i >= 0; $i--) {
            if (self::isBoundary($splitNeedle[$i])) {
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
    private static function isBoundary(string $letter): bool
    {
        return preg_match("/\w/iu", $letter) > 0;
    }

    /**
     * @param string $needle
     *
     * @return string
     */
    public static function escapeRegularPattern(string $needle): string
    {
        $needle = preg_quote($needle);

        return str_replace("/", "\/", $needle);
    }
}

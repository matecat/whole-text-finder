<?php

namespace Matecat\Finder\Tests;

use Matecat\Finder\Helper\RegexEscaper;
use PHPUnit\Framework\TestCase;

class RegexEscaperTest extends TestCase
{
    /**
     * @test
     */
    public function get_the_regular_regex()
    {
        $string  = "PHP é 😀 il @ (linguaggio) ggio #1 del mondo. 😀";
        $escaped = RegexEscaper::escapeWholeTextPattern($string);
        $expected = "\\bPHP é 😀 il @ \\(linguaggio\\) ggio \\#1 del mondo\\b\\. 😀";

        $this->assertEquals($escaped, $expected);
    }

    /**
     * @test
     */
    public function get_the_whole_text_regex()
    {
        $string  = "PHP é 😀 il @ (linguaggio) ggio #1 del mondo. 😀";
        $escaped = RegexEscaper::escapeWholeTextPattern($string);
        $expected = "\\bPHP é 😀 il @ \\(linguaggio\\) ggio \\#1 del mondo\\b\\. 😀";

        $this->assertEquals($escaped, $expected);

        $password = "I~/nuU'O8)+%M\o0h4xV";
        $escaped = RegexEscaper::escapeWholeTextPattern($password);
        $expected = "\bI~\/nuU'O8\)\+%M\\\\o0h4xV\b";

        $this->assertEquals($escaped, $expected);
    }

    /**
     * @test
     */
    public function get_the_whole_text_regex_with_cyrillic_words()
    {
        $string  = "Тест & Тестирование";
        $escaped = RegexEscaper::escapeWholeTextPattern($string);
        $expected = "\\bТест & Тестирование\\b";

        $this->assertEquals($escaped, $expected);
    }

    /**
     * @test
     */
    public function get_the_whole_text_regex_with_greek_words()
    {
        $string  = "Χάρηκα πολύ";
        $escaped = RegexEscaper::escapeWholeTextPattern($string);
        $expected = "\\bΧάρηκα πολύ\\b";

        $this->assertEquals($escaped, $expected);
    }
}

<?php

namespace Matecat\Finder\Tests;

use Matecat\Finder\WholeTextRegexEscaper;
use PHPUnit\Framework\TestCase;

class WholeTextRegexEscaperTest extends TestCase
{
    /**
     * @test
     */
    public function get_the_whole_text_regex()
    {
        $string  = "PHP é 😀 il @ (linguaggio) ggio #1 del mondo. 😀";
        $escaped = WholeTextRegexEscaper::obtain($string);
        $expected = "\\bPHP é 😀 il @ \\(linguaggio\\) ggio \\#1 del mondo\\b\\. 😀";

        $this->assertEquals($escaped, $expected);

        $password = "I~/nuU'O8)+%M\o0h4xV";
        $escaped = WholeTextRegexEscaper::obtain($password);
        $expected = "\bI~\/nuU'O8\)\+%M\\\\o0h4xV\b";

        $this->assertEquals($escaped, $expected);
    }
}

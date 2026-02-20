<?php

declare(strict_types=1);

namespace Matecat\Finder\Tests;

use Matecat\Finder\Helper\Strings;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{
    #[Test]
    public function html_entity_decode(): void
    {
        $input  = "&apos; &lt;a href='##'/&gt;This is a string&lt;/a&gt; with HTML entities;&#13;&#13;They must be skipped!";
        $output = "' <a href='##'/>This is a string</a> with HTML entities;\r\rThey must be skipped!";

        $this->assertEquals(Strings::htmlEntityDecode($input), $output);
    }

    #[Test]
    public function is_multibyte(): void
    {
        $string = "La casa e bella";
        $string2 = "La casa è bella";

        $this->assertFalse(Strings::isMultibyte($string));
        $this->assertTrue(Strings::isMultibyte($string2));
    }

    #[Test]
    public function protectHTMLTag(): void
    {
        $string = '<p>This is a p tag</p><p>This is a second p tag</p>';

        $protected = Strings::protectHTMLTags($string);
        $unprotected = Strings::unprotectHTMLTags($protected);

        $this->assertEquals($string, $unprotected);
    }

    #[Test]
    public function protectEscapedHTMLTags(): void
    {
        $string = '&lt;p&gt;This is a p tag&lt;/p&gt;&lt;p&gt;This is a second p tag with a &lt; sign &lt;/p&gt;';

        $protected = Strings::protectHTMLTags($string);
        $unprotected = Strings::unprotectHTMLTags($protected);

        $this->assertEquals($string, $unprotected);
    }

    #[Test]
    public function moreTestWithProtectedEscapedHTMLTags(): void
    {
        $strings = [
            [
                "string" => "&lt;p id='x' data-content='xxxx'&gt;Handling&lt;/p&gt;",
                "expected" => "ɑɑɑɑp id='x' data-content='xxxx'ʒʒʒʒHandlingɑɑɑɑ/pʒʒʒʒ"
            ],
            [
                "string" => "&lt;p&gt;Handling &lt;br/&gt; of this string&lt;/p&gt;",
                "expected" => "ɑɑɑɑpʒʒʒʒHandling ɑɑɑɑbr/ʒʒʒʒ of this stringɑɑɑɑ/pʒʒʒʒ"
            ],
        ];

        foreach ($strings as $s){
            $string = $s['string'];
            $expected = $s['expected'];
            $protected = Strings::protectHTMLTags($string);

            $this->assertEquals($expected, $protected, "Not matching exptected string: " . $expected);

            $unprotected = Strings::unprotectHTMLTags($protected);

            $this->assertEquals($string, $unprotected);
        }
    }

    #[Test]
    public function contains_returns_false_on_empty_haystack(): void
    {
        $this->assertFalse(Strings::contains('anything', ''));
    }

    #[Test]
    public function dontEscapeAPassword(): void
    {
        $passwords = [
            '3mM?t<T0&3AhFl`>#',
            '<~&6^{ck/Px>ky(0wzx',
        ];

        foreach ($passwords as $password){
            $protected = Strings::protectHTMLTags($password);

            $this->assertEquals($protected, $password, "Password not matching: " . $password);
        }
    }
}
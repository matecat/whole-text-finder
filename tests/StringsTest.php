<?php

namespace Matecat\Finder\Tests;

use Matecat\Finder\Helper\Strings;
use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{
    /**
     * @test
     */
    public function html_entity_decode()
    {
        $input  = "&apos; &lt;a href='##'/&gt;This is a string&lt;/a&gt; with HTML entities;&#13;&#13;They must be skipped!";
        $output = "' <a href='##'/>This is a string</a> with HTML entities;\r\rThey must be skipped!";

        $this->assertEquals(Strings::htmlEntityDecode($input), $output);
    }

    /**
     * @test
     */
    public function is_multibyte()
    {
        $string = "La casa e bella";
        $string2 = "La casa è bella";

        $this->assertFalse(Strings::isMultibyte($string));
        $this->assertTrue(Strings::isMultibyte($string2));
    }

    /**
     * @test
     */
    public function protectHTMLTag()
    {
        $string = '<p>This is a p tag</p><p>This is a second p tag</p>';

        $protected = Strings::protectHTMLTags($string);
        $unprotected = Strings::unprotectHTMLTags($protected);

        $this->assertEquals($string, $unprotected);
    }

    /**
     * @test
     */
    public function protectEscapedHTMLTags()
    {
        $string = '&lt;p&gt;This is a p tag&lt;/p&gt;&lt;p&gt;This is a second p tag with a &lt; sign &lt;/p&gt;';

        $protected = Strings::protectHTMLTags($string);
        $unprotected = Strings::unprotectHTMLTags($protected);

        $this->assertEquals($string, $unprotected);
    }

    /**
     * @test
     */
    public function moreTestWithProtectedEscapedHTMLTags()
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

    /**
     * @test
     */
    public function dontEscapeAPassword()
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
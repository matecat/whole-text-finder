<?php

declare(strict_types=1);

namespace Matecat\Finder\Tests;

use Matecat\Finder\WholeTextFinder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WholeTextFinderTest extends TestCase
{
    #[Test]
    public function can_detect_positions(): void
    {
        $haystack  = "La casa è bella bella";
        $needle = "bella";

        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);

        $this->assertCount(2, $matches);
        $this->assertEquals(10, $matches[0][1]);
        $this->assertEquals(16, $matches[1][1]);
    }

    #[Test]
    public function search_with_square_brackets(): void
    {
        $haystack  = "Text with [[square brackets]]";
        $needle = "[[";

        $matches = WholeTextFinder::find($haystack, $needle, true, false, false);

        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_with_curly_brackets(): void
    {
        $haystack  = "Text with {{curly brackets}}";
        $needle = "{{";

        $matches = WholeTextFinder::find($haystack, $needle, true, false, false);

        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_with_and_symbol(): void
    {
        $haystack  = "Storage &amp; Handling:";

        $needle = "&";
        $matches = WholeTextFinder::find($haystack, $needle, false, false, false);

        $this->assertCount(1, $matches);

        $needle = "&";
        $matches = WholeTextFinder::find($haystack, $needle, true, false, false);

        $this->assertCount(1, $matches);

        $needle = "&";
        $matches = WholeTextFinder::find($haystack, $needle, false, true, false);

        $this->assertCount(1, $matches);

        $needle = "&amp;";
        $matches = WholeTextFinder::find($haystack, $needle, false, false, false);

        $this->assertCount(1, $matches);

        $needle = "&amp;";
        $matches = WholeTextFinder::find($haystack, $needle, true, false, false);

        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_with_at_symbol(): void
    {
        $haystack  = "Text with @ symbol";
        $needle = "@";

        $matches = WholeTextFinder::find($haystack, $needle, false, false, false);

        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_with_apos(): void
    {
        $haystack  = "Text with &apos; symbol";
        $needle = "'";

        $matches = WholeTextFinder::find($haystack, $needle, false, false, false);

        $this->assertCount(0, $matches);

        $matches = WholeTextFinder::find($haystack, $needle, true, false, false);

        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_in_texts_with_dates(): void
    {
        $haystackWithDate  = "21/06/2019";
        $needleWithDate = "21/06/2019";

        $matches = WholeTextFinder::find($haystackWithDate, $needleWithDate, true, false, true);

        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_in_texts_with_nbsps(): void
    {
        $haystackWithNbsp  = "Lawful basis for processing including basis of legitimate interest";
        $needleWithoutNbsp = "Lawful basis for processing including basis of legitimate interest";

        $matches = WholeTextFinder::find($haystackWithNbsp, $needleWithoutNbsp, true, true, true);
        $matches2 = WholeTextFinder::find($haystackWithNbsp, $needleWithoutNbsp, true, false, true);

        $this->assertCount(1, $matches);
        $this->assertCount(1, $matches2);
    }

    #[Test]
    public function find_should_return_correct_matches(): void
    {
        $haystack  = "PHP é il @ linguaggio ggio #1 del mondo.";
        $needle = "ggio";

        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $matches2 = WholeTextFinder::find($haystack, $needle, true, false, true);

        $expected = [
            [
                0 => 'ggio',
                1 => 22
            ]
        ];
        $expected2 = [
            [
                0 => 'ggio',
                1 => 17
            ],
            [
                0 => 'ggio',
                1 => 22
            ]
        ];

        $this->assertEquals($expected, $matches);
        $this->assertEquals($expected2, $matches2);
    }

    #[Test]
    public function search_in_texts(): void
    {
        $haystack  = "PHP é il linguaggio numero 1 del mondo.";

        $needle = "mondo";
        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $this->assertCount(1, $matches);

        $needle = "mondo.";
        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $this->assertCount(1, $matches);

        $needle = "é";
        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $this->assertCount(1, $matches);

        $haystack  = "PHP PHP is the #1 web scripting PHP language of choice.";

        $needle = "php";
        $matches = WholeTextFinder::find($haystack, $needle);
        $this->assertCount(3, $matches);

        $needle = "php";
        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $this->assertCount(0, $matches);

        $needle = "#1";
        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $this->assertCount(1, $matches);

        $haystack  = "Lawful basis for processing including basis of legitimate interest";

        $needle = "including";
        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $this->assertCount(1, $matches);

        $haystack  = "To process and deliver your order including: (a) Manage payments, fees and charges (b) Collect and recover money owed to us";

        $needle = "including:";
        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_in_texts_with_html_entities(): void
    {
        $haystack  = "&lt;a href='##'/&gt;This is a string&lt;/a&gt; with HTML entities;&#13;&#13;They must be skipped!";

        $needle = "&";
        $matches = WholeTextFinder::find($haystack, $needle, true);
        $this->assertCount(0, $matches);

        $needle = ";";
        $matches = WholeTextFinder::find($haystack, $needle, true);
        $this->assertCount(1, $matches);

        $needle = "&lt;a";
        $matches = WholeTextFinder::find($haystack, $needle, false);
        $this->assertCount(1, $matches);

        $needle = "<a";
        $matches = WholeTextFinder::find($haystack, $needle, true);
        $this->assertCount(1, $matches);

        $needle = "<A";
        $matches = WholeTextFinder::find($haystack, $needle, true, false, true);
        $this->assertCount(0, $matches);

        $needle = "<a";
        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);

        $haystack  = "&quot;This is a quotation&quot; - says the donkey.";

        $needle = "quot";
        $matches = WholeTextFinder::find($haystack, $needle, true);
        $this->assertCount(1, $matches);
        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(0, $matches);

        $needle = ";";
        $matches = WholeTextFinder::find($haystack, $needle, true);
        $this->assertCount(0, $matches);

        $haystack  = "&quot;This is a quotation&quot; - says the donkey.";

        $needle = "&quot;";
        $matches = WholeTextFinder::find($haystack, $needle, false);
        $this->assertCount(2, $matches);
    }

    #[Test]
    public function search_in_texts_with_ampersand(): void
    {
        $haystack = 'Тест &amp; Тестирование';
        $needle = "Тест &amp; Тестирование";

        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_in_texts_with_japanese_ideograms(): void
    {
        $haystack = '「ハッスルの日」開催について';
        $needle = "ハッスルの日";

        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);

        $needle = "開催について";

        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_in_texts_with_cyrillic_words(): void
    {
        $haystackArray = [
            'Тест и тест',
            'Тест & Тестирование'
        ];

        // 1. not exact match, case insensitive
        $matches = [];
        $needle = 'тест';

        foreach ($haystackArray as $haystack){
            $matches[] = WholeTextFinder::find($haystack, $needle, true, false, false);
        }

        $this->assertCount(2, $matches[0]);
        $this->assertCount(2, $matches[1]);

        // 2. exact match, case insensitive
        $matches = [];

        foreach ($haystackArray as $haystack){
            $matches[] = WholeTextFinder::find($haystack, $needle, true, true, false);
        }

        $this->assertCount(2, $matches[0]);
        $this->assertCount(1, $matches[1]);

        // 3. not exact match, case sensitive
        $matches = [];
        $needle = 'Тест';

        foreach ($haystackArray as $haystack){
            $matches[] = WholeTextFinder::find($haystack, $needle, true, false, true);
        }

        $this->assertCount(1, $matches[0]);
        $this->assertCount(2, $matches[1]);

        // 4. exact match, case sensitive
        $matches = [];
        $needle = 'Тест';

        foreach ($haystackArray as $haystack){
            $matches[] = WholeTextFinder::find($haystack, $needle, true, true, true);
        }

        $this->assertCount(1, $matches[0]);
        $this->assertCount(1, $matches[1]);
    }

    #[Test]
    public function search_in_texts_with_arabic_words(): void
    {
        $haystack = '. سعدت بلقائك.';
        $needle = ". سعدت";

        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);
    }

    #[Test]
    public function search_in_texts_with_greek_words(): void
    {
        $haystack = 'Χάρηκα πολύ';

        $needle = 'Χάρηκα';

        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);

        $needle = 'Χάρηκ';

        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(0, $matches);

        $needle = 'Πολύ';

        $matches = WholeTextFinder::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);

        $matches = WholeTextFinder::find($haystack, $needle, true, true, true);
        $this->assertCount(0, $matches);
    }
}

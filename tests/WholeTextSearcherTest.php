<?php

namespace Searcher\Tests;

use PHPUnit\Framework\TestCase;
use Searcher\WholeTextSearcher;

class WholeTextSearcherTest extends TestCase
{
    /**
     * @test
     */
    public function search_in_texts()
    {
        $haystack  = "PHP PHP is the web scripting PHP language of choice.";

        $needle = "php";
        $matches = WholeTextSearcher::find($haystack, $needle);
        $this->assertCount(3, $matches);

        $needle = "php";
        $matches = WholeTextSearcher::find($haystack, $needle, true,true, true);
        $this->assertCount(0, $matches);
    }

    /**
     * @test
     */
    public function search_in_texts_with_html_entities()
    {
        $haystack  = "&lt;a href='##'/&gt;This is a string&lt;/a&gt; with HTML entities;&#13;&#13;They must be skipped!";

        $needle = "&";
        $matches = WholeTextSearcher::find($haystack, $needle, true);
        $this->assertCount(0, $matches);

        $needle = ";";
        $matches = WholeTextSearcher::find($haystack, $needle, true);
        $this->assertCount(1, $matches);

        $needle = "&lt;a";
        $matches = WholeTextSearcher::find($haystack, $needle, false);
        $this->assertCount(1, $matches);

        $needle = "<a";
        $matches = WholeTextSearcher::find($haystack, $needle, true);
        $this->assertCount(1, $matches);

        $needle = "<A";
        $matches = WholeTextSearcher::find($haystack, $needle, true, false, true);
        $this->assertCount(0, $matches);

        $needle = "<a";
        $matches = WholeTextSearcher::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);

        $haystack  = "&quot;This is a quotation&quot; - says the donkey.";

        $needle = "quot";
        $matches = WholeTextSearcher::find($haystack, $needle, true);
        $this->assertCount(1, $matches);
        $matches = WholeTextSearcher::find($haystack, $needle, true, true);
        $this->assertCount(0, $matches);

        $needle = ";";
        $matches = WholeTextSearcher::find($haystack, $needle, true);
        $this->assertCount(0, $matches);

        $haystack  = "&quot;This is a quotation&quot; - says the donkey.";

        $needle = "&quot;";
        $matches = WholeTextSearcher::find($haystack, $needle, false);
        $this->assertCount(2, $matches);
    }

    /**
     * @test
     */
    public function search_in_texts_with_japanese_ideograms()
    {
        $haystack = '「ハッスルの日」開催について';
        $needle = "ハッスルの日";

        $matches = WholeTextSearcher::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);
    }

    /**
     * @test
     */
    public function search_in_texts_with_arabic_words()
    {
        $haystack = '. سعدت بلقائك.';
        $needle = "سعدت";

        $matches = WholeTextSearcher::find($haystack, $needle, true, true);
        $this->assertCount(1, $matches);
    }
}
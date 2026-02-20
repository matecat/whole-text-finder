<?php

declare(strict_types=1);

namespace Matecat\Finder\Tests;

use Matecat\Finder\WholeTextFinder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WholeTextFinderReplacementTest extends TestCase
{
    #[Test]
    public function find_and_replace_test_on_greek_text(): void
    {
        $haystack = 'Δύο παράγοντες καθόρισαν την αντίληψή μου για την Τενεσί Ουίλιαμς και τη σκηνική παρουσίαση των κειμένων: η Maria Britneva και η Annette Saddik, αφετέρου.';
        $needle = 'και';
        $replacement = 'test';

        $expected = [
            'replacement' => 'Δύο παράγοντες καθόρισαν την αντίληψή μου για την Τενεσί Ουίλιαμς test τη σκηνική παρουσίαση των κειμένων: η Maria Britneva test η Annette Saddik, αφετέρου.',
            'occurrences' => [
                [$needle, 66],
                [$needle, 123],
            ],
        ];
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

        $this->assertEquals($expected, $matches);
    }

    #[Test]
    public function find_and_replace_test_close_to_period_on_greek_text(): void
    {
        $haystack = 'Δύο παράγοντες καθόρισαν την αντίληψή μου για την Τενεσί Ουίλιαμς και τη σκηνική παρουσίαση των κειμένων: η Maria Britneva και η Annette Saddik, αφετέρου.';
        $needle = 'αφετέρου';
        $replacement = 'test';

        $expected = 'Δύο παράγοντες καθόρισαν την αντίληψή μου για την Τενεσί Ουίλιαμς και τη σκηνική παρουσίαση των κειμένων: η Maria Britneva και η Annette Saddik, test.';
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

        $this->assertEquals($expected, $matches['replacement']);
    }

    #[Test]
    public function find_and_replace_must_skip_matecat_ph_tags(): void
    {
        $haystack = "Si asistent për përvojën %{experience_name}, ti do të ndihmosh %{primary_host_name} ta përmirësojë edhe më shumë këtë përvojë.";
        $needle = 'host';
        $replacement = 'test';

        $expected = $haystack;
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

        $this->assertEquals($expected, $matches['replacement']);
    }

    #[Test]
    public function find_and_replace_must_skip_matecat_html_tags(): void
    {
        $haystack = "Beauty -> 0 Anti-Akne Gesichtsreiniger Schlankmacher <g id=\"2\">XXX</g>";
        $needle = "0";
        $replacement = "test";

        $expected = "Beauty -> test Anti-Akne Gesichtsreiniger Schlankmacher <g id=\"2\">XXX</g>";
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

        $this->assertEquals($expected, $matches['replacement']);
    }

    #[Test]
    public function find_and_replace_inner_html_content(): void
    {
        $haystack = "Ciao questa è una stringa con <a href=\"#\">contenuto</a> dentro un tag HTML.";
        $needle = 'contenuto';
        $replacement = "test";

        $expected = "Ciao questa è una stringa con <a href=\"#\">test</a> dentro un tag HTML.";
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

        $this->assertEquals($expected, $matches['replacement']);
    }

    #[Test]
    public function find_and_replace_with_hashtag(): void
    {
        $haystack = "Ciao questa è una stringa con ## <a href=\"#\">contenuto</a> dentro un tag HTML.";
        $needle = '##';
        $replacement = "test";

        $expected = "Ciao questa è una stringa con test <a href=\"#\">contenuto</a> dentro un tag HTML.";
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

        $this->assertEquals($expected, $matches['replacement']);
    }

    #[Test]
    public function find_and_replace_with_ampersand(): void
    {
        $haystack  = "Storage &amp; Handling:";

        $needle = "&";
        $replacement = "e";

        $expected = "Storage e Handling:";
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

        $this->assertEquals($expected, $matches['replacement']);
    }

    #[Test]
    public function find_and_replace_with_ampersand_and_case_sensitive(): void
    {
        $haystack  = "handling, Storage &amp; <p id='x' data-content='xxxx'>Handling</p>:";

        $needle = "Handling";
        $replacement = "XX";

        $expected = "handling, Storage & <p id='x' data-content='xxxx'>XX</p>:";
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement, true, false ,true);

        $this->assertEquals($expected, $matches['replacement']);
    }

    #[Test]
    public function find_and_replace_a_slash(): void
    {
        $expected = 'ciao';
        $matches = WholeTextFinder::findAndReplace( "/ ", "/ ", "ciao");

        $this->assertEquals($expected, $matches['replacement']);
    }

    /*
     * =========================================================
     * TESTS WITH ESCAPED HTML
     * =========================================================
     */

    #[Test]
    public function find_and_replace_with_hashtag_with_escaped_html(): void
    {
        $haystack = "Ciao questa è una stringa con ## &lt;a href=\"#\"&gt;contenuto&lt;/a&gt; dentro un tag HTML.";
        $needle = '##';
        $replacement = "test";

        $expected = "Ciao questa è una stringa con test &lt;a href=\"#\"&gt;contenuto&lt;/a&gt; dentro un tag HTML.";
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

        $this->assertEquals($expected, $matches['replacement']);
    }

    #[Test]
    public function find_and_replace_with_ampersand_and_case_sensitive_with_escaped_html(): void
    {
        $haystack  = "handling, Storage &amp; &lt;p id='x' data-content='xxxx'&gt;Handling&lt;/p&gt;:";

        $needle = "Handling";
        $replacement = "XX";

        $expected = "handling, Storage & &lt;p id='x' data-content='xxxx'&gt;XX&lt;/p&gt;:";
        $matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement, true, false ,true);

        $this->assertEquals($expected, $matches['replacement']);
    }
}

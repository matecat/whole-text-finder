# WholeTextFinder

[![license](https://img.shields.io/github/license/matecat/whole-text-finder.svg)]()
[![Packagist](https://img.shields.io/packagist/v/matecat/whole-text-finder.svg)]()
[![Build Status](https://app.travis-ci.com/matecat/whole-text-finder.svg?token=qBazxkHwP18h3EWnHjjF&branch=main)](https://app.travis-ci.com/matecat/whole-text-finder)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=matecat_whole-text-finder&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=matecat_whole-text-finder)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=matecat_whole-text-finder&metric=coverage)](https://sonarcloud.io/summary/new_code?id=matecat_whole-text-finder)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=matecat_whole-text-finder&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=matecat_whole-text-finder)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=matecat_whole-text-finder&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=matecat_whole-text-finder)

**WholeTextFinder** is a simple whole text finder.

## Basic Usage

Use the `find` method. Here is a basic search:

```php
//..
use Finder\WholeTextFinder;

$haystack  = "PHP PHP is the #1 web scripting PHP language of choice.";
$needle = "php";

// 3 matches
$matches = WholeTextFinder::find($haystack, $needle);

// $matches is equals to:
//
// array(3) {
//    [0] =>
//  array(2) {
//            [0] =>
//    string(3) "PHP"
//            [1] =>
//    int(0)
//  }
//  [1] =>
//  array(2) {
//            [0] =>
//    string(3) "PHP"
//            [1] =>
//    int(4)
//  }
//  [2] =>
//  array(2) {
//            [0] =>
//    string(3) "PHP"
//            [1] =>
//    int(32)
//  }
// }

```

### Multi bytes strings

Please note that `WholeTextFinder::find` function is multi byte safe and returns the correct word positions in the original phrase. Take a look here:

```php
//..
use Finder\WholeTextFinder;

$haystack  = "La casa è bella bella";
$needle = "bella";

$matches = WholeTextFinder::find($haystack, $needle, true, true, true);

// $matches is equals to:
// array (
//    0 =>
//        array (
//            0 => 'bella',
//            1 => 10,
//        ),
//    1 =>
//        array (
//            0 => 'bella',
//            1 => 16,
//        ),
//)

```

## Find and Replace

There is also available a `findAndReplace` method:

```php
//..
use Finder\WholeTextFinder;

$haystack = 'Δύο παράγοντες καθόρισαν την αντίληψή μου για την Τενεσί Ουίλιαμς και τη σκηνική παρουσίαση των κειμένων: η Maria Britneva και η Annette Saddik, αφετέρου.';
$needle = 'και';
$replacement = 'test';

$matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

// $matches is equals to:
//
// array(2) {
//   ["replacement"]=>
//   string(252) "Δύο παράγοντες καθόρισαν την αντίληψή μου για την Τενεσί Ουίλιαμς test τη σκηνική παρουσίαση των κειμένων: η Maria Britneva test η Annette Saddik, αφετέρου."
//   ["occurrences"]=>
//   array(2) {
//     [0]=>
//     array(2) {
//       [0]=>
//       string(6) "και"
//       [1]=>
//       int(66)
//     }
//     [1]=>
//     array(2) {
//       [0]=>
//       string(6) "και"
//       [1]=>
//       int(123)
//     }
//   }
// } 
//

```

This method will automatically **exclude** from replace HTML and some Matecat special tags, but allows to replace the inner content inside HTML tags.

So, for example:

```php
//..
use Finder\WholeTextFinder;

// Example 1
$haystack = "Beauty -> 2 Anti-Akne Gesichtsreiniger Schlankmacher <g id=\"2\">XXX</g>";
$needle = 2;
$replacement = "test";

$matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

// $matches is equals to:
//
// array(2) {
//   ["replacement"]=>
//   string(252) "Beauty -> test Anti-Akne Gesichtsreiniger Schlankmacher <g id="2">XXX</g>"
//   ["occurrences"]=>
//   array(1) {
//    [0]=>
//      array(2) {
//        [0]=>
//        string(1) "2"
//        [1]=>
//        int(10)
//     }
//   }
// } 
//

// Example 2
$haystack = "Beauty -> 2 Anti-Akne Gesichtsreiniger Schlankmacher <g id=\"2\">XXX</g>";
$needle = 'XXX';
$replacement = "test";

$matches = WholeTextFinder::findAndReplace($haystack, $needle, $replacement);

// $matches is equals to:
//
// array(2) {
//   ["replacement"]=>
//   string(252) "Beauty -> 2 Anti-Akne Gesichtsreiniger Schlankmacher <g id="2">test</g>"
//   ["occurrences"]=>
//   array(1) {
//    [0]=>
//      array(2) {
//        [0]=>
//        string(1) "test"
//        [1]=>
//        int(55)
//     }
//   }
// } 
//

```

## Search options

Some options are avaliable:

You can also specify four options:

* **$skipHtmlEntities** (`true` by default)  
* **$exactMatch** (`false` by default) 
* **$caseSensitive** (`false` by default) 
* **$preserveNbsps** (`false` by default) 

Here are some examples:

```php
//..
use Finder\WholeTextFinder;

$haystack  = "PHP PHP is the #1 web scripting PHP language of choice.";

// 0 matches
$needle = "php";
$matches = WholeTextFinder::find($haystack, $needle, true, true, true);
   
// 1 match 
$needle = "#1";
$matches = WholeTextFinder::find($haystack, $needle, true, true, true);

// 1 match, even if the haystack contains an invisible nbsp and the needle has an ordinary spacer
$haystackWithNbsp  = "Lawful basis for processing including basis of legitimate interest";
$needleWithoutNbsp = "Lawful basis for processing including basis of legitimate interest";
$matches = WholeTextFinder::find($haystackWithNbsp, $needleWithoutNbsp, true, true, true);
   
```

## Support

If you found an issue or had an idea please refer [to this section](https://github.com/mauretto78/whole-text-finder/issues).

## Authors

* **Mauro Cassani** - [github](https://github.com/mauretto78)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

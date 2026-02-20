<?php

declare(strict_types=1);

namespace Matecat\Finder\Tests;

use Faker\Factory;
use Matecat\Finder\WholeTextFinder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WholeTextFinderWithFakerTest extends Base
{
    #[Test]
    public function testWithEmoji(): void
    {
        $this->markTestSkippedInCoverage();

        $faker = Factory::create();

        for ($i=0;$i<1000;$i++) {
            $emoji = $faker->name. ' ' .$faker->emoji;
            $matches = WholeTextFinder::find($emoji, $emoji, true, true, true);

            $this->assertCount(1, $matches);
        }
    }

    #[Test]
    public function testWithPasswords(): void
    {
        $this->markTestSkippedInCoverage();

        $faker = Factory::create();

        for ($i=0;$i<1000;$i++) {
            $password = $faker->password;
            $matches = WholeTextFinder::find($password, $password, true, true, true);

            $this->assertCount(1, $matches, "Password not matching: " . $password);
        }
    }

    #[Test]
    public function testWithAddresses(): void
    {
        $this->markTestSkippedInCoverage();

        $faker = Factory::create();

        for ($i=0;$i<1000;$i++) {
            $address = $faker->address;
            $matches = WholeTextFinder::find($address, $address, true, true, true);

            $this->assertCount(1, $matches);
        }
    }
}

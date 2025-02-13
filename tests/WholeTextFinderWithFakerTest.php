<?php

namespace Matecat\Finder\Tests;

use Faker\Factory;
use Matecat\Finder\WholeTextFinder;
use PHPUnit\Framework\TestCase;

class WholeTextFinderWithFakerTest extends TestCase
{
    /**
     * @test
     */
    public function testWithEmoji()
    {
        $faker = Factory::create();

        for ($i=0;$i<1000;$i++) {
            $emoji = $faker->name. ' ' .$faker->emoji;
            $matches = WholeTextFinder::find($emoji, $emoji, true, true, true);

            $this->assertCount(1, $matches);
        }
    }

    /**
     * @test
     */
    public function testWithPasswords()
    {
        $faker = Factory::create();

        for ($i=0;$i<1000;$i++) {
            $password = $faker->password;
            $matches = WholeTextFinder::find($password, $password, true, true, true);

            $this->assertCount(1, $matches, "Password not matching: " . $password);
        }
    }

    /**
     * @test
     */
    public function testWithAddresses()
    {
        $faker = Factory::create();

        for ($i=0;$i<1000;$i++) {
            $address = $faker->address;
            $matches = WholeTextFinder::find($address, $address, true, true, true);

            $this->assertCount(1, $matches);
        }
    }
}

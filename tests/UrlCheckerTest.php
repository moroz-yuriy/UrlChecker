<?php

use PHPUnit\Framework\TestCase;
use App\UrlChecker;

class UrlCheckerTest extends TestCase
{
    private $checker;

    public function setUp()
    {
        parent::setUp();
        $this->checker = new UrlChecker('https://habr.com/ru/');
    }

    public function testGetAllLinks(): void
    {
        $links    = $this->checker->getAllLinks();
        $rand_key = array_rand($links);

        self::assertIsArray($links);
        self::assertStringStartsWith('http', $links[$rand_key]);
    }

    public function testGetBrokenImages(): void
    {
        $images = $this->checker->getBrokenImages();

        self::assertIsArray($images);
    }
}

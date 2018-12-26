<?php

namespace Tests\Framework\Twig;

use Framework\Twig\TextExtension;
use PHPUnit\Framework\TestCase;

class TextExtensionTest extends TestCase
{
    /**
     * @var TextExtension
     */
    private $textExtension;

    public function setUp()
    {
        $this->textExtension = new TextExtension();
    }

    public function testExcerptWithShortText()
    {
        $text = "Hello";
        $result = $this->textExtension->excerpt($text, 10);
        $this->assertEquals($text, $result);
    }

    public function testExcerptWithLongText()
    {
        $text = "Hello sailors which are sailing";
        $this->assertEquals("Hello...", $this->textExtension->excerpt($text, 7));
        $this->assertEquals("Hello sailors...", $this->textExtension->excerpt($text, 15));
    }
}

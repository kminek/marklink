<?php
declare(strict_types=1);

namespace Tests\Kminek\Marklink;

use PHPUnit\Framework\TestCase;
use Kminek\Marklink\Parser;

/**
 * Class ParserTest
 * @package Tests\Marklink
 */
class ParserTest extends TestCase
{
    protected $parser;

    public function setUp(): void
    {
        $this->parser = new Parser;
    }

    public function testSimpleExample(): void
    {
        $markdown = <<<MARKDOWN
- [Link A](http://a.sample.com) - Link A description
- [Link B](http://b.sample.com) - Link B description with [some link](http://somelink.sample.com)
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'link',
                    'title' => 'Link A',
                    'url' => 'http://a.sample.com',
                    'description' => 'Link A description',
                ],
                [
                    'type' => 'link',
                    'title' => 'Link B',
                    'url' => 'http://b.sample.com',
                    'description' => 'Link B description with <a href="http://somelink.sample.com">some link</a>',
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testAdvancedExample(): void
    {
        $markdown = <<<MARKDOWN
## Category A

Category A description

### Sub-category A

- Sub-sub-category A
    - [Link A](http://a.sample.com) - Link A description
    - [Link B](http://b.sample.com) - Link B description with [some link](http://somelink.sample.com)
        - [Link C](http://c.sample.com) - Link C description
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Category A',
                    'description' => 'Category A description',
                    'children' => [
                        [
                            'type' => 'category',
                            'title' => 'Sub-category A',
                            'children' => [
                                [
                                    'type' => 'category',
                                    'title' => 'Sub-sub-category A',
                                    'children' => [
                                        [
                                            'type' => 'link',
                                            'title' => 'Link A',
                                            'url' => 'http://a.sample.com',
                                            'description' => 'Link A description',
                                        ],
                                        [
                                            'type' => 'link',
                                            'title' => 'Link B',
                                            'url' => 'http://b.sample.com',
                                            'description' => 'Link B description with <a href="http://somelink.sample.com">some link</a>',
                                            'children' => [
                                                [
                                                    'type' => 'link',
                                                    'title' => 'Link C',
                                                    'url' => 'http://c.sample.com',
                                                    'description' => 'Link C description',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }
}

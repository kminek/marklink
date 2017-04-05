<?php
declare(strict_types=1);

namespace Tests\Kminek\Marklink;

use PHPUnit\Framework\TestCase;
use Kminek\Marklink\ParserService;
use Kminek\Marklink\ParserInterface;
use Kminek\Marklink\Exception;

/**
 * Class ParserImplementationTest
 * @package Tests\Marklink
 */
class ParserImplementationTest extends TestCase
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * @return ParserInterface
     */
    protected function createParser(): ParserInterface
    {
        return new ParserService;
    }

    public function setUp(): void
    {
        $this->parser = $this->createParser();
    }

    public function testSingleList(): void
    {
        $markdown = <<<MARKDOWN
- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'link',
                    'title' => 'Link A',
                    'url' => 'http://a.example.com',
                    'description' => 'Link A description',
                ],
                [
                    'type' => 'link',
                    'title' => 'Link B',
                    'url' => 'http://b.example.com',
                    'description' => 'Link B description with [link](http://link.example.com)',
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testMultipleLists(): void
    {
        $markdown = <<<MARKDOWN
- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)

- [Link C](http://c.example.com) - Link C description
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'link',
                    'title' => 'Link A',
                    'url' => 'http://a.example.com',
                    'description' => 'Link A description',
                ],
                [
                    'type' => 'link',
                    'title' => 'Link B',
                    'url' => 'http://b.example.com',
                    'description' => 'Link B description with [link](http://link.example.com)',
                ],
                [
                    'type' => 'link',
                    'title' => 'Link C',
                    'url' => 'http://c.example.com',
                    'description' => 'Link C description',
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testNestedLists(): void
    {
        $markdown = <<<MARKDOWN
## Category A

- [Link A](http://a.example.com) - Link A description
    - [Link A1](http://a1.example.com) - Link A1 description
- [Link B](http://b.example.com) - Link B description
    - [Link B1](http://b1.example.com) - Link B1 description
    - [Link B2](http://b2.example.com) - Link B2 description
- [Link C](http://c.example.com) - Link C description
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Category A',
                    'children' => [
                        [
                            'type' => 'link',
                            'title' => 'Link A',
                            'url' => 'http://a.example.com',
                            'description' => 'Link A description',
                            'children' => [
                                [
                                    'type' => 'link',
                                    'title' => 'Link A1',
                                    'url' => 'http://a1.example.com',
                                    'description' => 'Link A1 description',
                                ],
                            ],
                        ],
                        [
                            'type' => 'link',
                            'title' => 'Link B',
                            'url' => 'http://b.example.com',
                            'description' => 'Link B description',
                            'children' => [
                                [
                                    'type' => 'link',
                                    'title' => 'Link B1',
                                    'url' => 'http://b1.example.com',
                                    'description' => 'Link B1 description',
                                ],
                                [
                                    'type' => 'link',
                                    'title' => 'Link B2',
                                    'url' => 'http://b2.example.com',
                                    'description' => 'Link B2 description',
                                ],
                            ],
                        ],
                        [
                            'type' => 'link',
                            'title' => 'Link C',
                            'url' => 'http://c.example.com',
                            'description' => 'Link C description',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testListCategory(): void
    {
        $markdown = <<<MARKDOWN
- Category A
    - [Link A](http://a.example.com) - Link A description
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Category A',
                    'children' => [
                        [
                            'type' => 'link',
                            'title' => 'Link A',
                            'url' => 'http://a.example.com',
                            'description' => 'Link A description',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testListCategoryWithDescription(): void
    {
        $markdown = <<<MARKDOWN
- Category A - Category A description with [link](http://link.example.com)
    - [Link A](http://a.example.com) - Link A description
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Category A',
                    'description' => 'Category A description with [link](http://link.example.com)',
                    'children' => [
                        [
                            'type' => 'link',
                            'title' => 'Link A',
                            'url' => 'http://a.example.com',
                            'description' => 'Link A description',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);

        $markdown = <<<MARKDOWN
- **Category A** - *Category A description with [link](http://link.example.com)*
    - [Link A](http://a.example.com) - Link A description
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Category A',
                    'description' => 'Category A description with [link](http://link.example.com)',
                    'children' => [
                        [
                            'type' => 'link',
                            'title' => 'Link A',
                            'url' => 'http://a.example.com',
                            'description' => 'Link A description',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testListItemWithSquareBrackets(): void
    {
        $markdown = <<<MARKDOWN
## PHP Magazines

*Fantastic PHP-related magazines.*

* [php[architect]](https://www.phparch.com/magazine/) - A monthly magazine dedicated to PHP.
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'PHP Magazines',
                    'description' => 'Fantastic PHP-related magazines.',
                    'children' => [
                        [
                            'type' => 'link',
                            'title' => 'php[architect]',
                            'url' => 'https://www.phparch.com/magazine/',
                            'description' => 'A monthly magazine dedicated to PHP.',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testMarkers(): void
    {
        $markdown = <<<MARKDOWN
- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)

<!-- marklink:start -->

- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)

<!-- marklink:end -->
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'link',
                    'title' => 'Link A',
                    'url' => 'http://a.example.com',
                    'description' => 'Link A description',
                ],
                [
                    'type' => 'link',
                    'title' => 'Link B',
                    'url' => 'http://b.example.com',
                    'description' => 'Link B description with [link](http://link.example.com)',
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testMultipleMarkers(): void
    {
        $markdown = <<<MARKDOWN
- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)

<!-- marklink:start -->

- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)

<!-- marklink:end -->

- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)

<!-- marklink:start -->

- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)

<!-- marklink:end -->
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'link',
                    'title' => 'Link A',
                    'url' => 'http://a.example.com',
                    'description' => 'Link A description',
                ],
                [
                    'type' => 'link',
                    'title' => 'Link B',
                    'url' => 'http://b.example.com',
                    'description' => 'Link B description with [link](http://link.example.com)',
                ],
                [
                    'type' => 'link',
                    'title' => 'Link A',
                    'url' => 'http://a.example.com',
                    'description' => 'Link A description',
                ],
                [
                    'type' => 'link',
                    'title' => 'Link B',
                    'url' => 'http://b.example.com',
                    'description' => 'Link B description with [link](http://link.example.com)',
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testHeading(): void
    {
        $markdown = <<<MARKDOWN
## Category A

- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Category A',
                    'children' => [
                        [
                            'type' => 'link',
                            'title' => 'Link A',
                            'url' => 'http://a.example.com',
                            'description' => 'Link A description',
                        ],
                        [
                            'type' => 'link',
                            'title' => 'Link B',
                            'url' => 'http://b.example.com',
                            'description' => 'Link B description with [link](http://link.example.com)',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testSingleHeadingWithDescription(): void
    {
        $markdown = <<<MARKDOWN
## Category A
*Category A description with [link](http://link.example.com)*

- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Category A',
                    'description' => 'Category A description with [link](http://link.example.com)',
                    'children' => [
                        [
                            'type' => 'link',
                            'title' => 'Link A',
                            'url' => 'http://a.example.com',
                            'description' => 'Link A description',
                        ],
                        [
                            'type' => 'link',
                            'title' => 'Link B',
                            'url' => 'http://b.example.com',
                            'description' => 'Link B description with [link](http://link.example.com)',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testHeadingsHierarchy(): void
    {
        $markdown = <<<MARKDOWN
# Heading 1

## Heading 2

# Heading 1
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Heading 1',
                    'children' => [
                        [
                            'type' => 'category',
                            'title' => 'Heading 2',
                        ]
                    ],
                ],
                [
                    'type' => 'category',
                    'title' => 'Heading 1',
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testSteppedHeadingsHierarchy(): void
    {
        $markdown = <<<MARKDOWN
# Heading 1

### Heading 3

# Heading 1

### Heading 3
MARKDOWN;
        $result = $this->parser->parse($markdown);
        $expected = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Heading 1',
                    'children' => [
                        [
                            'type' => 'category',
                            'title' => 'Heading 3',
                        ],
                    ],
                ],
                [
                    'type' => 'category',
                    'title' => 'Heading 1',
                    'children' => [
                        [
                            'type' => 'category',
                            'title' => 'Heading 3',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testInvalidHeadingsHierarchy(): void
    {
        $this->expectException(Exception::class);
        $markdown = <<<MARKDOWN
## Heading 2

# Heading 1
MARKDOWN;
        $result = $this->parser->parse($markdown);
    }

    public function testMixedChildren(): void
    {
        $this->expectException(Exception::class);
        $markdown = <<<MARKDOWN
- Category A
- [Link A](http://a.example.com) - Link A description
MARKDOWN;
        $result = $this->parser->parse($markdown);
    }

    public function testComplexHeadingsAndLists(): void
    {
        $markdown = <<<MARKDOWN
## Category A

Category A description

### Sub-category A

- Sub-sub-category A
    - [Link A](http://a.example.com) - Link A description
    - [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
        - [Link C](http://c.example.com) - Link C description

## Category B

Category B description

### Sub-category B

- Sub-sub-category B
    - [Link A](http://a.example.com) - Link A description
    - [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
        - [Link C](http://c.example.com) - Link C description
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
                                            'url' => 'http://a.example.com',
                                            'description' => 'Link A description',
                                        ],
                                        [
                                            'type' => 'link',
                                            'title' => 'Link B',
                                            'url' => 'http://b.example.com',
                                            'description' => 'Link B description with [link](http://link.example.com)',
                                            'children' => [
                                                [
                                                    'type' => 'link',
                                                    'title' => 'Link C',
                                                    'url' => 'http://c.example.com',
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
                [
                    'type' => 'category',
                    'title' => 'Category B',
                    'description' => 'Category B description',
                    'children' => [
                        [
                            'type' => 'category',
                            'title' => 'Sub-category B',
                            'children' => [
                                [
                                    'type' => 'category',
                                    'title' => 'Sub-sub-category B',
                                    'children' => [
                                        [
                                            'type' => 'link',
                                            'title' => 'Link A',
                                            'url' => 'http://a.example.com',
                                            'description' => 'Link A description',
                                        ],
                                        [
                                            'type' => 'link',
                                            'title' => 'Link B',
                                            'url' => 'http://b.example.com',
                                            'description' => 'Link B description with [link](http://link.example.com)',
                                            'children' => [
                                                [
                                                    'type' => 'link',
                                                    'title' => 'Link C',
                                                    'url' => 'http://c.example.com',
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

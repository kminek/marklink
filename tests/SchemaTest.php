<?php
declare(strict_types=1);

namespace Tests\Kminek\Marklink;

use PHPUnit\Framework\TestCase;
use JsonSchema\Validator;

/**
 * Class SchemaTest
 * @package Tests\Marklink
 */
class SchemaTest extends TestCase
{
    protected function validate(array $data): bool
    {
        $data = json_decode(json_encode($data));
        $schema = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'marklink.schema.json'));
        $validator = new Validator;
        $validator->validate($data, $schema);
        $isValid = $validator->isValid();
        if (!$isValid) {
            // var_dump($validator->getErrors());
        }
        return $isValid;
    }

    public function testValidJSON()
    {
        // simple example
        $data = [
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
        $this->assertEquals(true, $this->validate($data));

        // advanced example
        $data = [
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
        $this->assertEquals(true, $this->validate($data));
    }

    public function testInvalidJSON()
    {
        // invalid root node
        $data = [
            'type' => 'link',
        ];
        $this->assertEquals(false, $this->validate($data));

        // invalid link node
        $data = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'link',
                    'title' => 'Link A',
                    'description' => 'Link A description',
                ],
            ],
        ];
        $this->assertEquals(false, $this->validate($data));

        // invalid non-root category node
        $data = [
            'type' => 'category',
            'children' => [
                [
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
                ],
            ],
        ];
        $this->assertEquals(false, $this->validate($data));

        // mixed node types as children
        $data = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'category',
                    'title' => 'Some category',
                    'children' => [
                        [
                            'type' => 'category',
                            'title' => 'Other category'
                        ],
                        [
                            'type' => 'link',
                            'title' => 'Link B',
                            'url' => 'http://b.sample.com',
                            'description' => 'Link B description with <a href="http://somelink.sample.com">some link</a>',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals(false, $this->validate($data));

        // category node in link node
        $data = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'link',
                    'title' => 'Link B',
                    'url' => 'http://b.sample.com',
                    'description' => 'Link B description with <a href="http://somelink.sample.com">some link</a>',
                    'children' => [
                        'type' => 'category',
                        'title' => 'Some category',
                    ],
                ],
            ],
        ];
        $this->assertEquals(false, $this->validate($data));
    }
}

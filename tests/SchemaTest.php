<?php

declare(strict_types=1);

/*
 * This file is part of the `kminek/marklink` codebase.
 */

namespace Tests\Kminek\Marklink;

use Exception;
use JsonException;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaTest.
 */
class SchemaTest extends TestCase
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws JsonException
     */
    protected function validate(array $data): bool
    {
        $data = json_decode(json_encode($data, JSON_THROW_ON_ERROR));
        $file = file_get_contents(dirname(__DIR__).DIRECTORY_SEPARATOR.'marklink.schema.json');
        if (false === $file) {
            throw new Exception('Unable to read file');
        }
        $schema = json_decode($file, false, 512, JSON_THROW_ON_ERROR);
        $validator = new Validator();
        $validator->validate($data, $schema);
        $isValid = $validator->isValid();
        if (!$isValid) {
            // var_dump($validator->getErrors());
        }

        return $isValid;
    }

    public function testValidJSON(): void
    {
        // simple example
        $data = [
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
        $this->assertEquals(true, $this->validate($data));
    }

    public function testInvalidJSON(): void
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
                            'title' => 'Other category',
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
        $this->assertEquals(false, $this->validate($data));

        // category node inside link node
        $data = [
            'type' => 'category',
            'children' => [
                [
                    'type' => 'link',
                    'title' => 'Link B',
                    'url' => 'http://b.example.com',
                    'description' => 'Link B description with [link](http://link.example.com)',
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

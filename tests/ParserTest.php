<?php

declare(strict_types=1);

/*
 * This file is part of the `kminek/marklink` codebase.
 */

namespace Tests\Kminek\Marklink;

use Kminek\Marklink\ParserService;
use Kminek\Marklink\ParserTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest.
 */
class ParserTest extends TestCase
{
    use ParserTestTrait;

    public function setUp(): void
    {
        $this->parser = new ParserService();
    }
}

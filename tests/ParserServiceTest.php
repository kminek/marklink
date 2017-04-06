<?php
declare(strict_types=1);

namespace Tests\Kminek\Marklink;

use Kminek\Marklink\AbstractParserImplementationTest;
use Kminek\Marklink\ParserInterface;
use Kminek\Marklink\ParserService;

/**
 * Class ParserServiceTest
 * @package Tests\Marklink
 */
class ParserServiceTest extends AbstractParserImplementationTest
{
    /**
     * {@inheritdoc}
     */
    public function createParser(): ParserInterface
    {
        return new ParserService;
    }
}

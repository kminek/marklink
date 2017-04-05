<?php
declare(strict_types=1);

namespace Kminek\Marklink;

/**
 * Interface ParserInterface
 * @package Kminek\Marklink
 */
interface ParserInterface
{
    /**
     * Parse Markdown string into Marklink array
     *
     * @param string $markdown
     * @return array
     */
    public function parse(string $markdown): array;
}

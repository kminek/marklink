<?php

declare(strict_types=1);

/*
 * This file is part of the `kminek/marklink` codebase.
 */

namespace Kminek\Marklink;

/**
 * Interface ParserInterface.
 */
interface ParserInterface
{
    /**
     * Parse Markdown string into Marklink array.
     *
     * @return array<string, mixed>
     */
    public function parse(string $markdown): array;
}

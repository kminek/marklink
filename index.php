<?php

declare(strict_types=1);

/*
 * This file is part of the `kminek/marklink` codebase.
 */

use Kminek\Marklink\ParserService;
use GuzzleHttp\Client;

require 'vendor/autoload.php';

$markdown = <<<MARKDOWN
- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
MARKDOWN;

$parser = new ParserService();
dump($parser->parse($markdown));

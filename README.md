![Marklink logo](https://cdn.rawgit.com/kminek/marklink/master/media/marklink.logo.svg)

Marklink
========

A simple standard allowing embedding (and parsing) categorized lists of links
inside [Markdown](https://en.wikipedia.org/wiki/Markdown) files.

Marklink was born as an attempt to standardize various
[awesome lists of links](https://github.com/sindresorhus/awesome) available on
GitHub.

Schema
------

Every Markdown document with embedded well-formed Marklink sections can be
parsed by Marklink parser into tree-like [JSON](https://en.wikipedia.org/wiki/JSON)
structure with categories and links. This JSON data structure is described by
schema file (see [marklink.schema.json](https://raw.githubusercontent.com/kminek/marklink/master/marklink.schema.json) for reference). Schema
file allows JSON structure to be validated (see [JSON Schema](http://json-schema.org/)
for reference).

### Schema rules

1. there are two types of nodes:
    - `category`
    - `link`
2. there are four types of node fields (apart from `type` field which
   determines node type):
    - `title`
    - `url`
    - `description`
    - `children`
3. there is only one root `category` node
4. `category` nodes below root node REQUIRE valid `title` and may optionally
   contain other fields
5. `category` node CAN have child `category` nodes OR `link` nodes - but not
   both mixed at the same time
6. `link` node REQUIRE valid `title` and valid `url`, CAN have child `link` nodes and
   CANNOT have child `category` nodes

Examples
--------

Here are some examples how Markdown fragments are parsed by Marklink parser into
JSON data (see [AbstractParserImplementationTest.php](https://raw.githubusercontent.com/kminek/marklink/master/src/AbstractParserImplementationTest.php) for more).

### Basic example

Input:

```markdown
- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
```

Output:

```json
{
    "type": "category",
    "children": [
        {
            "type": "link",
            "title": "Link A",
            "url": "http://a.example.com",
            "description": "Link A description"
        },
        {
            "type": "link",
            "title": "Link B",
            "url": "http://b.example.com",
            "description": "Link B description with [link](http://link.example.com)"
        }
    ]
}
```

### Advanced example

Input:

```markdown
## Category A

Category A description

### Sub-category A

- Sub-sub-category A
    - [Link A](http://a.example.com) - Link A description
    - [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
        - [Link C](http://c.example.com) - Link C description
```

Output:

```json
{
    "type": "category",
    "children": [
        {
            "type": "category",
            "title": "Category A",
            "description": "Category A description",
            "children": [
                {
                    "type": "category",
                    "title": "Sub-category A",
                    "children": [
                        {
                            "type": "category",
                            "title": "Sub-sub-category A",
                            "children": [
                                {
                                    "type": "link",
                                    "title": "Link A",
                                    "url": "http://a.example.com",
                                    "description": "Link A description"
                                },
                                {
                                    "type": "link",
                                    "title": "Link B",
                                    "url": "http://b.example.com",
                                    "description": "Link B description with [link](http://link.example.com)",
                                    "children": [
                                        {
                                            "type": "link",
                                            "title": "Link C",
                                            "url": "http://c.example.com",
                                            "description": "Link C description",
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ]
}
```

### Input with markers

By default Marklink parser will parse whole document unless it finds
following markers:

```markdown
<!-- marklink:start -->
- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
<!-- marklink:end -->
```

In that case only content between markers will be parsed.

## Online Marklink parsing service

Initial Markling parser implementation is available as a service.

### Web interface

[http://awesomelist.kminek.pl/marklink](http://awesomelist.kminek.pl/marklink)

### cURL example

```bash
curl --request POST \
  --url http://awesomelist.kminek.pl/api/marklink/parse \
  --header 'cache-control: no-cache' \
  --header 'content-type: application/json' \
  --data '{"markdown": "- [Link A](http://a.example.com) - Link A description\n- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)"}'
```

### PHP example

```bash
composer require kminek/marklink
```

```php
use Kminek\Marklink\ParserService;

$markdown = <<<MARKDOWN
- [Link A](http://a.example.com) - Link A description
- [Link B](http://b.example.com) - Link B description with [link](http://link.example.com)
MARKDOWN;

$parser = new ParserService;
$result = $parser->parse($markdown);
```

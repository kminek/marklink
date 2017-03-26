![Marklink logo](https://cdn.rawgit.com/kminek/marklink/master/media/marklink.logo.svg)

# Marklink

A simple standard to store categorized lists of links in Markdown/CommonMark
files.

## Schema

Every well-formed Marklink document can be parsed by Marklink parser into
tree-like JSON structure with categories and links (see `marklink.schema.json`
for schema reference).

Schema rules:

- there are two types of nodes: `category` and `link`
- there are four types of node fields: `title`, `url`, `description`, `children`
- there is only one root `category` node
- `category` nodes below root node MUST contain valid `title` and optionally
  other fields
- `category` node CAN have child `category` nodes OR `link` nodes (not both
  at the same time)
- `link` node MUST contain valid `title` and `url`, CAN have child `link` nodes and CANNOT have child
  `category` nodes

## Basic example

Input:

```markdown
- [Link A](http://a.sample.com) - Link A description
- [Link B](http://b.sample.com) - Link B description with [some link](http://somelink.sample.com)
```

Output:

```json
{
    "type": "category",
    "children": [
        {
            "type": "link",
            "title": "Link A",
            "url": "http://a.sample.com",
            "description": "Link A description"
        },
        {
            "type": "link",
            "title": "Link B",
            "url": "http://b.sample.com",
            "description": "Link B description with <a href='http://somelink.sample.com'>some link</a>"
        }
    ]
}
```

# Advanced example

Input:

```markdown
## Category A

Category A description

### Sub-category A

- Sub-sub-category A
    - [Link A](http://a.sample.com) - Link A description
    - [Link B](http://b.sample.com) - Link B description with [some link](http://somelink.sample.com)
        - [Link C](http://c.sample.com) - Link C description
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
                                    "url": "http://a.sample.com",
                                    "description": "Link A description"
                                },
                                {
                                    "type": "link",
                                    "title": "Link B",
                                    "url": "http://b.sample.com",
                                    "description": "Link B description with <a href='http://somelink.sample.com'>some link</a>",
                                    "children": [
                                        {
                                            "type": "link",
                                            "title": "Link C",
                                            "url": "http://c.sample.com",
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

## Todo

- implement parser
- implement online validator

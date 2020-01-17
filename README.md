# JsonSchemaFormBundle

Creates a Symfony form from a [JSON schema](https://json-schema.org).

## Installation:

With [Composer](http://packagist.org):
```sh
composer require cyve/json-schema-form-bundle
```

## Usage

```php
use Cyve\JsonSchemaFormBundle\Form\Type\SchemaType;
use Cyve\JsonSchemaFormBundle\Validator\Constraint\Schema;

$json = <<<JSON
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "$id": "http://example.com/product.schema.json",
  "title": "Product",
  "type": "object",
  "properties": {
    "id": {
      "type": "integer"
    },
    "name": {
      "type": "string"
    },
    "source": {
        "type": "*",
        "oneOf": [
            {
                "title": "Git",
                "description": "g"
            },
            {
                "title": "SVN",
                "description": "s"
            }
        ]
    }
  },
  "required": ["id", "name"]
}
JSON;
$schema = json_decode($json);
$subject = new \StdClass();
$form = $container->get('form.factory')->create(SchemaType::class, $subject, ['data_schema' => $schema, 'constraints' => [new Schema($schema)]]);
```
The form option `data_schema` MUST be an `object` representing a JSON schema.

## Documentation
### Form generation

| JSON schema property | Symfony FormType | Form options |
|------------------|------------------|---|
| `type: "*"` and `enum: [*]` or `oneOf: [*]` | `ChoiceType` | `choices` is set with the content of `enum` or `oneOf` |
| `type: "array"` | `CollectionType` | `allow_add`, `allow_delete` and `delete_empty` are set to `true`.  `entry_type` and `entry_options` are resolved from the `items` sub-schema |
| `type: "object"` | `SchemaType` | `data_schema` is set with the object sub-schema |
| `type: "integer"` | `IntegerType` | |
| `type: "number"` | `NumberType` | |
| `type: "boolean"` | `CheckboxType` | |
| `type: "string"` and `format: "date-time"` | `DateTimeType` | `input_format` is set to `"c"` ([ISO 8601](https://en.wikipedia.org/wiki/ISO_8601)) |
| `type: "string"` and `format: "date"` | `DateType` | `input_format` is set to `"Y-m-d"` |
| `type: "string"` and `format: "time"` | `TimeType` | `input_format` is set to `"H:i:s"` |
| `type: "string"` and `format: "email"` | `EmailType` | |
| `type: "string"` and `format: "uri"` | `UrlType` | |
| `type: "null"` | `null` | |

The form option `label` is set with JSON property `title` if defined  
The form option `help` is set with JSON property `description` if defined  
The form option `empty_data` is set with JSON property `default` if defined  

### Validation

To validate the form subject against the JSON schema, add the form option `'constraints' => [new Cyve\JsonSchemaFormBundle\Validator\Constraint\Schema($schema)]` to the root form. The validator uses `propertyPath` to display the violation messages on the proper fields.  
The JSON schema validation is made using [justinrainbow/json-schema](http://packagist.org/packages/justinrainbow/json-schema).  
See the [JSON schema specification](https://json-schema.org/draft/2019-09/json-schema-core.html)  

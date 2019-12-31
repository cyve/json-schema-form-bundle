# JsonSchemaFormBundle

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
  },
  "required": ["id", "name"]
}
JSON;
$schema = json_decode($json);
$subject = new \StdClass();
$form = $container->get('form.factory')->create(SchemaType::class, $subject, ['data_schema' => $schema, 'constraints' => [new Schema($schema)]]);
```

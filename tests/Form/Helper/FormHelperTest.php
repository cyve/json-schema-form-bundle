<?php

namespace Cyve\JsonSchemaFormBundle\Tests\Form\Helper;

use Cyve\JsonSchemaFormBundle\Form\Helper\FormHelper;
use Cyve\JsonSchemaFormBundle\Form\Type\SchemaType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class FormHelperTest extends TestCase
{
    /**
     * @dataProvider resolveFormTypeDataProvider
     */
    public function testResolveFormType($schema, $expected)
    {
        $this->assertEquals($expected, FormHelper::resolveFormType($schema));
    }

    public function resolveFormTypeDataProvider()
    {
        yield [(object) ['type' => 'array'], CollectionType::class];
        yield [(object) ['type' => 'object'], SchemaType::class];
        yield [(object) ['type' => 'integer'], IntegerType::class];
        yield [(object) ['type' => 'number'], NumberType::class];
        yield [(object) ['type' => 'boolean'], CheckboxType::class];
        yield [(object) ['type' => 'string', 'enum' => []], ChoiceType::class];
        yield [(object) ['type' => 'string', 'format' => 'date-time'], DateTimeType::class];
        yield [(object) ['type' => 'string', 'format' => 'date'], DateType::class];
        yield [(object) ['type' => 'string', 'format' => 'time'], TimeType::class];
        yield [(object) ['type' => 'string', 'format' => 'uri'], UrlType::class];
        yield [(object) ['type' => 'string', 'format' => 'email'], EmailType::class];
        yield [(object) ['type' => 'string', 'format' => 'foo'], TextType::class];
        yield [(object) ['type' => 'string'], TextType::class];
        yield [(object) ['type' => 'null'], null];
    }

    /**
     * @dataProvider resolveFormOptionsDataProvider
     */
    public function testResolveFormOptions($schema, $expected)
    {
        $this->assertEquals($expected, FormHelper::resolveFormOptions($schema));
    }

    public function resolveFormOptionsDataProvider()
    {
        yield [(object) ['type' => 'array', 'items' => (object) ['type' => 'string', 'default' => 'foo']], ['allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'entry_type' => TextType::class, 'entry_options' => ['empty_data' => 'foo']]];
        yield [(object) ['type' => 'object', 'title' => 'Lorem ipsum'], ['label' => 'Lorem ipsum', 'data_schema' => (object) ['type' => 'object', 'title' => 'Lorem ipsum']]];
        yield [(object) ['type' => 'string', 'title' => 'Lorem ipsum'], ['label' => 'Lorem ipsum']];
        yield [(object) ['type' => 'string', 'description' => 'Lorem ipsum'], ['help' => 'Lorem ipsum']];
        yield [(object) ['type' => 'string', 'default' => 'foo'], ['empty_data' => 'foo']];
        yield [(object) ['type' => 'string', 'enum' => ['lorem', 'ipsum']], ['choices' => ['lorem' => 'lorem', 'ipsum' => 'ipsum']]];
        yield [(object) ['type' => 'string', 'format' => 'date-time'], ['input' => 'string', 'input_format' => 'c']];
        yield [(object) ['type' => 'string', 'format' => 'date'], ['input' => 'string', 'input_format' => 'Y-m-d']];
        yield [(object) ['type' => 'string', 'format' => 'time'], ['input' => 'string', 'input_format' => 'H:i:s']];
    }
}

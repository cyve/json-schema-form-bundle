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
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class FormHelperTest
 * @package Cyve\JsonSchemaFormBundle\Tests\Form\Helper
 * @coversDefaultClass \Cyve\JsonSchemaFormBundle\Form\Helper\FormHelper
 */
class FormHelperTest extends TestCase
{
    /**
     * @covers ::resolveFormType
     * @dataProvider resolveFormTypeDataProvider
     * @param mixed $property
     * @param mixed $expected
     */
    public function testResolveFormType($property, $expected)
    {
        if (is_a($expected, \LogicException::class, true)) {
            $this->expectException($expected);
        }
        if (is_a($expected, \TypeError::class, true)) {
            $this->expectException($expected);
        }
        $formType = FormHelper::resolveFormType($property);
        $this->assertEquals($expected, $formType);
    }

    public function resolveFormTypeDataProvider()
    {
        yield [(object) ['type' => 'array'], CollectionType::class];
        yield [(object) ['type' => 'object'], SchemaType::class];
        yield [(object) ['type' => 'integer'], IntegerType::class];
        yield [(object) ['type' => 'number'], NumberType::class];
        yield [(object)['type' => 'number', 'minimum' => 0, 'maximum' => 100,], RangeType::class];
        // DRAFT-07 Version Number Range
        yield [(object) ['type' => 'number', 'exclusiveMinimum' => 0, 'maximum' => 100,], RangeType::class];
        yield [(object) ['type' => 'number', 'minimum' => 0, 'exclusiveMaximum' => 100,], RangeType::class];
        yield [(object) ['type' => 'number', 'exclusiveMinimum' => 0,'exclusiveMaximum' => 100,], RangeType::class];
        // DRAFT-04 Version Number Range
        yield [(object) ['type' => 'number', 'minimum' => 0, 'exclusiveMinimum' => true, 'maximum' => 100,], RangeType::class];
        yield [(object) ['type' => 'number', 'minimum' => 0, 'maximum' => 100, 'exclusiveMaximum' => true,], RangeType::class];
        yield [(object) ['type' => 'number', 'minimum' => 0, 'exclusiveMinimum' => true, 'maximum' => 100,'exclusiveMaximum' => false,], RangeType::class];
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
        yield [(object) ['type' => ['null', 'string']], \LogicException::class];
        yield [['type' => 'string'], \TypeError::class];
    }

    /**
     * @covers ::resolveFormOptions
     * @dataProvider resolveFormOptionsDataProvider
     *
     * @param mixed $property
     * @param mixed $expected
     */
    public function testResolveFormOptions($property, $expected)
    {
        if (is_a($expected, \LogicException::class, true)) {
            $this->expectException($expected);
        }
        if (is_a($expected, \TypeError::class, true)) {
            $this->expectException($expected);
        }
        $this->assertEquals($expected, FormHelper::resolveFormOptions($property));
    }

    public function resolveFormOptionsDataProvider()
    {
        yield [
            (object)[
                'type' => 'array',
                'items' => (object)[
                    'type' => 'object',
                    'title' => 'My nested Schema 1',
                    'description' => 'Yet another nested Schema 1',
                    'properties' => (object)[
                        'foo' => (object)[
                            'title' => 'String in Nested Schema',
                            'description' => 'Test the nested schema', 'default' => 'the default',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            [
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'entry_type' => SchemaType::class,
                'entry_options' => [
                    'label' => 'My nested Schema 1',
                    'help' => 'Yet another nested Schema 1',
                    'data_schema' => (object)[
                        'type' => 'object', 'title' => 'My nested Schema 1',
                        'description' => 'Yet another nested Schema 1',
                        'properties' => (object)[
                            'foo' => (object)[
                                'title' => 'String in Nested Schema',
                                'description' => 'Test the nested schema',
                                'default' => 'the default', 'type' => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        yield [(object) ['type' => 'array', 'items' => (object) ['type' => 'string', 'default' => 'foo']], ['allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'entry_type' => TextType::class, 'entry_options' => ['empty_data' => 'foo']]];
        yield [(object) ['type' => 'object', 'title' => 'Lorem ipsum'], ['label' => 'Lorem ipsum', 'data_schema' => (object) ['type' => 'object', 'title' => 'Lorem ipsum']]];
        yield [(object) ['type' => 'number', 'minimum' => 0, 'maximum' => 100,],['attr' => ['min' => 0, 'max' => 100,],],];
        // DRAFT-07 Number Range
        yield [(object) ['type' => 'number', 'exclusiveMinimum' => 0, 'maximum' => 100,],['attr' => ['min' => 0,'max' => 100,],],];
        yield [(object) ['type' => 'number', 'minimum' => 0, 'exclusiveMaximum' => 100,],['attr' => ['min' => 0,'max' => 100,],],];
        yield [(object) ['type' => 'number', 'exclusiveMinimum' => 0, 'exclusiveMaximum' => 100,],['attr' => ['min' => 0,'max' => 100,],],];
        // DRAFT-04 Number Range
        yield [(object) ['type' => 'number', 'minimum' => 0, 'exclusiveMinimum' => true, 'maximum' => 100,],['attr' => ['min' => 0,'max' => 100,],],];
        yield [(object) ['type' => 'number', 'minimum' => 0, 'maximum' => 100, 'exclusiveMaximum' => true,],['attr' => ['min' => 0,'max' => 100,],],];
        yield [(object) ['type' => 'number', 'minimum' => 0, 'exclusiveMinimum' => true, 'maximum' => 100,'exclusiveMaximum' => false,],['attr' => ['min' => 0,'max' => 100,],],];
        yield [(object) ['type' => 'string', 'title' => 'Lorem ipsum'], ['label' => 'Lorem ipsum']];
        yield [(object) ['type' => 'string', 'description' => 'Lorem ipsum'], ['help' => 'Lorem ipsum']];
        yield [(object) ['type' => 'string', 'default' => 'foo'], ['empty_data' => 'foo']];
        yield [(object) ['type' => 'string', 'enum' => ['lorem', 'ipsum']], ['choices' => ['lorem' => 'lorem', 'ipsum' => 'ipsum']]];
        yield [(object) ['type' => 'string', 'format' => 'date-time'], ['input' => 'string', 'input_format' => 'c']];
        yield [(object) ['type' => 'string', 'format' => 'date'], ['input' => 'string', 'input_format' => 'Y-m-d']];
        yield [(object) ['type' => 'string', 'format' => 'time'], ['input' => 'string', 'input_format' => 'H:i:s']];
        yield [(object) ['type' => ['string', null], 'default' => 'foo'], \LogicException::class];
        yield [['type' => ['string', null], 'default' => 'foo'], \TypeError::class];
    }
}

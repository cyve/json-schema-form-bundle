<?php

namespace Cyve\JsonSchemaFormBundle\Form\Helper;

use Cyve\JsonSchemaFormBundle\Form\Type\SchemaType;
use stdClass;
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

class FormHelper
{
    /**
     * @param stdClass $property
     * @return string|null
     */
    public static function resolveFormType(stdClass $property): ?string
    {
        if (is_array($property->type)) {
            throw new \LogicException('Multiple types support is not implemeted yet');
        }

        if (isset($property->enum)) {
            return ChoiceType::class;
        }

        switch ($property->type) {
            case 'array':
                return CollectionType::class;
            case 'object':
                return SchemaType::class;
            case 'integer':
                return IntegerType::class;
            case 'number':
                if (static::isRangeType($property)) {
                    return RangeType::class;
                }
                return NumberType::class;
            case 'boolean':
                return CheckboxType::class;
            case 'string':
                switch ($property->format ?? null) {
                    case 'date-time':
                        return DateTimeType::class;
                    case 'date':
                        return DateType::class;
                    case 'time':
                        return TimeType::class;
                    case 'email':
                    case 'idn-email':
                        return EmailType::class;
                    case 'uri':
                    case 'uri-reference':
                    case 'iri':
                    case 'iri-reference':
                        return UrlType::class;
                    default:
                        return TextType::class;
                }
            default:
                return null;
        }
    }

    /**
     * @param stdClass $property
     * @return array
     */
    public static function resolveFormOptions(stdClass $property): array
    {
        if (is_array($property->type)) {
            throw new \LogicException('Multiple types support is not implemeted yet');
        }

        $options = [];

        if (isset($property->title)) {
            $options['label'] = $property->title;
        }

        if (isset($property->description)) {
            $options['help'] = $property->description;
        }

        if (isset($property->default)) {
            $options['empty_data'] = $property->default;
        }

        if (isset($property->enum)) {
            return $options + ['choices' => array_combine($property->enum, $property->enum)];
        }

        switch ($property->type) {
            case 'array':
                return $options + [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'entry_type' => self::resolveFormType($property->items),
                    'entry_options' => self::resolveFormOptions($property->items),
                ];
            case 'object':
                return $options + ['data_schema' => $property];
            case 'string':
                switch ($property->format ?? null) {
                    case 'date-time':
                        return $options + ['input' => 'string', 'input_format' => 'c'];
                    case 'date':
                        return $options + ['input' => 'string', 'input_format' => 'Y-m-d'];
                    case 'time':
                        return $options + ['input' => 'string', 'input_format' => 'H:i:s'];
                    default:
                        return $options;
                }
            case 'number':
                switch (true) {
                    case static::isRangeType($property):
                        return $options + [
                                'attr' => [
                                    'min' => $property->minimum ?? $property->exclusiveMinimum,
                                    'max' => $property->maximum ?? $property->exclusiveMaximum,
                                ],
                            ];
                    default:
                        return $options;
                }
            default:
                return $options;
        }
    }

    /**
     * Check if the Property is from type number and has the (exclusive)minimum and (exclusive)maximum attribute
     * @param stdClass $property
     * @return bool
     */
    public static function isRangeType(stdClass $property): bool
    {
        return 'number' === $property->type &&
            (property_exists($property, 'minimum')
                || property_exists($property, 'exclusiveMinimum')
            ) && (
                property_exists($property, 'maximum')
                || property_exists($property, 'exclusiveMaximum')
            );
    }

}

<?php

namespace Cyve\JsonSchemaFormBundle\Form\Helper;

use Cyve\JsonSchemaFormBundle\Form\Type\SchemaType;
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

class FormHelper
{
    /**
     * @param object $schema
     * @return string|null
     */
    public static function resolveFormType($schema): ?string
    {
        if (is_array($schema->type)) {
            throw new \LogicException('Multiple types support is not implemeted yet');
        }

        if (isset($schema->enum) || isset($schema->oneOf)) {
            return ChoiceType::class;
        }

        switch ($schema->type) {
            case 'array':
                return CollectionType::class;
            case 'object':
                return SchemaType::class;
            case 'integer':
                return IntegerType::class;
            case 'number':
                return NumberType::class;
            case 'boolean':
                return CheckboxType::class;
            case 'string':
                switch ($schema->format ?? null) {
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
     * @param object $schema
     * @return array
     */
    public static function resolveFormOptions($schema): array
    {
        if (is_array($schema->type)) {
            throw new \LogicException('Multiple types support is not implemeted yet');
        }

        $options = [];

        if (isset($schema->title)) {
            $options['label'] = $schema->title;
        }

        if (isset($schema->description)) {
            $options['help'] = $schema->description;
        }

        if (isset($schema->default)) {
            $options['empty_data'] = $schema->default;
        }

        if (isset($schema->oneOf)) {
            $tab = [];
            foreach ($schema->oneOf as $value) {
                $tab[$value->title] = $value->description;
                if (isset($value->default) && $value->default) {
                    $options['data'] = $value->description;
                }
            }

            return $options + ['choices' => $tab];
        }

        if (isset($schema->enum)) {
            return $options + ['choices' => array_combine($schema->enum, $schema->enum)];
        }

        switch ($schema->type) {
            case 'array':
                return $options + [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'entry_type' => self::resolveFormType($schema->items),
                    'entry_options' => self::resolveFormOptions($schema->items),
                ];
            case 'object':
                return $options + ['data_schema' => $schema];
            case 'string':
                switch ($schema->format ?? null) {
                    case 'date-time':
                        return $options + ['input' => 'string', 'input_format' => 'c'];
                    case 'date':
                        return $options + ['input' => 'string', 'input_format' => 'Y-m-d'];
                    case 'time':
                        return $options + ['input' => 'string', 'input_format' => 'H:i:s'];
                    default:
                        return $options;
                }
            default:
                return $options;
        }
    }
}

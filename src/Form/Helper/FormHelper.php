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

        if (isset($schema->enum)) {
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
                return TextType::class;
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

        $options = [
            'label' => $schema->title ?? null,
            'help' => $schema->description ?? null,
            'empty_data' => (string) ($schema->default ?? null),
        ];

        if (isset($schema->enum)) {
            return $options + ['choices' => \array_combine($schema->enum, $schema->enum)];
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
                return $options + ['schema' => $schema];
            default:
                return $options;
        }
    }
}

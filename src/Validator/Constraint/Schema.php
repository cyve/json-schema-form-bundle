<?php

namespace Cyve\JsonSchemaFormBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class Schema extends Constraint
{
    const SCHEMA_VALIDATION_FAILED_ERROR = 'ed7e5b18-1663-4440-9a52-6f08bd45725a';

    protected static $errorNames = [
        self::SCHEMA_VALIDATION_FAILED_ERROR => 'SCHEMA_VALIDATION_FAILED_ERROR',
    ];

    public $schema;

    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return [Constraint::CLASS_CONSTRAINT, Constraint::PROPERTY_CONSTRAINT];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'schema';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['schema'];
    }
}

<?php

namespace Cyve\JsonSchemaFormBundle\Validator\Constraint;

use JsonSchema\Constraints\Constraint as JsonSchemaConstraint;
use JsonSchema\Validator as JsonSchemaValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SchemaValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Schema) {
            throw new UnexpectedTypeException($constraint, Schema::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $validator = new JsonSchemaValidator();
        $validator->validate($value, $constraint->schema, JsonSchemaConstraint::CHECK_MODE_APPLY_DEFAULTS);

        foreach ($validator->getErrors() as $error) {
            $this->context->buildViolation($error['message'])
                ->atPath('['.str_replace('.', '][', $error['property']).']') // converts "foo.bar" to "[foo][bar]"
                ->setCode(Schema::SCHEMA_VALIDATION_FAILED_ERROR)
                ->addViolation();
        }
    }
}

<?php

namespace Cyve\JsonSchemaFormBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ObjectToArrayTransformer implements DataTransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transform($data)
    {
        if (!is_object($data)) {
            throw new TransformationFailedException(sprintf('Exprected object, %s given.', gettype($data)));
        }

        return (array) $data;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($data)
    {
        if (!is_array($data)) {
            throw new TransformationFailedException(sprintf('Exprected array, %s given.', gettype($data)));
        }

        return (object) $data;
    }
}

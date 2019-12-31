<?php

namespace Cyve\JsonSchemaFormBundle\Form\Type;

use Cyve\JsonSchemaFormBundle\Form\Helper\FormHelper;
use Cyve\JsonSchemaFormBundle\Form\Transformer\ObjectToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchemaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rootSchema = $options['data_schema'];
        foreach ($rootSchema->properties as $name => $schema) {
            if (!$formType = FormHelper::resolveFormType($schema)) {
                continue;
            }

            $formOptions = FormHelper::resolveFormOptions($schema) + ['required' => in_array($name, $rootSchema->required ?? [])];

            $builder->add($name, $formType, $formOptions);
        }

        if (!$options['data_class']) {
            $builder->addModelTransformer(new ObjectToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('data_schema')
            ->setAllowedTypes('data_schema', 'object')
            ->setDefault('data_class', null)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'form';
    }
}

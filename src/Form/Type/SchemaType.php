<?php

namespace Cyve\JsonSchemaFormBundle\Form\Type;

use Cyve\JsonSchemaFormBundle\Form\Helper\FormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchemaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rootSchema = $options['schema'];
        foreach ($rootSchema->properties as $name => $schema) {
            if (!$formType = FormHelper::resolveFormType($schema)) {
                continue;
            }

            $formOptions = FormHelper::resolveFormOptions($schema) + ['required' => in_array($name, $rootSchema->required ?? [])];

            $builder->add($name, $formType, $formOptions);

            if (!$options['data_class']) {
                $builder->addModelTransformer(new CallbackTransformer(
                    function ($value) {
                        return json_decode(json_encode($value), true);
                    },
                    function ($data) {
                        return json_decode(json_encode($data));
                    }
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['schema'])
            ->setAllowedTypes('schema', 'object')
            ->setDefaults(['data_class' => null])
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

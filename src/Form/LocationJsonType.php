<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataTransformerInterface;

class LocationJsonType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lat', TextType::class, [
                'label' => 'Latitude',
            ])
            ->add('lng', TextType::class, [
                'label' => 'Longitude',
            ])
            ->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Configure any other options you need
    }

    public function transform($value)
    {
        if ($value === null || is_object($value) || is_array($value)) {
            return $value;
        }
    
        return json_encode([
            'lat' => $value['lat'] ?? null,
            'lng' => $value['lng'] ?? null,
        ]);
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }
    
        if (is_string($value)) {
            return json_decode($value, true);
        }
    
        return $value;
    }
}
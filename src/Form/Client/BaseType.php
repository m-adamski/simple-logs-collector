<?php

namespace App\Form\Client;

use App\Entity\Client;
use App\Form\AdvancedChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add("name", TextType::class, [
                "label"    => "Name",
                "required" => true,
            ])
            ->add("secretToken", TextType::class, [
                "label"    => "Secret Token",
                "required" => true,
            ])
            ->add("active", AdvancedChoiceType::class, [
                "label"    => "Status",
                "choices"  => ["Active" => true, "Disabled" => false],
                "required" => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            "data_class"         => Client::class,
            "translation_domain" => "client",
        ]);
    }
}

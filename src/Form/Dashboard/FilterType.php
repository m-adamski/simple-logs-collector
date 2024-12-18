<?php

namespace App\Form\Dashboard;

use App\Entity\Client;
use App\Entity\Filter;
use App\Model\Event\Level;
use App\Model\Filter\QuickRange;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add("client", EntityType::class, [
                "label"        => "Client",
                "class"        => Client::class,
                "choice_label" => "name",
                "placeholder"  => "*",
                "required"     => false,
            ])
            ->add("measurement", ChoiceType::class, [
                "label"       => "Measurement",
                "choices"     => $options["measurements"],
                "placeholder" => "*",
                "required"    => false,
            ])
            ->add("level", ChoiceType::class, [
                "label"       => "Level",
                "choices"     => array_flip(Level::array()),
                "placeholder" => "*",
                "required"    => false,
            ])
            ->add("startDate", DateTimeType::class, [
                "label"    => "Date range",
                "required" => false,
            ])
            ->add("endDate", DateTimeType::class, [
                "label"    => false,
                "required" => false,
            ])
            ->add("quickRange", EnumType::class, [
                "label"        => false,
                "class"        => QuickRange::class,
                "choice_label" => function (QuickRange $quickRange) {
                    return $quickRange->toString();
                },
                "placeholder"  => "Custom",
                "required"     => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setRequired("measurements")
            ->setDefaults([
                "data_class"         => Filter::class,
                "translation_domain" => "dashboard",
            ])
            ->setAllowedTypes("measurements", "array");
    }
}

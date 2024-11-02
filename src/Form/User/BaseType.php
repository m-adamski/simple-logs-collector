<?php

namespace App\Form\User;

use App\Entity\User;
use App\Form\AdvancedChoiceType;
use App\Model\User\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add("name", TextType::class, [
                "label"    => "Name",
                "attr"     => ["placeholder" => "John Doe"],
                "required" => true,
            ])
            ->add("emailAddress", EmailType::class, [
                "label"    => "Email address",
                "attr"     => ["placeholder" => "john.doe@example.com"],
                "required" => true,
            ])
            ->add("roles", AdvancedChoiceType::class, [
                "label"    => "Roles",
                "choices"  => Role::getChoices(),
                "multiple" => true,
                "required" => false,
            ])
            ->add("active", AdvancedChoiceType::class, [
                "label"    => "Status",
                "choices"  => ["Active" => true, "Disabled" => false],
                "required" => true,
            ])
            ->add("plainPassword", RepeatedType::class, [
                "type"           => PasswordType::class,
                "first_options"  => ["label" => "Password", "attr" => ["placeholder" => "Password"], "required" => $options["mode"] === "create"],
                "second_options" => ["label" => false, "attr" => ["placeholder" => "Repeat password"], "required" => $options["mode"] === "create"],
                "required"       => $options["mode"] === "create",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setRequired("mode");
        $resolver->setDefaults([
            "data_class"         => User::class,
            "translation_domain" => "user"
        ]);
    }
}

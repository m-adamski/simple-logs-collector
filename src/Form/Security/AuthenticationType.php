<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthenticationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $lastUsername = $options["last_username"];

        $builder
            ->add("_username", EmailType::class, [
                "label"    => "Email address",
                "data"     => $lastUsername,
                "attr"     => ["class" => "form-control form-control-lg"],
                "required" => true
            ])
            ->add("_password", PasswordType::class, [
                "label"    => "Password",
                "attr"     => ["class" => "form-control form-control-lg", "autocomplete" => "off"],
                "required" => true
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setRequired("last_username");

        // Enable CSRF protection in login form
        // https://symfony.com/doc/current/security.html#csrf-protection-in-login-forms
        $resolver->setDefaults([
            "csrf_token_id"      => "authenticate",
            "csrf_field_name"    => "_csrf_token",
            "translation_domain" => "security",
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string {
        // In this case, it needs to be blank string because firewall expects _username & _password fields
        // https://symfony.com/doc/current/security.html#form-login
        return "";
    }
}

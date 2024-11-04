<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedChoiceType extends ChoiceType {

    // Preline Advanced Select
    // https://preline.co/docs/advanced-select.html
    //
    // Disadvantages:
    // - Cannot use other attributes defined in the Form
    // - Native browser validation is not working (select field is hidden)
    //
    // Use it only for not required select fields or required but without multiple option.
    public function getBlockPrefix(): string {
        return "advanced_choice";
    }
}

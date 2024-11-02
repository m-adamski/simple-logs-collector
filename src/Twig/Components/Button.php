<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button {
    // This Class is not required until we don't need any logic - that's because we can use an Anonymous Components
    //
    // Sometimes a component is simple enough that it doesn't need a PHP class. In this case, you can skip the class
    // and only create the template. The component name is determined by the location of the template
    //
    // https://symfony.com/bundles/ux-twig-component/current/index.html#anonymous-components
}

<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder {
    public function __construct(
        private readonly FactoryInterface $factory
    ) {
    }

    public function createMainMenu(RequestStack $requestStack): ItemInterface {
        $menu = $this->factory->createItem("root");

        $menu->addChild("Dashboard", ["route" => "dashboard", "label" => "Dashboard"]);
        $menu->addChild("Users", ["route" => "user", "label" => "Users"]);
        $menu->addChild("Clients", ["route" => "client", "label" => "Clients"]);

        return $menu;
    }
}

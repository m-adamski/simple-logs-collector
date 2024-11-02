<?php

namespace App\Model\User;

enum Role: string {
    case ROLE_ADMINISTRATOR = "ROLE_ADMINISTRATOR";

    public function toString(): string {
        return match ($this) {
            self::ROLE_ADMINISTRATOR => "Administrator",
        };
    }

    public static function getChoices(?array $roles = null): array {
        $roles = $roles ?? self::cases();

        $choices = [];
        foreach ($roles as $role) {
            $choices[$role->toString()] = $role->name;
        }

        return $choices;
    }
}

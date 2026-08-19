<?php

namespace App\Enum;

enum BoardMemberComite: string
{
    case PRATIQUE = 'PRATIQUE';
    case INTERNATIONAL = 'INTERNATIONAL';
    case ETHIQUE = 'ETHIQUE';
    case PARENTALITE = 'PARENTALITE';
    case SCIENTIFIQUE = 'SCIENTIFIQUE';

    public function getLabel(): string
    {
        return match ($this) {
            self::PRATIQUE => 'Comité Pratique',
            self::INTERNATIONAL => 'Comité International',
            self::ETHIQUE => 'Comité Ethique',
            self::PARENTALITE => 'Comité Parentalité',
            self::SCIENTIFIQUE => 'Comité Scientifique',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }

        return $choices;
    }
}

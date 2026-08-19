<?php

namespace App\Enum;

enum BoardMemberDon: string
{
    case EMBRYONS = 'EMBRYONS';
    case SPERMATOZOIDES = 'SPERMATOZOIDES';
    case OVOCYTES = 'OVOCYTES';
    case MITOCHONDRIES = 'MITOCHONDRIES';
    case GESTATION = 'GESTATION';

    public function getLabel(): string
    {
        return match ($this) {
            self::EMBRYONS => 'Don d\'embryons',
            self::SPERMATOZOIDES => 'Don de spermatozoïdes',
            self::OVOCYTES => 'Don d\'ovocytes',
            self::MITOCHONDRIES => 'Don de mitochondries',
            self::GESTATION => 'Don de gestation',
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

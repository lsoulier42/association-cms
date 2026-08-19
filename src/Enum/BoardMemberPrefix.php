<?php

namespace App\Enum;

enum BoardMemberPrefix: string
{
    case DR = 'DR';
    case DRE = 'DRE';
    case PR = 'PR';
    case PRE = 'PRE';
    case M = 'M';
    case MME = 'MME';

    public function getLabel(): string
    {
        return match ($this) {
            self::DR => 'Dr.',
            self::DRE => 'Dre.',
            self::PR => 'Pr.',
            self::PRE => 'Pre.',
            self::M => 'M.',
            self::MME => 'Mme',
        };
    }

    /**
     * @return array<string, self>
     */
    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->getLabel()] = $case;
        }

        return $choices;
    }
}

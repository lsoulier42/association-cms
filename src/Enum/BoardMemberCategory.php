<?php

namespace App\Enum;

enum BoardMemberCategory: string
{
    case BUREAU_RESTREINT = 'BUREAU_RESTREINT';
    case VICE_PRESIDENTS = 'VICE_PRESIDENTS';
    case ADMINISTRATEUR = 'ADMINISTRATEUR';
    case CONSEILLER_SPECIAL = 'CONSEILLER_SPECIAL';

    public function getLabel(): string
    {
        return match ($this) {
            self::BUREAU_RESTREINT => 'Bureau restreint',
            self::VICE_PRESIDENTS => 'Vice-présidents',
            self::ADMINISTRATEUR => 'Administrateurs',
            self::CONSEILLER_SPECIAL => 'Conseillers spéciaux',
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

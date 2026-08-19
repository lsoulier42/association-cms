<?php

namespace App\Enum;

enum BoardMemberTitle: string
{
    // Bureau restreint
    case PRESIDENT = 'PRESIDENT';
    case VICE_PRESIDENT_GENERAL = 'VICE_PRESIDENT_GENERAL';
    case SECRETAIRE = 'SECRETAIRE';
    case TRESORIER = 'TRESORIER';

    // Autres
    case VICE_PRESIDENT = 'VICE_PRESIDENT';

    // Conseillers spéciaux & Comité (shared sometimes)
    case ETHIQUE = 'ETHIQUE';
    case JURIDIQUE = 'JURIDIQUE';
    case GENETIQUE = 'GENETIQUE';
    case SOCIETE = 'SOCIETE';
    case PSYCHOLOGIE = 'PSYCHOLOGIE';
    case COMMUNICATION = 'COMMUNICATION';

    public function getLabel(): string
    {
        return match ($this) {
            self::PRESIDENT => 'Président.e',
            self::VICE_PRESIDENT_GENERAL => 'Vice-président.e.s général.e',
            self::VICE_PRESIDENT => 'Vice-président.e.s',
            self::SECRETAIRE => 'Secrétaire général.e',
            self::TRESORIER => 'Trésorier.e',

            self::ETHIQUE => 'Ethique',
            self::JURIDIQUE => 'Question juridique',
            self::GENETIQUE => 'Génétique',
            self::SOCIETE => 'Société',
            self::PSYCHOLOGIE => 'Psychologie',
            self::COMMUNICATION => 'Communication et Réseaux sociaux',
        };
    }

    /**
     * @return array<string, self[]>
     */
    public static function getValidTitlesPerCategory(): array
    {
        return [
            BoardMemberCategory::BUREAU_RESTREINT->value => [
                self::PRESIDENT,
                self::VICE_PRESIDENT_GENERAL,
                self::SECRETAIRE,
                self::TRESORIER,
            ],
            BoardMemberCategory::VICE_PRESIDENTS->value => [],
            BoardMemberCategory::ADMINISTRATEUR->value => [],
            BoardMemberCategory::CONSEILLER_SPECIAL->value => [
                self::ETHIQUE,
                self::JURIDIQUE,
                self::GENETIQUE,
                self::SOCIETE,
                self::PSYCHOLOGIE,
                self::COMMUNICATION,
            ],
        ];
    }

    /**
     * @return array<string, array<string, self>>
     */
    public static function getGroupedChoices(): array
    {
        $mapping = self::getValidTitlesPerCategory();
        $grouped = [];

        foreach ($mapping as $categoryValue => $titles) {
            $category = BoardMemberCategory::from($categoryValue);
            if (empty($titles)) {
                continue;
            }

            $groupLabel = $category->getLabel();
            $grouped[$groupLabel] = [];
            foreach ($titles as $title) {
                $grouped[$groupLabel][$title->getLabel()] = $title;
            }
        }

        return $grouped;
    }
}

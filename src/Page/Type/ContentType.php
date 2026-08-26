<?php

namespace App\Page\Type;

use App\Page\AbstractPageType;

/**
 * Fallback type: a page rendering only its stored content.
 */
class ContentType extends AbstractPageType
{
    public function getIdentifier(): string
    {
        return 'content';
    }

    public function getLabel(): string
    {
        return 'Contenu simple';
    }
}

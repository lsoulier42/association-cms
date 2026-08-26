<?php

namespace App\Page;

use App\Entity\SpecialPage;

abstract class AbstractPageType implements PageTypeInterface
{
    public function getTemplate(): string
    {
        return 'special_page/show.html.twig';
    }

    public function getData(SpecialPage $page): array
    {
        return [];
    }
}

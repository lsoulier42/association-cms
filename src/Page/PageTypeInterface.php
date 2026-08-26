<?php

namespace App\Page;

use App\Entity\SpecialPage;

/**
 * A page type plugs business logic into a SpecialPage.
 *
 * Implement this interface and tag the service with `app.page_type`
 * (automatic via services.yaml) to expose a new kind of page whose
 * behaviour lives in code rather than in stored content.
 */
interface PageTypeInterface
{
    /**
     * Unique identifier persisted on SpecialPage::$identifier.
     */
    public function getIdentifier(): string;

    /**
     * Human readable label, shown in the admin interface.
     */
    public function getLabel(): string;

    /**
     * Template used to render pages of this type.
     */
    public function getTemplate(): string;

    /**
     * Extra template variables for pages of this type.
     *
     * @return array<string, mixed>
     */
    public function getData(SpecialPage $page): array;
}

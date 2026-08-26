<?php

namespace App\Page;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Holds every service tagged with `app.page_type`, indexed by identifier.
 */
class PageTypeRegistry
{
    /**
     * @var array<string, PageTypeInterface>
     */
    private array $types = [];

    /**
     * @param iterable<PageTypeInterface> $pageTypes
     */
    public function __construct(
        #[AutowireIterator(tag: 'app.page_type')]
        iterable $pageTypes,
    ) {
        foreach ($pageTypes as $pageType) {
            $this->types[$pageType->getIdentifier()] = $pageType;
        }
    }

    public function has(string $identifier): bool
    {
        return isset($this->types[$identifier]);
    }

    public function get(string $identifier): ?PageTypeInterface
    {
        return $this->types[$identifier] ?? null;
    }

    /**
     * All registered types indexed by identifier.
     *
     * @return array<string, PageTypeInterface>
     */
    public function all(): array
    {
        return $this->types;
    }

    /**
     * identifier => label map for admin forms.
     *
     * @return array<string, string>
     */
    public function getChoices(): array
    {
        $choices = [];
        foreach ($this->types as $type) {
            $choices[$type->getLabel()] = $type->getIdentifier();
        }

        return $choices;
    }
}

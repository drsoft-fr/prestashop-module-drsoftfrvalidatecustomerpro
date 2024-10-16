<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;

/**
 * Class ValidateCustomerProChoiceProvider
 *
 * This class provides a choice list of groups.
 */
final class ValidateCustomerProChoiceProvider
{
    /**
     * @var GroupDataProvider
     */
    private $groupDataProvider;

    /**
     * @var int
     */
    private $language;

    /**
     * @var int
     */
    private $shop;

    public function __construct(
        GroupDataProvider $groupDataProvider,
        int               $language,
        int               $shop
    )
    {
        $this->groupDataProvider = $groupDataProvider;
        $this->language = $language;
        $this->shop = $shop;
    }

    /**
     * Get groups array for choice list
     *
     * @return array
     */
    public function getGroupChoices(): array
    {
        $choices = [];

        foreach ($this->getGroups() as $group) {
            if (
                empty($group['id_group']) ||
                empty($group['name'])
            ) {
                continue;
            }

            $choices[$group['name']] = $group['id_group'];
        }

        return $choices;
    }

    /**
     * Returns an array of groups.
     *
     * @return array The array of groups.
     */
    private function getGroups(): array
    {
        return $this
            ->groupDataProvider
            ->getGroups(
                $this->language,
                $this->shop
            );
    }
}

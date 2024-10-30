<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;

/**
 * Class SettingChoiceProvider
 */
final class SettingChoiceProvider
{
    /**
     * @var array
     */
    private $formFields;

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
        array             $formFields,
        GroupDataProvider $groupDataProvider,
        int               $language,
        int               $shop
    )
    {
        $this->formFields = $formFields;
        $this->groupDataProvider = $groupDataProvider;
        $this->language = $language;
        $this->shop = $shop;
    }

    /**
     * Returns an array of choices for additional form fields.
     *
     * Iterates through the additional form fields and creates choices only if domain, name, and label are not empty.
     *
     * @return array The array of choices for additional form fields.
     */
    public function getFormFieldChoices(): array
    {
        $choices = [];

        foreach ($this->formFields as $field) {
            if (
                empty($field['domain']) ||
                empty($field['label']) ||
                empty($field['name'])
            ) {
                continue;
            }

            $choices[$field['label'] . ' (' . $field['domain'] . ')'] = $field['name'];
        }

        return $choices;
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

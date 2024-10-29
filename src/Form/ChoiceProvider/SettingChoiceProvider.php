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
    private $additionalFormFields;

    /**
     * @var GroupDataProvider
     */
    private $groupDataProvider;

    /**
     * @var int
     */
    private $language;

    /**
     * @var array
     */
    private $requiredFormFields;

    /**
     * @var int
     */
    private $shop;

    public function __construct(
        array             $additionalFormFields,
        GroupDataProvider $groupDataProvider,
        int               $language,
        array             $requiredFormFields,
        int               $shop
    )
    {
        $this->additionalFormFields = $additionalFormFields;
        $this->groupDataProvider = $groupDataProvider;
        $this->language = $language;
        $this->requiredFormFields = $requiredFormFields;
        $this->shop = $shop;
    }

    /**
     * Returns an array of choices for additional form fields.
     *
     * Iterates through the additional form fields and creates choices only if domain, name, and label are not empty.
     *
     * @return array The array of choices for additional form fields.
     */
    public function getAdditionalFormFieldChoices(): array
    {
        $choices = [];

        foreach ($this->additionalFormFields as $field) {
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

    /**
     * Returns an array of choices for required form fields.
     *
     * Iterates through the required form fields and creates choices only if domain, name, and label are not empty.
     *
     * @return array The array of choices for required form fields.
     */
    public function getRequiredFormFieldChoices(): array
    {
        $choices = [];

        foreach ($this->requiredFormFields as $field) {
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
}

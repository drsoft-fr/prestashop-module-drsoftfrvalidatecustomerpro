<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Data\Provider;

use Symfony\Contracts\Translation\TranslatorInterface;

final class RequiredFormFieldsProvider
{
    /**
     * @var array
     */
    private $additionalFormFields;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param array $additionalFormFields
     * @param TranslatorInterface $translator
     */
    public function __construct(
        array               $additionalFormFields,
        TranslatorInterface $translator
    )
    {
        $this->additionalFormFields = $additionalFormFields;
        $this->translator = $translator;
    }

    /**
     * Retrieve an array of data with domains, fields, and labels.
     *
     * @return array
     */
    public function getData(): array
    {
        return array_merge(
            $this->additionalFormFields,
            [
                [
                    'domain' => 'customer',
                    'field' => 'company',
                    'label' => $this->translator->trans('Company', [], 'Shop.Forms.Labels'),
                    'name' => 'customer__company'
                ],
                [
                    'domain' => 'customer',
                    'field' => 'siret',
                    'label' => $this->translator->trans('Siret', [], 'Modules.Drsoftfrvalidatecustomerpro.Shop'),
                    'name' => 'customer__siret'
                ],
            ]
        );
    }

    /**
     * Get an array of 'domain' values from the data
     *
     * @return array
     */
    public function getDomains(): array
    {
        $a = [];

        foreach ($this->getData() as $arr) {
            $a[] = $arr['domain'];
        }

        return array_unique($a);
    }

    /**
     * Get an array of 'field' values from the data
     *
     * @return array
     */
    public function getFields(): array
    {
        $a = [];

        foreach ($this->getData() as $arr) {
            $a[] = $arr['field'];
        }

        return array_unique($a);
    }

    /**
     * Get an array of 'label' values from the data
     *
     * @return array
     */
    public function getLabels(): array
    {
        $a = [];

        foreach ($this->getData() as $arr) {
            $a[] = $arr['label'];
        }

        return array_unique($a);
    }

    /**
     * Get an array of 'name' values from the data
     *
     * @return array
     */
    public function getNames(): array
    {
        $a = [];

        foreach ($this->getData() as $arr) {
            $a[] = $arr['name'];
        }

        return array_unique($a);
    }
}

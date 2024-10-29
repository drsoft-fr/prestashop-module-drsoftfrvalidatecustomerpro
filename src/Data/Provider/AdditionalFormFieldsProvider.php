<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Data\Provider;

use Symfony\Contracts\Translation\TranslatorInterface;

final class AdditionalFormFieldsProvider
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    )
    {
        $this->translator = $translator;
    }

    /**
     * Retrieve an array of data with domains, fields, and labels.
     *
     * @return array
     */
    public function getData(): array
    {
        return [
            [
                'domain' => 'customer',
                'field' => 'ape',
                'label' => $this->translator->trans('APE', [], 'Modules.Drsoftfrvalidatecustomerpro.Shop'),
                'name' => 'customer__ape'
            ],
            [
                'domain' => 'customer',
                'field' => 'website',
                'label' => $this->translator->trans('Website', [], 'Modules.Drsoftfrvalidatecustomerpro.Shop'),
                'name' => 'customer__website'
            ],
            [
                'domain' => 'customer',
                'field' => 'note',
                'label' => $this->translator->trans('Note', [], 'Modules.Drsoftfrvalidatecustomerpro.Shop'),
                'name' => 'customer__note'
            ],
            [
                'domain' => 'address',
                'field' => 'alias',
                'label' => $this->translator->trans('Alias', [], 'Shop.Forms.Labels'),
                'name' => 'address__alias'
            ],
            [
                'domain' => 'address',
                'field' => 'company',
                'label' => $this->translator->trans('Company', [], 'Shop.Forms.Labels'),
                'name' => 'address__company'
            ],
            [
                'domain' => 'address',
                'field' => 'address2',
                'label' => $this->translator->trans('Address Complement', [], 'Shop.Forms.Labels'),
                'name' => 'address__address2'
            ],
            [
                'domain' => 'address',
                'field' => 'other',
                'label' => $this->translator->trans('Other', [], 'Shop.Forms.Labels'),
                'name' => 'address__other'
            ],
            [
                'domain' => 'address',
                'field' => 'phone',
                'label' => $this->translator->trans('Phone', [], 'Shop.Forms.Labels'),
                'name' => 'address__phone'
            ],
            [
                'domain' => 'address',
                'field' => 'phone_mobile',
                'label' => $this->translator->trans('Mobile phone', [], 'Shop.Forms.Labels'),
                'name' => 'address__phone_mobile'
            ],
            [
                'domain' => 'address',
                'field' => 'vat_number',
                'label' => $this->translator->trans('VAT number', [], 'Shop.Forms.Labels'),
                'name' => 'address__vat_number'
            ],
            [
                'domain' => 'address',
                'field' => 'dni',
                'label' => $this->translator->trans('Identification number', [], 'Shop.Forms.Labels'),
                'name' => 'address__dni'
            ],
        ];
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

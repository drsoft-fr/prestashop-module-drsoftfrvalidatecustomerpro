<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Form;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;

/**
 * Class ValidateCustomerProType
 *
 * This class represents a form type for managing settings.
 * It extends the TranslatorAwareType class for translation support.
 */
final class ValidateCustomerProType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', SwitchType::class, [
                'empty_data' => false,
                'label' => $this->trans(
                    'Active',
                    'Modules.Drsoftfrvalidatecustomerpro.Admin'
                ),
                'required' => true,
            ]);
    }
}

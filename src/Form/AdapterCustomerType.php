<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Represents a form type for creating and editing adapter_customers.
 *
 * @final
 */
final class AdapterCustomerType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_customer', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Exchange rate', 'Admin.International.Feature')),
                            ]
                        ),
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => $this->trans(
                            'This value should be greater than or equal to %value%',
                            'Admin.Notifications.Error',
                            [
                                '%value%' => 0,
                            ]
                        ),
                    ])
                ],
                'invalid_message' => $this->trans(
                    'This field is invalid, it must contain numeric values',
                    'Admin.Notifications.Error'
                ),
                'label' => $this->trans('Customer ID', 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
                'required' => true,
            ])
            ->add('active', SwitchType::class, [
                'empty_data' => false,
                'label' => $this->trans('Active', 'Modules.Drsoftfrvalidatecustomerpro.Admin')
            ]);
    }
}

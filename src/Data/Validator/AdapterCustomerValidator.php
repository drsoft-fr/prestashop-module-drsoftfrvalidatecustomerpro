<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Data\Validator;

use Exception;
use DrSoftFr\Module\ValidateCustomerPro\Exception\Customer\NonexistentCustomerIdException;
use DrSoftFr\Module\ValidateCustomerPro\Exception\AdapterCustomer\AdapterCustomerConstraintException;
use DrSoftFr\PrestaShopModuleHelper\Data\Validator\AbstractValidator;
use DrSoftFr\PrestaShopModuleHelper\Data\Validator\ValidatorInterface;

final class AdapterCustomerValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * @var array
     */
    private $customerIds;

    /**
     * @param array $customerIds
     */
    public function __construct(
        array $customerIds
    )
    {
        $this->customerIds = $customerIds;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AdapterCustomerConstraintException If any of the data fields fail validation.
     * @throws Exception
     */
    public function validate(array $data): bool
    {
        $this
            ->validateActive($data)
            ->validateIdCustomer($data);

        return true;
    }

    /**
     * Validates the active in the given data array.
     *
     * @param array $data The data array to validate.
     *
     * @return AdapterCustomerValidator
     *
     * @throws Exception If the 'active' field is not set or empty.
     */
    private function validateActive(array $data): AdapterCustomerValidator
    {
        $this->isSet($data, 'active', new AdapterCustomerConstraintException);
        $this->isBool($data, 'active', new AdapterCustomerConstraintException);

        return $this;
    }

    /**
     * Validates the id_customer in the given data array.
     *
     * @param array $data The data array to validate.
     *
     * @return AdapterCustomerValidator
     *
     * @throws Exception If the 'id_customer' field is not a valid ID.
     */
    private function validateIdCustomer(array $data): AdapterCustomerValidator
    {
        if (!isset($data['id_customer'])) {
            return $this;
        }

        $data['id_customer'] = (int)$data['id_customer'];

        if (
            0 >= $data['id_customer'] ||
            !in_array(
                $data['id_customer'],
                $this->customerIds,
                true
            )
        ) {
            throw new NonexistentCustomerIdException(
                sprintf(
                    'Customer width "%d" does not exist.',
                    $data['id_customer']
                )
            );
        }

        return $this;
    }
}

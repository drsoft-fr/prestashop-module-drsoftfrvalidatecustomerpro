<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Data\Validator;

use Exception;
use DrSoftFr\Module\ValidateCustomerPro\Exception\ValidateCustomerPro\ValidateCustomerProConstraintException;
use DrSoftFr\PrestaShopModuleHelper\Data\Validator\AbstractValidator;
use DrSoftFr\PrestaShopModuleHelper\Data\Validator\ValidatorInterface;

final class ValidateCustomerProValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * Validates all the data fields.
     *
     * @param array $data The data array to validate.
     *
     * @return bool Returns true if all the fields pass the validation.
     *
     * @throws ValidateCustomerProConstraintException If any of the data fields fail validation.
     */
    public function validate(array $data): bool
    {
        $this
            ->validateActive($data);

        return true;
    }

    /**
     * Validates the active field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return void
     *
     * @throws ValidateCustomerProConstraintException If the active field is not set.
     * @throws ValidateCustomerProConstraintException If the active field is not a boolean value.
     * @throws Exception
     */
    private function validateActive(array $configuration): void
    {
        $this->isSet($configuration, 'active', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'active', new ValidateCustomerProConstraintException);
    }
}

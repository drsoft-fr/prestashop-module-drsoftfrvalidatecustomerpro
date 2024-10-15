<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Data\Factory;

use DrSoftFr\Module\ValidateCustomerPro\Data\Configuration\ValidateCustomerProConfiguration;

/**
 * Class ValidateCustomerProDataFactory is in charge of accessing the ValidateCustomerPro settings in PrestaShop configuration
 */
final class ValidateCustomerProDataFactory
{
    /**
     * @var ValidateCustomerProConfiguration
     */
    private $configuration;

    /**
     * @param ValidateCustomerProConfiguration $configuration
     */
    public function __construct(
        ValidateCustomerProConfiguration $configuration
    )
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array the form data as an associative array
     */
    public function getData(): array
    {
        return $this->configuration->getConfiguration();
    }
}

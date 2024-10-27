<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Form\DataProvider;

use Exception;
use DrSoftFr\Module\ValidateCustomerPro\Data\Configuration\SettingConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class ValidateCustomerProFormDataProvider is in charge of accessing/saving the ValidateCustomerPro settings in PrestaShop configuration
 */
final class ValidateCustomerProFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var SettingConfiguration
     */
    private $configuration;

    /**
     * @param SettingConfiguration $configuration
     */
    public function __construct(
        SettingConfiguration $configuration
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

    /**
     * Persists form Data in Database and Filesystem.
     *
     * @param array $data
     *
     * @return array $errors if data can't persisted an array of errors messages
     *
     * @throws Exception
     */
    public function setData(array $data): array
    {
        return $this->configuration->updateConfiguration($data);
    }
}

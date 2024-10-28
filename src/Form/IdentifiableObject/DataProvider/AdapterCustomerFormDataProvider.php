<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Form\IdentifiableObject\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use DrSoftFr\Module\ValidateCustomerPro\Entity\AdapterCustomer;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

/**
 * Class AdapterCustomerFormDataProvider
 *
 * This class implements the FormDataProviderInterface to provide data for AdapterCustomer forms.
 */
final class AdapterCustomerFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var AdapterCustomer $obj */
        $obj = $this
            ->entityManager
            ->getRepository(AdapterCustomer::class)
            ->find((int)$id);

        if (null === $obj) {
            return $this->getDefaultData();
        }

        return $obj->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'id' => 0,
            'id_customer' => null,
            'active' => false,
        ];
    }
}

<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use DrSoftFr\Module\ValidateCustomerPro\Config as Configuration;
use DrSoftFr\PrestaShopModuleHelper\Traits\ClassHydrateTrait;

/**
 * @ORM\Table(name=Configuration::ADAPTER_CUSTOMER_TABLE_NAME)
 * @ORM\Entity(repositoryClass="DrSoftFr\Module\ValidateCustomerPro\Repository\AdapterCustomerRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AdapterCustomer
{
    use ClassHydrateTrait;

    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int $idCustomer
     *
     * @ORM\Column(name="id_customer", type="integer", length=10, nullable=false, options={"unsigned"=true})
     */
    private $idCustomer;

    /**
     * @var bool $active
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":0, "unsigned"=true})
     */
    private $active;

    /**
     * @var DateTimeInterface $dateAdd creation date
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var DateTimeInterface $dateUpd last modification date
     *
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"}, nullable=false)
     */
    private $dateUpd;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'id_customer' => $this->getIdCustomer(),
            'active' => $this->isActive(),
            'date_add' => $this->getDateAdd(),
            'date_upd' => $this->getDateUpd(),
        ];
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setDateUpd(new DateTime());

        if ($this->dateAdd === null) {
            $this->setDateAdd(new DateTime());
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AdapterCustomer
     */
    public function setId(int $id): AdapterCustomer
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdCustomer(): int
    {
        return $this->idCustomer;
    }

    /**
     * @param int $idCustomer
     *
     * @return AdapterCustomer
     */
    public function setIdCustomer(int $idCustomer): AdapterCustomer
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return AdapterCustomer
     */
    public function setActive(bool $active): AdapterCustomer
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateAdd(): DateTimeInterface
    {
        return $this->dateAdd;
    }

    /**
     * @param DateTimeInterface $dateAdd
     *
     * @return AdapterCustomer
     */
    public function setDateAdd(DateTimeInterface $dateAdd): AdapterCustomer
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateUpd(): DateTimeInterface
    {
        return $this->dateUpd;
    }

    /**
     * @param DateTimeInterface $dateUpd
     *
     * @return AdapterCustomer
     */
    public function setDateUpd(DateTimeInterface $dateUpd): AdapterCustomer
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }
}

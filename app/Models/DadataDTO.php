<?php

namespace App\Models;

class DadataDTO
{
    private ?string $org_name = null;
    private ?string $managment_name = null;
    private ?string $managment_post = null;
    private ?string $address_value = null;
    private ?string $unrestricted_value = null;

    public function __construct(
        ?string $org_name = null,
        ?string $managment_name = null,
        ?string $managment_post = null,
        ?string $address_value = null,
        ?string $unrestricted_value = null
    )
    {
        $this->$org_name = $this->getOrgName();
        $this->$managment_name = $this->getManagmentName();
        $this->$managment_post = $this->getManagmentPost();
        $this->$address_value = $this->getAddressValue();
        $this->$unrestricted_value = $this->getUnrestrictedValue();
    }

    /**
     * @return string|null
     */
    public function getOrgName(): ?string
    {
        return $this->org_name;
    }

    /**
     * @param string|null $org_name
     */
    public function setOrgName(?string $org_name): void
    {
        $this->org_name = $org_name;
    }

    /**
     * @return string|null
     */
    public function getManagmentName(): ?string
    {
        return $this->managment_name;
    }

    /**
     * @param string|null $managment_name
     */
    public function setManagmentName(?string $managment_name): void
    {
        $this->managment_name = $managment_name;
    }

    /**
     * @return string|null
     */
    public function getManagmentPost(): ?string
    {
        return $this->managment_post;
    }

    /**
     * @param string|null $managment_post
     */
    public function setManagmentPost(?string $managment_post): void
    {
        $this->managment_post = $managment_post;
    }

    /**
     * @return string|null
     */
    public function getAddressValue(): ?string
    {
        return $this->address_value;
    }

    /**
     * @param string|null $address_value
     */
    public function setAddressValue(?string $address_value): void
    {
        $this->address_value = $address_value;
    }

    /**
     * @return string|null
     */
    public function getUnrestrictedValue(): ?string
    {
        return $this->unrestricted_value;
    }

    /**
     * @param string|null $unrestricted_value
     */
    public function setUnrestrictedValue(?string $unrestricted_value): void
    {
        $this->unrestricted_value = $unrestricted_value;
    }

    public static function daResDTO($arr)
    {
        $dto = new self();
        $dto->org_name = $arr['suggestions'][0]['value'];
        $dto->managment_name = $arr['suggestions'][0]['data']['management']['name'];
        $dto->managment_post = $arr['suggestions'][0]['data']['management']['post'];
        $dto->address_value = $arr['suggestions'][0]['data']['address']['value'];
        $dto->unrestricted_value = $arr['suggestions'][0]['data']['address']['unrestricted_value'];
        return $dto;
    }

}

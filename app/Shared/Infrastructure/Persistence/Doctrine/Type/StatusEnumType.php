<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastucture\Enum\StatusEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class StatusEnumType extends Type
{
    const TYPE = 'status_enum';

    /**
     * @param array $column
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(
        array            $column,
        AbstractPlatform $platform
    ): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::TYPE;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return StatusEnum
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): StatusEnum
    {
        return $value === null ? StatusEnum::hidden : StatusEnum::from($value);
    }

    /**
     * @param StatusEnum $value
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform
    ): string
    {
        return $value->value;
    }

}

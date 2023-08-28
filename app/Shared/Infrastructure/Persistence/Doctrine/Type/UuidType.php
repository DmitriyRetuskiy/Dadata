<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastructure\ValueObject\UuidValueObject;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;


class UuidType extends Type
{
    const TYPE = 'uuid';

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
     * @return UuidValueObject|null
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?UuidValueObject
    {
        return $value === null ? null : UuidValueObject::create($value);
    }

    /**
     * @param UuidValueObject $value
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform
    ): string
    {
        return (string)$value;
    }
}

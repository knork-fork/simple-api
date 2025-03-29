<?php
declare(strict_types=1);

namespace App\System\Database;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;
use ReflectionType;
use RuntimeException;

abstract class Entity
{
    public ?int $id = null;
    public ?string $created_at = null;

    private string $tableName;
    private Connection $connection;

    public function __construct(string $tableName = '')
    {
        if ($tableName === '') {
            $tableName = strtolower((new ReflectionClass($this))->getShortName()) . 's';
        }
        $this->tableName = $tableName;

        $this->connection = Connection::getInstance();
    }

    /**
     * @param mixed[] $data
     */
    public function hydrate(array $data): self
    {
        foreach ($this->getPublicProperties() as $property) {
            $name = $property->getName();

            if (\array_key_exists($name, $data)) {
                $value = $this->castToPropertyType($data[$name], $property->getType());
                $this->{$name} = $value;
            }
        }

        return $this;
    }

    private function castToPropertyType(mixed $value, ?ReflectionType $propertyType): mixed
    {
        if ($propertyType === null || !($propertyType instanceof ReflectionNamedType)) {
            return $value;
        }

        if ($propertyType->getName() === 'int') {
            return (int) $value;
        }

        return $value;
    }

    /**
     * @return ReflectionProperty[]
     */
    private function getPublicProperties(): array
    {
        $reflection = new ReflectionObject($this);

        return $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
    }

    public function save(): void
    {
        $properties = array_map(
            static fn (ReflectionProperty $property) => $property->getName(),
            array_filter(
                $this->getPublicProperties(),
                static fn (ReflectionProperty $property) => $property->getName() !== 'id'
            )
        );

        if ($this->created_at === null) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        $values = [];
        foreach ($properties as $property) {
            $values[$property] = $this->{$property};
        }

        $updateId = false;
        if ($this->id === null) {
            $query = \sprintf(
                'INSERT INTO %s (%s) VALUES (%s) RETURNING id',
                $this->tableName,
                implode(', ', $properties),
                implode(', ', array_map(static fn ($property) => ':' . $property, $properties))
            );
            $updateId = true;
        } else {
            $query = 'UPDATE';
        }

        $ret = $this->connection->query($query, $values);

        if (!$updateId) {
            return;
        }

        if (\count($ret) !== 1 || !\is_array($ret[0]) || \count($ret[0]) !== 1) {
            throw new RuntimeException('Failed to save entity');
        }
        $this->id = (int) $ret[0]['id'];
    }

    public function getBy(string $property, mixed $value): self
    {
        $query = \sprintf(
            'SELECT * FROM %s WHERE %s = :value',
            $this->tableName,
            $property
        );

        $result = $this->connection->query($query, ['value' => $value]);

        if (\count($result) !== 1 || !\is_array($result[0])) {
            throw new RuntimeException('Failed to get entity');
        }

        return $this->hydrate($result[0]);
    }
}

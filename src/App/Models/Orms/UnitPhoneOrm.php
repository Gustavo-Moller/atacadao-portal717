<?php

namespace Src\App\Models\Orms;
use Src\Interfaces\Database\IOrm;

class UnitPhoneOrm implements IOrm
{
    private \StdClass $row;
    public function __construct()
    {
        $this->row = (object) [
            "id" => null,
            "number" => null,
            "sector" => null,
            "owner" => null,
            "unitId" => null
        ];
    }

    public function __get(string $column): mixed
    {
        return $this->row->$column;
    }

    public function __set(string $column, mixed $value): void
    {
        $this->set($column, $value);
    }

    public function set(string $column, mixed $value): void
    {
        $this->row->$column = $value;
    }

    public function getRow(...$columns): \StdClass
    {
        if(empty($columns)) {
            return $this->row;
        }

        return (object) array_reduce($columns,
            fn($columnsFiltered, $columnFiltered) =>
                [...$columnsFiltered, $columnFiltered => $this->row->$columnFiltered],
            []
        );
    }

    public function getRowExcept(...$columns): \StdClass
    {
        $filteredColumns = array_diff(array_keys((array) $this->row), (array) $columns);

        return $this->getRow(...$filteredColumns);
    }

    public function getUnitOrm(): ?UnitOrm
    {
        if(!empty($this->row->unitId)) {
            return (new UnitOrm())->loadBy("id", $this->row->unitId);
        }

        return null;
    }
}
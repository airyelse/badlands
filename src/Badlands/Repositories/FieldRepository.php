<?php

namespace Dcat\Admin\Badlands\Repositories;

use App\Models\Systems\Data\FieldModel;
use App\Models\Systems\Data\ViewModel;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

class FieldRepository
{

    protected ViewModel $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function removeAll()
    {
        return $this->view->fields()->delete();
    }


    /**
     * @throws \Exception
     */
    public function remove(string $name)
    {
        throw new \Exception("Not implemented");
    }

    public function create(Column $column): FieldModel
    {
        return FieldModel::firstOrCreate(["view_uuid" => $this->getViewModel()->uuid, "name" => $column->getName()], [
            "name" => $column->getName(),
            "view_uuid" => $this->getViewModel()->uuid,
            "form_uuid" => $this->getViewModel()->form_uuid,
            "order" => $this->getViewModel()->fields()->max("order") + 1,
            "form_settings" => ["type" => $this->typeof($column->getType(), $column->getName())]
        ]);
    }

    /**
     * @throws Exception
     */
    public function listColumns(): Collection
    {
        return Collection::make(
            Schema::connection($this->getViewModel()->form->connection_name)
                ->getConnection()
                ->getDoctrineSchemaManager()
                ->listTableColumns($this->getViewModel()->form->table_name));

    }

    public function typeof(Type $type, string $columnName): string
    {
        // 根据字段名称约定返回类型
        switch ($columnName) {
            case 'created_at' :
            case 'updated_at' :
            case 'id':
            case 'uuid':
                return 'display';
        }
        // 根据字段类型自动生成
        try {
            return match (Type::getTypeRegistry()->lookupName($type)) {
                'integer', 'bigint' => "number",
                'decimal' => "decimal",
                'text' => "textarea",
                "datetime" => "datetime",
                default => "text",
            };
        } catch (Exception $e) {
            return "text";
        }
    }

    public function getViewModel(): ViewModel
    {
        return $this->view;
    }
}

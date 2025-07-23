<?php

namespace App\Models;

use App\Exceptions\ModelNotFoundException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

abstract class BaseModel extends Model
{
    use SoftDeletes {
        runSoftDelete as laravelSoftDelete;
    }
    protected static bool $softDeletes = true;
    protected $guarded = [];
    protected $perPage = 10;
    protected array $_changed_data = [];
    protected bool $isReallyUpdated = false;
    protected bool $withUpdatedAtField = true;
    protected string $modelTitle = 'model';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $fields = [
            static::CREATED_AT => 'date:Y-m-d H:i:s',
        ];

        if ($this->withUpdatedAtField) {
            $fields[static::UPDATED_AT] = 'date:Y-m-d H:i:s';
        }

        $this->casts = array_merge($this->casts, $fields);
    }

    public static function insert(array $rows): bool
    {
        $timestamp = date('Y-m-d');
        foreach ($rows as &$row) {
            $row['created_at'] = $timestamp;
            $row['updated_at'] = $timestamp;
        }

        return static::query()->insert($rows);
    }

    public static function withCreatorJoin(
        array  $columns = [],
        string $pkColumn = 'created_by',
        string $fieldName = 'creator_full_name',
    ): Builder {
        $builder = static::query();
        $tableName = $builder->getModel()->getTable();

        if ($columns == []) {
            $columns[] = "$tableName.*";
        }

        $columns[] = DB::raw('TRIM(CONCAT(creator.first_name, \' \', creator.last_name)) AS creator_full_name');

        return $builder
            ->join('users AS creator', 'creator.id', "$tableName.$pkColumn")
            ->select($columns);
    }

    /**
     * @return static
     * @throws BindingResolutionException
     */
    public static function new(): static
    {
        return app()->make(static::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        if (self::$softDeletes) {
            static::deleting(function (BaseModel $model) {
                // TODO: implement log trail
                //                $model->update(['deleted_by' => Auth::id()]);
                return $model;
            });
        }

        static::updating(function (BaseModel $model) {
            $model->isReallyUpdated = true;

            // Keep track of changed data
            $original = $model->getOriginal();
            foreach (array_keys($model->getDirty()) as $key) {
                $model->_changed_data[$key] = $original[$key] ?? null;
            }
        });
    }

    public function columnMustBe(string $colName, mixed $value): void
    {
        if (!$this->columnIs($colName, $value)) {
            throw new ModelNotFoundException($this->getNotFoundMessage());
        }
    }

    public function columnIs(string $colName, mixed $value): bool
    {
        return $this[$colName] === $value;
    }

    public function getNotFoundMessage(): string
    {
        return "Such {$this->getModelTitle()} does not exists";
    }

    /**
     * Get model displayable name
     *
     * @return string
     */
    public function getModelTitle(): string
    {
        return $this->modelTitle;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getChangedData(): array
    {
        return $this->_changed_data;
    }

    public function isReallyUpdated(): bool
    {
        return $this->isReallyUpdated;
    }
}

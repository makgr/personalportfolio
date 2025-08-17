<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->update($data);
    }

    public function delete($id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function find($id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    public function findOrFail($id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function findBy(string $field, $value, array $columns = ['*']): ?Model
    {
        return $this->model->where($field, $value)->first($columns);
    }

    public function findWhere(array $where, array $columns = ['*']): Collection
    {
        return $this->model->where($where)->get($columns);
    }

    // Additional helper methods
    public function with($relations)
    {
        return $this->model->with($relations);
    }

    public function orderBy($column, $direction = 'asc')
    {
        return $this->model->orderBy($column, $direction);
    }

    public function where($column, $operator = null, $value = null)
    {
        return $this->model->where($column, $operator, $value);
    }
}

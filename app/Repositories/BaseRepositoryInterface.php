<?php

namespace App\Repositories;

use App\Http\Requests\UserRequest;
use Illuminate\Database\Eloquent\Model;
use Request;

/**
 * Interface EloquentRepositoryInterface
 * @package App\Repositories
 */
interface BaseRepositoryInterface
{

    /**
     * @param $id
     * @return Model
     */
    public function find($id): ?Model;

    /**
     * @param array $attributes
     * @return Collection|Model[]
     */
    public function findAll(array $attributes);

    /**
     * @param $id
     * @return Model
     */
    public function delete($id): ?Model;
}

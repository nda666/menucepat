<?php

namespace App\Repositories;

use App\Http\Requests\UserRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create(FormRequest $request)
    {
        return $this->model->create($request);
    }

    /**
     * @param array $attributes
     * @param string|int $id
     * @return Model
     */
    public function update(FormRequest $request)
    {
        return $this->model->where(
            $this->model->getKeyName(),
            $request->post($this->model->getKeyName())
        )
            ->update([$request->toArray()]);
    }

    /**
     * @param $id
     * @return Model
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param $id
     * @return Collection|Model[]
     */
    public function findAll(Request $request)
    {
        return $this->model->all();
    }

    /**
     * @param $id
     * @return Collection|Model[]
     */
    public function paginate(Request $request)
    {
        return $this->model->paginate($request->get('page'));
    }

    /**
     * @param $id
     * @return Model
     */
    public function delete($id)
    {
        $model = $this->model->findOrFail($id);
        $model->delete();
        return $model;
    }
}

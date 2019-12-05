<?php

namespace SmartlyJobs\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

abstract class MainRepository extends BaseRepository
{

    abstract public function declareFilter();

    /**
     * @inheritdoc
     */
    public function filter(array $filters = [])
    {
        $filter = $this->declareFilter();

        $this->model = (new $filter(request()))->add($filters)->filter($this->model);

        return $this;
    }

}

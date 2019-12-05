<?php

namespace SmartlyJobs\Entities\Permission\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use SmartlyJobs\Entities\Permission\Contracts\PermissionRepository;
use SmartlyJobs\Entities\Permission\Models\Permission;

class PermissionRepositoryEloquent extends BaseRepository implements PermissionRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permission::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}

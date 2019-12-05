<?php

namespace SmartlyJobs\Entities\User\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use SmartlyJobs\Entities\User\Contracts\UserRepository;
use SmartlyJobs\Entities\User\Models\User;

/**
 * Class UserRepositoryEloquent
 * @package namespace Nodepole\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    protected $fieldSearchable = [
        User::FIRST_NAME => 'like'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}

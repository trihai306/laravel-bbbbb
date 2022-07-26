<?php

namespace App\Admin\Repositories;

use App\Models\HistoryPayment as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class HistoryPayment extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

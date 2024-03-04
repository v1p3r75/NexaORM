<?php

namespace Models;

use Entities\UserEntity;
use Nexa\Models\Model;

class User extends Model
{

    protected $entity = UserEntity::class;

    protected $fillable = ['*'];

    protected $timestamp = false;

}
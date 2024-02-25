<?php

namespace Models;

use Entities\ProfileEntity;
use Nexa\Models\Model;

class Profile extends Model
{

    protected $entity = ProfileEntity::class;

    protected $timestamp = false;

}
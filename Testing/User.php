<?php

namespace Testing;

require '../vendor/autoload.php';

use Nexa\Attributes\Dates\Date;
use Nexa\Attributes\Entity;
use Nexa\Attributes\Strings\Text;

#[Entity]
class User
{
    #[Text(45)]
    public $firstname;

    #[Text]
    public $lastname;

    #[Date]
    public $date_creation;
}
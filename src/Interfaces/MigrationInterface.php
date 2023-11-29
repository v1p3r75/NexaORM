<?php

namespace Nexa\Attributes;

interface MigrationInterface {

    public function up();

    public function down();
}
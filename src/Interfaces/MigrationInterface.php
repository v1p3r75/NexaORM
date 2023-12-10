<?php

namespace Nexa\Interfaces;

interface MigrationInterface {

    public function up();

    public function down();
}
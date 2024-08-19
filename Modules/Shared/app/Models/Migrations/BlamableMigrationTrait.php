<?php

namespace Modules\Shared\Models\Migrations;

trait BlamableMigrationTrait
{
    public function runBlamable($table)
    {
        $table->uuid('created_by');
        $table->uuid('updated_by');
        $table->uuid('deleted_by')->nullable();
    }
}

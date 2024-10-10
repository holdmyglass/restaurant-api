<?php

namespace Modules\Shared\Models\Migrations;

trait VersionableTraitMigration
{
    public function runVersionable($table)
    {
        $table->uuid('version_identifier');
        $table->integer('version')->default(1);
        $table->boolean('is_current_version')->default(true)->index();
        $table->boolean('is_deleted')->default(false);
    }
}

<?php

namespace Modules\Shared\Models\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Shared\Exceptions\InvalidVersionException;

trait VersionableTrait
{
    private $internalSave = false;

    protected $currentVersionAttribute = 'is_current_version';

    protected $deletedAttribute = 'is_deleted';

    /**
     * Get the casts array for the model.
     */
    public function getCasts(): array
    {
        return array_merge(parent::getCasts(), [
            'is_current_version' => 'boolean',
        ]);
    }

    protected static function bootVersionableTrait()
    {
        static::creating(function ($model) {
            if (! $model->version_identifier) {
                $model->version_identifier = Str::uuid();
                $model->version = 1;
            }
        });
    }

    /**
     * Update the model in the database, creating a new version if necessary and handling relationships.
     *
     * @return Model|bool|null
     */
    public function update(array $attributes = [], array $options = []): mixed
    {
        if (! $this->{$this->currentVersionAttribute}) {
            throw new InvalidVersionException(__('shared::messages.error.only_current_version_can_be_updated'));
        }
        // Ensure the model is dirty to avoid unnecessary updates
        if ($this->isDirty()) {
            // Check if any preservable attributes have changed
            $versionableAttributesChanged = false;
            foreach ($this->getPreservableAttributes() as $attribute) {
                if ($this->isDirty($attribute)) {
                    if ($this->getOriginal($attribute) !== $this->getAttribute($attribute)) {
                        $versionableAttributesChanged = true;
                        break;
                    }
                }
            }

            // If preservable attributes have changed, create a new version
            if ($versionableAttributesChanged) {
                // Create a new version of the model
                $newVersion = $this->replicate();
                $newVersion->version = $this->version + 1;
                $newVersion->{$this->currentVersionAttribute} = true; // Set the new version as current
                $newVersion->save();

                // Update the current version to mark it as not current
                $this->{$this->currentVersionAttribute} = false;
                $this->save();

                // Replicate pivot relationships
                $this->replicatePivotRelationships();
                $this->replicateHasOneRelationships();
                $this->replicateHasManyRelationships();

                return $newVersion;
            }

            // Update the model with the provided attributes
            return parent::update($attributes, $options);
        }

        return null;
    }

    /**
     * Replicate pivot table relationships for the new version, handling circular references and custom pivot models.
     */
    protected function replicatePivotRelationships()
    {
        $visitedRelationships = [];

        foreach ($this->getRelations() as $relationshipName => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                $pivotTable = $relation->getTable();
                $foreignKey = $relation->getForeignKey();
                $relatedForeignKey = $relation->getRelatedKey();

                // Check for circular references
                if (in_array($relationshipName, $visitedRelationships)) {
                    continue;
                }
                $visitedRelationships[] = $relationshipName;

                // Find existing pivot records for the current version
                $existingPivotRecords = $relation->getPivot()->where($foreignKey, $this->getKey())->get();

                // Create new pivot records for the new version
                foreach ($existingPivotRecords as $pivotRecord) {
                    $newPivotRecord = $pivotRecord->replicate();
                    $newPivotRecord->{$foreignKey} = $this->getKey();
                    $newPivotRecord->{$relatedForeignKey} = $pivotRecord->{$relatedForeignKey};
                    $newPivotRecord->save();

                    // Handle custom pivot models
                    if ($pivotRecord instanceof Model) {
                        $this->replicateModel($newPivotRecord);
                    }
                }
            }
        }
    }

    /**
     * Replicate one-to-one relationships for the new version.
     */
    protected function replicateHasOneRelationships()
    {
        foreach ($this->getRelations() as $relationshipName => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasOne) {
                $relatedModel = $relation->getRelated();
                $foreignKey = $relation->getForeignKey();

                // Find existing related record for the current version
                $existingRelatedRecord = $relatedModel->where($foreignKey, $this->getKey())->first();

                // Create new related record for the new version
                if ($existingRelatedRecord) {
                    $newRelatedRecord = $existingRelatedRecord->replicate();
                    $newRelatedRecord->{$foreignKey} = $this->getKey();
                    $newRelatedRecord->save();

                    // Handle custom pivot models
                    if ($newRelatedRecord instanceof Model) {
                        $this->replicateModel($newRelatedRecord);
                    }
                }
            }
        }
    }

    /**
     * Replicate one-to-many relationships for the new version.
     */
    protected function replicateHasManyRelationships()
    {
        foreach ($this->getRelations() as $relationshipName => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
                $relatedModel = $relation->getRelated();
                $foreignKey = $relation->getForeignKey();

                // Find existing related records for the current version
                $existingRelatedRecords = $relatedModel->where($foreignKey, $this->getKey())->get();

                // Create new related records for the new version
                foreach ($existingRelatedRecords as $relatedRecord) {
                    $newRelatedRecord = $relatedRecord->replicate();
                    $newRelatedRecord->{$foreignKey} = $this->getKey();
                    $newRelatedRecord->save();

                    // Handle custom pivot models
                    if ($newRelatedRecord instanceof Model) {
                        $this->replicateModel($newRelatedRecord);
                    }
                }
            }
        }
    }

    /**
     * Get the attributes that should be preserved when updating.
     *
     * @return array
     */
    abstract protected function getPreservableAttributes();

    public function hasVersionableTrait(): bool
    {
        return true;
    }

    /**
     * Save method for the models that need to be versioned
     *
     * @return bool|Model|null
     */
    public function saveWithVersion(array $options = [])
    {
        if ($this->isDirty() && ! $this->internalSave) {
            $this->internalSave = true;
            $newVersion = $this->update($this->getDirty());
            $this->internalSave = false; // Reset the flag

            return $newVersion;
        }

        $result = parent::save($options);

        return $result instanceof Model ? $result : $this;
    }

    /**
     * Only set is_deleted flag to true for this model
     *
     * @throws InvalidVersionException
     */
    public function deleteWithoutVersion(): bool
    {
        if (! $this->{$this->currentVersionAttribute}) {
            throw new InvalidVersionException(__('shared::messages.error.only_current_version_can_be_deleted'));
        }
        $this->{$this->deletedAttribute} = true;
        $this->{$this->currentVersionAttribute} = false;

        return $this->save();
    }

    /**
     * Set is_deleted flag to true for all versions of this model
     *
     * @throws InvalidVersionException
     */
    public function deleteWithVersion(): bool
    {
        // Get all older versions with the same version_identifier
        $olderVersions = self::where('version_identifier', $this->version_identifier)
            ->where($this->currentVersionAttribute, false)
            ->get();

        // Set is_deleted to true for all older versions
        foreach ($olderVersions as $olderVersion) {
            $olderVersion->{$this->deletedAttribute} = true;
            $olderVersion->save();
        }

        // Set is_deleted to true for the current version
        $this->{$this->deletedAttribute} = true;

        return $this->save();
    }
}

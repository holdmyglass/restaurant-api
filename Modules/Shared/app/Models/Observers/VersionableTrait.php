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
     */
    public function update(array $attributes = [], array $options = []): ?Model
    {
        if (! $this->{$this->currentVersionAttribute}) {
            throw new InvalidVersionException(__('shared::messages.error.only_current_version_can_be_updated'));
        }

        if ($this->isDirty()) {
            $versionableAttributesChanged = false;
            foreach ($this->getPreservableAttributes() as $attribute) {
                if ($this->isDirty($attribute)) {
                    $versionableAttributesChanged = true;
                    break;
                }
            }

            if ($versionableAttributesChanged) {

                $original = $this->findOrFail($this->id);
                $original->{$this->currentVersionAttribute} = false;
                $original->save();

                $newVersion = $this->replicate();
                $newVersion->version = $this->version + 1;
                $newVersion->{$this->currentVersionAttribute} = true; // Set the new version as current
                $newVersion->save();

                // Replicate relationships
                $this->replicateRelationships();

                return $newVersion;
            }

            parent::update($attributes, $options);

            return $this;
        }

        return null;
    }

    /**
     * Replicate all types of relationships for the new version.
     */
    protected function replicateRelationships()
    {
        $this->replicatePivotRelationships();
        $this->replicateHasOneRelationships();
        $this->replicateHasManyRelationships();
        $this->replicateMorphManyRelationships();
        $this->replicateMorphToRelationships();
    }

    protected function replicatePivotRelationships()
    {
        foreach ($this->getRelations() as $relationshipName => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                $this->replicateBelongsToMany($relation);
            }
        }
    }

    protected function replicateBelongsToMany($relation)
    {
        $existingPivotRecords = $relation->getPivot()->where($relation->getForeignKey(), $this->getKey())->get();

        foreach ($existingPivotRecords as $pivotRecord) {
            $newPivotRecord = $pivotRecord->replicate();
            $newPivotRecord->{$relation->getForeignKey()} = $this->getKey();
            $newPivotRecord->{$relation->getRelatedKey()} = $pivotRecord->{$relation->getRelatedKey()};
            $newPivotRecord->save();
        }
    }

    protected function replicateHasOneRelationships()
    {
        foreach ($this->getRelations() as $relationshipName => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasOne) {
                $this->replicateHasOne($relation);
            }
        }
    }

    protected function replicateHasOne($relation)
    {
        $existingRelatedRecord = $relation->getRelated()->where($relation->getForeignKey(), $this->getKey())->first();

        if ($existingRelatedRecord) {
            $newRelatedRecord = $existingRelatedRecord->replicate();
            $newRelatedRecord->{$relation->getForeignKey()} = $this->getKey();
            $newRelatedRecord->save();
        }
    }

    protected function replicateHasManyRelationships()
    {
        foreach ($this->getRelations() as $relationshipName => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
                $this->replicateHasMany($relation);
            }
        }
    }

    protected function replicateHasMany($relation)
    {
        $existingRelatedRecords = $relation->getRelated()->where($relation->getForeignKey(), $this->getKey())->get();

        foreach ($existingRelatedRecords as $relatedRecord) {
            $newRelatedRecord = $relatedRecord->replicate();
            $newRelatedRecord->{$relation->getForeignKey()} = $this->getKey();
            $newRelatedRecord->save();
        }
    }

    /**
     * Replicate polymorphic one-to-many relationships for the new version.
     */
    protected function replicateMorphManyRelationships()
    {
        foreach ($this->getRelations() as $relationshipName => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\MorphMany) {
                $this->replicateMorphMany($relation);
            }
        }
    }

    protected function replicateMorphMany($relation)
    {
        $existingRelatedRecords = $relation->getRelated()->where($relation->getMorphType(), $this->getMorphClass())
            ->where($relation->getMorphKey(), $this->getKey())->get();

        foreach ($existingRelatedRecords as $relatedRecord) {
            $newRelatedRecord = $relatedRecord->replicate();
            $newRelatedRecord->{$relation->getMorphKey()} = $this->getKey();
            $newRelatedRecord->save();
        }
    }

    /**
     * Replicate polymorphic one-to-one relationships for the new version.
     */
    protected function replicateMorphToRelationships()
    {
        foreach ($this->getRelations() as $relationshipName => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\MorphTo) {
                $this->replicateMorphTo($relation);
            }
        }
    }

    protected function replicateMorphTo($relation)
    {
        $relatedModel = $relation->getRelated();
        $foreignKey = $relation->getForeignKey();

        // Find existing related record for the current version
        $existingRelatedRecord = $relatedModel->where($foreignKey, $this->getKey())->first();

        if ($existingRelatedRecord) {
            $newRelatedRecord = $existingRelatedRecord->replicate();
            $newRelatedRecord->{$foreignKey} = $this->getKey();
            $newRelatedRecord->save();
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
    public function saveWithVersion(array $options = []): bool|Model|string|null
    {
        if ($this->isDirty() && ! $this->internalSave) {
            $this->internalSave = true;
            $newVersion = $this->update($this->getDirty());
            $this->internalSave = false; // Reset the flase

            return $newVersion;
        }

        parent::update($this->getDirty(), $options);

        return $this;
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

<?php

namespace twa\smsautils\Models\Traits;

use twa\smsautils\Models\Courier;
use twa\smsautils\Models\Operator;

trait HasPolymorphicActivity
{
    /**
     * Get the activity performer (courier or operator)
     */
    public function activityPerformer()
    {
        return $this->morphTo('activity_by', 'activity_by_model', 'activity_by_id');
    }

    /**
     * Get the activity performer's name
     */
    public function getActivityPerformerNameAttribute()
    {
        if (!$this->activity_by_model || !$this->activity_by_id) {
            return 'Unknown';
        }

        $model = $this->activityPerformer;
        
        if (!$model) {
            return 'Unknown';
        }

        // Return the appropriate name based on the model type
        if ($model instanceof Courier) {
            $name = $model->getFullNameAttribute();
            return $name ?: 'Unknown Courier';
        } elseif ($model instanceof Operator) {
            $name = $model->getFullNameAttribute();
            return $name ?: 'Unknown Operator';
        }

        // Fallback to direct property access
        if (isset($model->name)) {
            return $model->name;
        }
        
        if (isset($model->first_name) && isset($model->last_name)) {
            return trim($model->first_name . ' ' . $model->last_name);
        }

        return 'Unknown';
    }

    /**
     * Get the activity performer's type (human-readable)
     */
    public function getActivityPerformerTypeAttribute()
    {
        if (!$this->activity_by_model) {
            return null;
        }

        return match ($this->activity_by_model) {
            Courier::class => 'Courier',
            Operator::class => 'Operator',
            default => 'Unknown'
        };
    }

    /**
     * Scope to filter by activity performer type
     */
    public function scopeByActivityPerformerType($query, $type)
    {
        return $query->where('activity_by_model', $type);
    }

    /**
     * Scope to filter by courier activities
     */
    public function scopeByCourier($query)
    {
        return $query->byActivityPerformerType(Courier::class);
    }

    /**
     * Scope to filter by operator activities
     */
    public function scopeByOperator($query)
    {
        return $query->byActivityPerformerType(Operator::class);
    }
} 
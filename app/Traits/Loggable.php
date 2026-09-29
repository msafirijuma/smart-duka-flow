<?php

namespace App\Traits;

use App\Models\ActivityLog;
use \App\Models\Employee;
use \App\Models\Department;
use \App\Models\LeaveRequest;
use \App\Models\LeaveType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Loggable
{

    public static function bootLoggable()
    {
        static::created(function ($model) {
            self::logActivity('created', $model, null, $model->toArray());
        });

        static::updated(function ($model) {

            $oldValues = array_intersect_key($model->getOriginal(), $model->getChanges());
            $newValues = $model->getChanges();

            if (!empty($newValues)) {
                self::logActivity('updated', $model, $oldValues, $newValues);
            }
        });

        static::deleted(function ($model) {
            self::logActivity('deleted', $model, $model->toArray(), null);
        });
    }

    protected static function logActivity($action, $model, $old = null, $new = null)
    {
        try {
            ActivityLog::create([
                'user_id'     => Auth::check() ? Auth::id() : null,
                'action'      => $action,
                'model_type'  => get_class($model),
                'model_id'    => $model->id ?? null,
                'description' => self::getDescription($action, $model),
                'old_values'  => !empty($old) ? json_encode($old) : null,
                'new_values'  => !empty($new) ? json_encode($new) : null,
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error("Activity Log Error: " . $e->getMessage());
        }
    }

    protected static function getDescription($action, $model)
    {
        $name = class_basename($model);

        if ($model instanceof Employee) {
            return ucfirst($action) . ' employee: ' . ($model->first_name ?? '') . ' ' . ($model->last_name ?? '');
        }

        if ($model instanceof LeaveType) {
            return ucfirst($action) . ' leave type: ' . ($model->name ?? '');
        }

        if ($model instanceof LeaveRequest) {
            return ucfirst($action) . ' leave request';
        }

        if ($model instanceof Department) {
            return ucfirst($action) . ' department: ' . ($model->name ?? '');
        }

        return ucfirst($action) . ' ' . $name;
    }
}
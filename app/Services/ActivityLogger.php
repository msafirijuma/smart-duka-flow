<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(
        string $action,
        string $description,
        ?Model $subject = null,
        ?int $shopId = null,
        array $properties = []
    ): void {
        try {
            ActivityLog::create([
                'user_id'      => Auth::id(),
                'shop_id'      => $shopId ?? session('current_shop_id'),
                'action'       => $action,
                'description'  => $description,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id'   => $subject?->getKey(),
                'properties'   => $properties ?: null,
                'ip'           => Request::ip(),
            ]);
        } catch (\Throwable $e) {
            report($e); // not interfere main request
        }
    }
}
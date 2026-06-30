<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Str;

trait HasAuditLog
{
    public static function bootHasAuditLog()
    {
        foreach (static::getObservableEvents() as $eventName) {
            static::registerModelEvent($eventName, function ($model) use ($eventName) {
                if (in_array($eventName, ['created', 'updated', 'deleted'])) {
                    AuditLog::create([
                        'uuid' => Str::uuid(),
                        'model_type' => static::class,
                        'model_id' => $model->id,
                        'event' => $eventName,
                        'changes' => $model->getChanges(),
                        'user_data' => auth()->user() ? [
                            'id' => auth()->user()->id,
                            'name' => auth()->user()->name ?? null,
                        ] : null,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            });
        }
    }
}

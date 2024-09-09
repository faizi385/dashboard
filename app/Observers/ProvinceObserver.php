<?php
namespace App\Observers;

use App\Models\ProvinceLog;
use App\Models\Province;
use Illuminate\Support\Facades\Auth;

class ProvinceObserver
{
    public function created(Province $province)
    {
        $actionUserId = Auth::check() ? Auth::id() : null;

        ProvinceLog::create([
            'user_id' => $actionUserId,
            'province_id' => $province->id,
            'action' => 'created',
            'description' => json_encode([
                'name' => $province->name,
                'slug' => $province->slug,
                'timezone_1' => $province->timezone_1,
                'timezone_2' => $province->timezone_2,
                'tax_value' => $province->tax_value,
                'status' => $province->status,
            ]),
        ]);
    }

    public function updated(Province $province)
    {
        $actionUserId = Auth::check() ? Auth::id() : null;

        $changes = $province->getChanges();

        ProvinceLog::create([
            'user_id' => $actionUserId,
            'province_id' => $province->id,
            'action' => 'updated',
            'description' => json_encode([
                'old' => $province->getOriginal(),
                'new' => $changes,
            ]),
        ]);
    }
}

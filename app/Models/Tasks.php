<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tasks extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title', 'deadline', 'description', 'owner_id', 'assignee_id', 'status'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deadline' => 'date',
    ];

    // only the `updated` event will get logged automatically
    protected static $recordEvents = ['updated'];

    /*
     * Customize activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    /**
     * This method will allow you to fill properties and add custom fields before the activity is saved.
     *
     * @param Activity $activity
     * @param string $eventName
     * @return false|void
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $viewer = auth()->user();

        $properties = json_decode($activity->properties, true);
        $old_values = $properties['old'] ?? [];
        $new_values = $properties['attributes'] ?? [];

        $changed_fields = array_diff($old_values, $new_values);
        $changed_keys = array_keys($changed_fields);

        $changed_key = array_shift($changed_keys);
        $changed_value = $this->getChangedFieldValues($old_values, $new_values, $changed_key);
        $activity->description = $viewer->getName() . ' have changed the task ' . $this->getChangedFieldKey($changed_key) . ($changed_value ? (' ' . $changed_value) : '' ) . '.';

        foreach ($changed_keys as $changed_key) {
            $changed_value = $this->getChangedFieldValues($old_values, $new_values, $changed_key);
            $activity->description .= "; \n" . $viewer->getName() . ' have changed the task ' . $this->getChangedFieldKey($changed_key) . ($changed_value ? (' ' . $changed_value) : '' ) . '.';
        }
    }

    /**
     * Get parsed changed field name.
     *
     * @param $key
     * @return string
     */
    private function getChangedFieldKey($key)
    {
        switch ($key) {
            case 'assignee_id':
                return 'Assignee';
            default:
                return $key;
        }
    }

    /**
     * Get parsed changed field values.
     *
     * @param $old_values
     * @param $new_values
     * @param $key
     * @return string|null
     */
    private function getChangedFieldValues($old_values, $new_values, $key)
    {
        switch ($key) {
            case 'status':
                return '"'. ucfirst(str_replace('_', ' ', $old_values[$key])) . '" -> "'. ucfirst(str_replace('_', ' ', $new_values[$key])) .'"';
            case 'assignee_id':
                $old_assignee = User::find($old_values[$key]) ? User::find($old_values[$key])->getName() : 'DELETED USER';
                $new_assignee = User::find($new_values[$key]) ? User::find($new_values[$key])->getName() : 'DELETED USER';
                return '"'. $old_assignee . '" -> "'. $new_assignee .'"';
            case 'title':
                return '"'. $old_values[$key] . '" -> "'. $new_values[$key] .'"';
            case 'deadline':
                return '"'. date('Y-m-d', strtotime($old_values[$key])) . '" -> "'. date('Y-m-d', strtotime($new_values[$key])) .'"';
            default:
                return null;
        }
    }
}

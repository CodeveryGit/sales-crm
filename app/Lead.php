<?php

namespace Codevery;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lead extends Model
{
    protected $guarded = [];
    protected $appends = ['lead_type_name', 'statuses'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks()
    {
        return $this->hasMany(LeadTask::class)->orderBy('date_time', 'asc');
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }


    public function lead_type()
    {
        return $this->belongsTo(LeadType::class);
    }

    public function logs()
    {
        return $this->hasMany(LeadStatusLog::class);
    }

    public function fields()
    {
        return $this->belongsToMany(LeadTypeField::class, 'lead_lead_type_field', 'lead_id', 'lead_type_field_id')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function getLeadTypeNameAttribute()
    {
        return LeadType::find($this->lead_type_id)->name;
    }

    public function getStatusesAttribute()
    {
        return LeadType::find($this->lead_type_id)->statuses;
    }


    public function attachField($field)
    {
        $hasField = $this->fields()->where('id', $field['id'])->exists();

        if (!$hasField) {
            $this->fields()->attach([
                $field['id'] => ['value' => $field['value']]
            ]);
        } else {
            $this->fields()->updateExistingPivot($field['id'], ['value' => $field['value']]);
        }

        return $this;
    }

    public function changeStatus($data)
    {
        $this->logs()->create([
            'status_id' => $data['status_id'],
            'comment' => $data['comment']
        ]);

        $this->current_status = $data['position'];
        $this->save();

    }

    public function getIntersectingFields()
    {
        $allFields = $this->lead_type->fields;
        $fieldsWithValue = $this->fields;

        $intersectingFields = [];

        foreach ($allFields as $leadtype_field) {
            foreach ($fieldsWithValue as $lead_field) {
                if ($leadtype_field->id == $lead_field->pivot->lead_type_field_id) {
                    $leadtype_field['value'] = $lead_field['pivot']['value'];
                }
            }
            $intersectingFields[] = $leadtype_field;
        }

        return $intersectingFields;
    }

    /**
     * max leads - 4 for every status, other autoload with scroll
     * if admin get all
     * if chief get chief`s managers, get chief himself and get their leads
     * if manager get self
     * @param $statuses
     * @param $manager
     * @return \Illuminate\Support\Collection
     */
    public static function getIndexLeadsByRole($manager, $statuses)
    {
        $leads = collect();
        $conditions[] = ['archived', '!=', '1'];
        $manager_id = $manager->isAdmin() ? 0 : $manager->id;
        $limit = 4;

        foreach ($statuses as $status) {
            if ($manager->isChief()) {
                $cycle_conditions = [];
                $cycle_conditions[] = ['lead_type_id', '=', $status->lead_type_id];
                $cycle_conditions[] = ['current_status', '=', $status->position];
                $cycle_conditions = array_merge($conditions, $cycle_conditions);
                $status_leads = $manager->getChiefModels('leads', $cycle_conditions, $limit);
            } else {
                $status_leads = $status->getLeads($manager_id, $conditions, $limit);
            }
            $leads = $leads->merge($status_leads);
        }
        return $leads;
    }

    public static function getIndexLeadsByRoleAjax($manager, $statuses, $request)
    {
        $leads = collect();
        $conditions[] = ['archived', '!=', '1'];
        $manager_id = $manager->isAdmin() ? 0 : $manager->id;
        $limit = 4;
        $date = [];
        $offset = $request['offset'] ?: 0;

        if ($request['manager_id']) $conditions[] = ['manager_id', '=', $request['manager_id']];
        if ($request['search']) $conditions[] = ['name', 'like', "%" . $request['search'] . "%"];
        if ($request['date_start'] && $request['date_end']) {
            $date = [
                Carbon::parse($request['date_start'])->startOfDay(),
                Carbon::parse($request['date_end'])->endOfDay()
            ];
        }

        if ($request['current_status'] === 'all') {
            foreach ($statuses as $status) {
                if ($manager->isChief()) {
                    $cycle_conditions = [];
                    $cycle_conditions[] = ['lead_type_id', '=', $status->lead_type_id];
                    $cycle_conditions[] = ['current_status', '=', $status->position];
                    $cycle_conditions = array_merge($conditions, $cycle_conditions);
                    $status_leads = $manager->getChiefModels('leads', $cycle_conditions, $limit, $date);
                } else {
                    $status_leads = $status->getLeads($manager_id, $conditions, $limit, $date);
                }
                $leads = $leads->merge($status_leads);
            }
            return compact('statuses', 'leads');
        } else {
            $position = $request['current_status'];
            $status = $statuses->filter(function ($item) use ($position) {
                return $item->position == $position;
            })->first();

            if ($manager->isChief()) {
                $conditions[] = ['lead_type_id', '=', $status->lead_type_id];
                $conditions[] = ['current_status', '=', $status->position];
                $leads = $manager->getChiefModels('leads', $conditions, $limit, $date, $offset);
            } else {
                $leads = $status->getLeads($manager_id, $conditions, $limit, $date, $offset);
            }

            return $leads;
        }
    }
}

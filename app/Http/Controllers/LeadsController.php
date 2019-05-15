<?php

namespace Codevery\Http\Controllers;

use Codevery\Client;
use Codevery\Lead;
use Codevery\LeadType;
use Codevery\LeadTypeStatus;
use Codevery\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Codevery\Traits\ModelTrait;

class LeadsController extends Controller
{
    use ModelTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = [];
        $leadtypeStatuses = [];
        $managers = collect();
        $leads = collect();

        $manager = Auth::user();

        $leadtypes = LeadType::with('fields')->get();

        // get managers list for filter/creating
        if ($manager->isHighLevelAccess()) {
            if ($manager->isAdmin()) {
                $managers = Manager::where([
                    ['position', '!=', 'admin'],
                    ['active', '=', '1'],
                ])->get();
            } elseif ($manager->isChief()) {
                $managers = Manager::where([
                    ['position', '!=', 'admin'],
                    ['chief_id', '=', $manager->id],
                    ['active', '=', '1'],
                ])->orWhere('id', $manager->id)->get();
            }

            $models['manager'] = $managers;
        }

        // get clients list for creating
        $clients = Client::getIndexClientsByRole($manager);

        $models += ['client' => $clients, 'leadtype' => $leadtypes];
        $absentModels = $this->checkIssetModel($models);

        if (!in_array('leadtype', $absentModels )) {
            $leadtypeStatuses = $leadtypes->first()->load('statuses')->statuses;
            $leads = Lead::getIndexLeadsByRole($manager, $leadtypeStatuses);
        }

        return view('leads.index', compact('leads', 'leadtypes', 'leadtypeStatuses', 'managers', 'clients', 'absentModels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'url' => 'active_url|nullable',
            'lead_type_id' => 'required|integer|exists:lead_types,id',
            'priority' => ['required', 'string', Rule::in(config('constants.PRIORITIES'))],
            'client_id' => 'required|integer',
            'manager_id' => 'integer|exists:managers,id',
            'payment_type' => ['required', 'string', Rule::in(config('constants.PAYMENT_TYPES'))],
            'payment_quantity' => 'required|integer',
            'comment' => 'string|nullable',
            'fields' => 'array',
            'fields.*.field' => 'string',
            'fields.*.value' => 'string',
        ]);

        if (!Auth::user()->isHighLevelAccess()) {
            $data['manager_id'] = Auth::id();
        }

        if ($data['client_id'] == 0) {
            $data['client_id'] = null;
        }

        $lead = Lead::create([
            'name' => $data['name'],
            'url' => $data['url'],
            'lead_type_id' => $data['lead_type_id'],
            'priority' => $data['priority'],
            'client_id' => $data['client_id'],
            'manager_id' => $data['manager_id'],
            'payment_type' => $data['payment_type'],
            'payment_quantity' => $data['payment_quantity'],
            'comment' => $data['comment'],
        ]);


        if (isset($data['fields'])) {
            foreach ($data['fields'] as $field) {
                $lead->attachField($field);
            }
        }

        $lead->changeStatus([
            'position' => 0,
            'status_id' => $lead->lead_type->statuses->first()->id,
            'comment' => 'Lead created'
        ]);

        return redirect(route('leads.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param Lead $lead
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        $leadtypes = LeadType::latest()->get();
        $managers = [];
        $manager = Auth::user();

        // get managers list for filter/creating
        if ($manager->isHighLevelAccess()) {
            if ($manager->isAdmin()) {
                $managers = Manager::where([
                    ['position', '!=', 'admin'],
                    ['active', '=', '1'],
                ])->get();
            } elseif ($manager->isChief()) {
                $managers = Manager::where([
                    ['position', '!=', 'admin'],
                    ['chief_id', '=', $manager->id],
                    ['active', '=', '1'],
                ])->orWhere('id', $manager->id)->get();
            }

            if ($managers->isEmpty()) $absentModels[] = 'manager';
        }

        // get clients list for creating
        $clients = Client::getIndexClientsByRole($manager);
        if ($clients->isEmpty()) $absentModels[] = 'client';

        $intersectingFields = $lead->getIntersectingFields();

        return view('leads.show', compact('lead', 'leadtypes', 'clients', 'managers', 'intersectingFields'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Lead $lead
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $data = $request->validate([
            'name' => 'required|string',
            'url' => 'active_url|nullable',
            'lead_type_id' => 'required|integer|exists:lead_types,id',
            'priority' => ['required', 'string', Rule::in(config('constants.PRIORITIES'))],
            'client_id' => 'required|integer',
            'payment_type' => ['required', 'string', Rule::in(config('constants.PAYMENT_TYPES'))],
            'payment_quantity' => 'required|integer',
            'manager_id' => 'integer|exists:managers,id',
            'comment' => 'string|nullable',
        ]);

        if ($data['client_id'] == 0) {
            $data['client_id'] = null;
        }

        $lead->update($data);

        return redirect(route('leads.show', $lead));
    }

    public function updateManagerId(Request $request, Lead $lead)
    {
        $lead->update($request->validate([
            'manager_id' => 'required|integer'
        ]));

        return back();
    }

    public function archive(Lead $lead)
    {
        $lead->archived = true;
        $lead->save();

        return redirect()->route('leads.index');
    }

    public function unarchive(Lead $lead)
    {
        $lead->archived = false;
        $lead->save();

        return redirect()->route('leads.index');
    }

    public function archiveIndex()
    {
        if (!Auth::user()->isHighLevelAccess()) {
            $leads = Lead::latest()->where([
                ['archived', '=', '1'],
                ['manager_id', '=', Auth::user()->id]
            ])->with('manager', 'client')->get();
        } else {
            $leads = Lead::latest()->where('archived', '=', '1')->with('manager', 'client')->get();
        }

        return view('leads.archiveIndex', compact('leads'));
    }

    public function archiveShow(Lead $lead)
    {
        $this->authorize('view', $lead);

        $status = LeadTypeStatus::where([
            ['lead_type_id', '=', $lead->lead_type_id],
            ['position', '=', $lead->current_status]
        ])->first();

        return view('leads.archiveShow', compact('lead', 'status'));
    }
}

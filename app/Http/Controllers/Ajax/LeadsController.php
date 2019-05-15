<?php

namespace Codevery\Http\Controllers\Ajax;

use Codevery\Http\Controllers\Controller;
use Codevery\Lead;
use Codevery\LeadTypeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $manager = Auth::user();
        $leadtypeStatuses = LeadTypeStatus::where('lead_type_id', $request['lead_type_id'])->get();
        return Lead::getIndexLeadsByRoleAjax($manager, $leadtypeStatuses, $request);
    }

    /**
     * Create new task related with lead (for manager)
     *
     * @param Request $request
     * @param Lead $lead
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */
    public function storeTask(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'date' => 'required|date_format:Y-m-d H:i:s|after:today',
            'comment' => 'required:string',
        ]);
        $task = $lead->tasks()->create([
            'date_time' => $data['date'],
            'manager_id' => Auth::id(),
        ]);
        $task->comments()->create([
            'comment' => $data['comment'],
            'manager_id' => Auth::id(),
        ]);
        return response($task, 201);
    }

    public function getLogs(Lead $lead)
    {
        return $lead->logs;
    }

    public function changeStatus(Lead $lead, Request $request)
    {
        $data = $request->validate([
            'status_id' => 'required|integer',
            'comment' => 'string|nullable',
            'position' => 'integer',
        ]);

        $lead->changeStatus($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Lead $lead
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Lead $lead)
    {
        $this->authorize('destroy');

        $lead->delete();

    }
}

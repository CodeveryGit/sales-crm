<?php

namespace Codevery\Http\Controllers\Ajax;

use Codevery\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Codevery\LeadTask;
use Illuminate\Http\Request;


class LeadTasksController extends Controller
{
    /**
     * Get all comments to task related with lead (for manager)
     *
     * @param LeadTask $LeadTask
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(LeadTask $LeadTask)
    {
        $comments = $LeadTask->comments()->get();

        return response($comments, 201);
    }

    /**
     * Add comment to task related with lead (for manager)
     * @param Request $request
     * @param LeadTask $LeadTask
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request, LeadTask $LeadTask)
    {
        $data = $request->validate([
            'state' => 'required|boolean',
            'comment' => 'required:string',
        ]);

        $comments = $LeadTask->comments()->create(
            [ 'state' => $data['state'],
              'comment' => $data['comment'],
              'manager_id' => Auth::id()]
        );

        return response($comments, 201);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Events\AcceptOrDecineProposalEvent;
use App\Http\Controllers\Controller;
use App\Models\ProjectContract;
use App\Models\ProjectOffer;
use Illuminate\Http\Request;

class HandleApplicateController extends Controller
{
    public function accept(Request $request, ProjectOffer $offer)
    {
        $project = $offer->project;

        if ($request->user()->id != $offer->project->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You dont unthenticated to use this feature.'
            ], 403);
        }

        if ($project->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'This project has already set in-process.'
            ], 403);
        }

        $project->offers->where('id', '!=', $offer->id)->each(function ($record) {
            $record->update(['status' => 2]); // Decined
            event(new AcceptOrDecineProposalEvent($record, 'declined'));
        });

        $offer->update(['status' => 3]); // approved
        $project->update(['status' => 2]); // set as in-process
        ProjectContract::create([
            'student_id' => $offer->user_id,
            'owner_id' => $project->user_id,
            'project_id' => $project->id,
            'started_at' => now()
        ]);
        event(new AcceptOrDecineProposalEvent($offer, 'approved'));

        return response()->json([
            'success' => true,
            'message' => 'The offer was updated.'
        ], 200);
    }

    public function decine(Request $request, ProjectOffer $offer)
    {
        $project = $offer->project;

        if ($request->user()->id != $offer->project->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You dont unthenticated to use this feature.'
            ], 403);
        }

        $offer->update(['status' => 2]); // Decined
        event(new AcceptOrDecineProposalEvent($offer, 'declined'));

        return response()->json([
            'success' => true,
            'message' => 'The offer was declined.'
        ], 200);
    }
}

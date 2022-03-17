<?php

namespace App\Http\Controllers\Api;

use App\Events\InviteEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\InviteResource;
use App\Listeners\InviteListener;
use App\Models\Invite;
use App\Models\Project;
use App\Models\ProjectContract;
use App\Models\User;
use Illuminate\Http\Request;

class ApiInviteController extends Controller
{
    public function index(Request $request)
    {
        $student = User::find($request->student_id);
        $project = Project::find($request->project_id);

        if ($project->status > 1) {
            return response()->json([
                'success' => false,
                'message' => 'This project has already set in process or completed'
            ], 400);
        }

        $isExistProject = Invite::query()
            ->where('project_id', $project->id)
            ->where('status', 1)
            ->first();

        if ($isExistProject) {
            return response()->json([
                'success' => false,
                'message' => 'You had already have an invitation for this project'
            ], 400);
        }

        // check if student is already invited
        $isExistStudent = Invite::query()
            ->where('student_id', $student->id)
            ->where('project_id', $project->id)
            ->where('status', 1)
            ->first();

        if ($isExistStudent) {
            return response()->json([
                'success' => false,
                'message' => 'This student has already invited to this project'
            ], 400);
        }

        // store into table
        $invite = Invite::create([
            'student_id' => $student->id,
            'project_id' => $project->id,
        ]);

        // Send a notify to student
        event(new InviteEvent($invite));

        return response()->json([
            'success' => true,
            'invite' => $invite
        ]);
    }

    public function acceptOrDecline(Request $request)
    {
        $status = $request->status;
        $invite = Invite::findOrFail($request->invite_id);

        // make sure the invitation is not in declined status
        if (!$invite) {
            return response()->json([
                'success' => false,
                'message' => 'The invitation was not found'
            ], 400);
        }

        // move to contract table
        ProjectContract::create([
            'student_id' => $invite->user->id,
            'owner_id' => $invite->project->poster->id,
            'project_id' => $invite->project->id,
            'started_at' => now()
        ]);
        $invite->project->update(['status' => 2]); // set as in-process

        event(new InviteEvent($invite, $status));

        return response()->json([
            'success' => true,
            'message' => 'The invitation was updated.'
        ], 200);
    }

    public function decline(Invite $invite)
    {
    }

    public function byStudent($studentId)
    {
        $invited = Invite::query()
            ->with(['project.poster', 'user'])
            ->where('student_id', $studentId)
            ->get();

        return InviteResource::collection($invited);
    }
}

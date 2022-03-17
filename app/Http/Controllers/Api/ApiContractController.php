<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Models\ProjectContract;
use Illuminate\Http\Request;

class ApiContractController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userId = $request->user()->id;
        $query = ProjectContract::query()->with(['project', 'owner', 'student']);
        $query = $user->isPoster() ? $query->where('owner_id', $userId) : $query->where('student_id', $userId);

        return ContractResource::collection($query->orderBy('id', 'desc')->get());
    }

    public function show($id)
    {
        $contract = ProjectContract::query()
            ->with(['project', 'project.documents', 'owner', 'student'])
            ->where('id', $id)
            ->firstOrFail();

        return new ContractResource($contract);
    }
}

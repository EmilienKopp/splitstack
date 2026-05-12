<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGitLogRequest;
use App\Http\Requests\UpdateGitLogRequest;
use App\Models\GitLog;

class GitLogController extends HybridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGitLogRequest $request)
    {
        $validated = $request->validated();

        foreach ($validated['logs'] as $logData) {
            GitLog::upsert(
                [
                    'user_id' => $request->user()->id,
                    'commit_hash' => $logData['commit_hash'],
                    'message' => $logData['message'],
                    'timestamp' => $logData['timestamp'],
                ],
                ['commit_hash', 'user_id']
            );
        }

        return response()->json(['message' => 'Git logs stored successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(GitLog $gitLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GitLog $gitLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGitLogRequest $request, GitLog $gitLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GitLog $gitLog)
    {
        //
    }
}

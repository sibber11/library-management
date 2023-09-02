<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Inertia\Inertia;


/**
 * todo: member 
 */
class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->acceptsJson() && request()->wantsJson() && request()->ajax()) {
            $data = request()->validate([
                'search' => 'nullable|string|max:64',
            ]);
            
            return Member::whereHas('user', function ($query) use ($data) {
                    $query->where('name', 'like', "%{$data['search']}%");
                })->paginate()
                ->withQueryString()
                ->through(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->user->name,
                    ];
                });
        }

        return Inertia::render('Admin/Member/Index', [
            'members' => Member::paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Member/Fields',[
            'users' => \App\Models\User::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = new Member();
        $member->user_id = $request->user_id;
        $member->save();

        return redirect()->route('members.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        return Inertia::render('Admin/Member/Fields', [
            'member' => $member,
            'users' => \App\Models\User::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->user_id = $request->user_id;
        $member->save();
        return redirect()->route('members.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('members.index');
    }
}

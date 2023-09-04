<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;


/**
 * todo: member 
 */
class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        $members = Member::with(['user'])
            ->when($request->has('filter'), function ($query) use ($request) {
                switch ($request->filter) {
                    case 'active':
                        $query->active();
                        break;
                    case 'expired':
                        $query->expired();
                        break;
                }
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('user', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->paginate()
            ->withQueryString();
        return Inertia::render('Admin/Member/Index', [
            'members' => $members,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Member/Fields', [
            'users' => \App\Models\User::whereDoesntHave('member')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = new Member();
        $member->user_id = $request->user_id;
        $member->membership_due_date = now()->addMonths($request->validated('membership_duration'));
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
            'users' => [$member->user],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->extendMembership($request->integer('membership_duration'));
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

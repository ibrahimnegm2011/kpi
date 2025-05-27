<?php

namespace App\Http\Controllers\Account;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Mail\InviteAgent;
use App\Models\Account;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AgentsController extends Controller
{
    public function index()
    {
        return view('account.agents.index', [
            'users' => QueryBuilder::for(User::class)->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('company', 'agent_assignments.company_id'),
                AllowedFilter::exact('department', 'agent_assignments.department_id'),
                AllowedFilter::scope('active'),
            ])
                ->where('type', UserType::AGENT())
                ->withWhereHas('account_agent_assignments')
                ->with('account_agent_assignments.company', 'account_agent_assignments.department')
                ->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?User $user = null)
    {
        if ($user && in_array($user->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        $assignments = [];
        if (old('assignments')) {
            $assignments = old('assignments', '[]'); // json string
            $assignments = json_decode($assignments, true);
            $companies = Company::whereIn('id', Arr::pluck($assignments, 'company_id'))->pluck('name', 'id');
            $departments = Department::whereIn('id', Arr::pluck($assignments, 'department_id'))->pluck('name', 'id');
            $assignments = Arr::map($assignments, fn ($assignment) => [
                'companyId' => $assignment['company_id'],
                'companyName' => $companies[$assignment['company_id']] ?? '',
                'departmentId' => $assignment['department_id'],
                'departmentName' => $departments[$assignment['department_id']] ?? '',
            ]);
        }

        if ($user) {
            $user->load([
                'agent_assignments' => fn ($query) => $query->where('account_id', Auth::user()->account_id)
                    ->with('company', 'department'),
            ]);

            $assignments = $user->agent_assignments
                ->map(fn ($assignment) => [
                    'companyId' => $assignment->company_id,
                    'companyName' => $assignment->company->name,
                    'departmentId' => $assignment->department_id,
                    'departmentName' => $assignment->department->name,
                ])
                ->values()->toArray();
        }

        return view('account.agents.form', compact('user', 'assignments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'assignments' => ['required', 'string'], // It will be a JSON string
            'position' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (User::where('email', $data['email'])
            ->whereHas('agent_assignments', fn ($q) => $q->where('id', Auth::user()->account_id))
            ->exists()) {
            return back()->withErrors(['email' => 'Email already exists.'])->withInput();
        }

        // Decode the assignments JSON string
        $assignments = json_decode($request->input('assignments'), true);

        // Ensure it's a non-empty array
        if (! is_array($assignments) || empty($assignments)) {
            return back()->withErrors(['assignments' => 'You must assign at least one company and department.'])->withInput();
        }

        // Now validate each company_id and department_id in the array
        foreach ($assignments as $assignment) {
            validator($assignment, [
                'company_id' => ['required', Rule::exists('companies', 'id')],
                'department_id' => ['required', Rule::exists('departments', 'id')],
            ])->validate();
        }
        $data['assignments'] = $assignments;

        $data['type'] = UserType::AGENT();
        $data['is_active'] = $request->boolean('is_active');
        $data['created_by'] = Auth::id();

        try {
            DB::transaction(function () use ($data) {
                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    Arr::except($data, ['company_id', 'department_id', 'position', 'assignments']),
                );

                foreach ($data['assignments'] as $assignment) {
                    $user->agent_assignments()->create([
                        'account_id' => Auth::user()->account_id,
                        'company_id' => $assignment['company_id'],
                        'department_id' => $assignment['department_id'],
                        'position' => $data['position'] ?? null,
                        'created_by' => Auth::id(),
                    ]);
                }

                Mail::to($user)->sendNow(new InviteAgent(
                    user: $user,
                    account: Account::find(Auth::user()->account_id),
                ));
            });
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());

            return redirect(route('account.agents.create'))
                ->withInput()
                ->with(['error' => app()->environment('local') ? $th->getMessage() : 'Something wrong. Try again or contact administrator.']);
        }

        return redirect(route('account.agents.index'))
            ->with(['success' => 'User has been created and Invitation has been sent.']);
    }

    public function update(User $user, Request $request)
    {
        $data = $request->validate([
            'assignments' => ['required', 'string'], // It will be a JSON string
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Decode the "assignments" JSON string
        $assignments = json_decode($request->input('assignments'), true);

        // Ensure it's a non-empty array
        if (! is_array($assignments) || empty($assignments)) {
            return back()->withErrors(['assignments' => 'You must assign at least one company and department.'])->withInput();
        }

        // Now validate each company_id and department_id in the array
        foreach ($assignments as $assignment) {
            validator($assignment, [
                'company_id' => ['required', Rule::exists('companies', 'id')],
                'department_id' => ['required', Rule::exists('departments', 'id')],
            ])->validate();
        }
        $data['assignments'] = $assignments;
        $data['is_active'] = $request->boolean('is_active');

        $user->update(Arr::except($data, ['assignments']));

        $this->updateAgentAssignments($user, $data['assignments']);

        return redirect(route('account.agents.index'))->with(['success' => 'User has been updated successfully']);
    }

    public function delete(User $user)
    {
        $user->agent_assignments()->where('account_id', Auth::user()->account_id)->delete();

        return redirect(route('account.agents.index'))->with(['success' => 'User has been deleted successfully.']);
    }

    private function updateAgentAssignments(User $user, array $assignments)
    {
        $arrayToString = fn ($arr) => json_encode(ksort($arr) ? $arr : $arr);

        $new = collect($assignments)->map($arrayToString);
        $old = $user->agent_assignments()
            ->where('account_id', Auth::user()->account_id)->get()
            ->map(fn ($assignment) => $arrayToString([
                'company_id' => $assignment->company_id,
                'department_id' => $assignment->department_id,
            ]));

        // Add: In new but not in old
        $toAdd = $new->diff($old)->map(fn ($item) => json_decode($item, true))->values();
        // Remove: In old but not in new
        $toRemove = $old->diff($new)->map(fn ($item) => json_decode($item, true))->values();

        $isChanged = $toAdd->isNotEmpty() || $toRemove->isNotEmpty();

        if (! $isChanged) {
            return false;
        }

        // Add new assignments
        $user->agent_assignments()->createMany([
            ...$toAdd->map(fn ($assignment) => [
                'company_id' => $assignment['company_id'],
                'department_id' => $assignment['department_id'],
                'account_id' => Auth::user()->account_id,
                'created_by' => Auth::id(),
            ]),
        ]);

        // Remove assignments
        foreach ($toRemove as $assignment) {
            $user->agent_assignments()
                ->where('company_id', $assignment['company_id'])
                ->where('department_id', $assignment['department_id'])
                ->where('account_id', Auth::user()->account_id)
                ->delete();
        }

        return true;
    }
}

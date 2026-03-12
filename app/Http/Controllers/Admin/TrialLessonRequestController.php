<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrialLessonRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TrialLessonRequestController extends Controller
{
    public function index(Request $request): View
    {
        if (!Schema::hasTable('trial_lesson_requests')) {
            $tableMissing = true;
            $requests = $this->emptyPaginator($request, 20);

            return view('admin.trial-lesson-requests.index', compact('requests', 'tableMissing'));
        }

        $tableMissing = false;
        $status = trim((string) $request->query('status', ''));
        $keyword = trim((string) $request->query('keyword', ''));

        $query = TrialLessonRequest::query()
            ->with('user:id,name,email,phone')
            ->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        if ($keyword !== '') {
            $query->where(function ($inner) use ($keyword) {
                $inner->where('phone', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%')
                            ->orWhere('phone', 'like', '%' . $keyword . '%');
                    });
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.trial-lesson-requests.index', compact('requests', 'tableMissing', 'status', 'keyword'));
    }

    public function updateStatus(Request $request, TrialLessonRequest $trialLessonRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $trialLessonRequest->status = $validated['status'];
        $trialLessonRequest->save();

        return redirect()->back()->with([
            'messege' => __('Updated Successfully'),
            'alert-type' => 'success',
        ]);
    }

    private function emptyPaginator(Request $request, int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, (int) $request->input('page', 1), [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}


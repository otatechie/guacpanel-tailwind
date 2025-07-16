<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SystemNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminSystemNoticeController extends Controller
{
    public function index()
    {
        $systemNotices = SystemNotice::all()->map(function ($notice) {
            $notice->created_at_human = $notice->created_at->diffForHumans();
            $notice->visible_from_formatted = $notice->visible_from?->format('M j, Y g:ia');
            $notice->expires_at_formatted = $notice->expires_at?->format('M j, Y g:ia');
            return $notice;
        });
        return Inertia::render('Admin/Notice/IndexPage', [
            'systemNotices' => $systemNotices
        ]);
    }


    public function create()
    {
        return Inertia::render('Admin/Notice/CreatePage');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', 'in:info,success,warning,error'],
            'visible_from' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'is_dismissible' => ['required', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $notice = new SystemNotice();
        $notice->title = $validated['title'];
        $notice->content = $validated['content'];
        $notice->type = $validated['type'];
        $notice->visible_from = $validated['visible_from'];
        $notice->expires_at = $validated['expires_at'];
        $notice->is_dismissible = $validated['is_dismissible'];
        $notice->is_active = $validated['is_active'] ?? true;
        $notice->created_by = Auth::id();
        $notice->save();

        return redirect()->route('admin.system-notice.index')->with('success', 'System notice created successfully');
    }


    public function edit(SystemNotice $systemNotice)
    {
        return Inertia::render('Admin/Notice/EditPage', [
            'systemNotice' => $systemNotice
        ]);
    }


    public function update(Request $request, SystemNotice $systemNotice)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', 'in:info,success,warning,error'],
            'visible_from' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'is_dismissible' => ['required', 'boolean'],
        ]);

        $systemNotice->title = $validated['title'];
        $systemNotice->content = $validated['content'];
        $systemNotice->type = $validated['type'];
        $systemNotice->visible_from = $validated['visible_from'];
        $systemNotice->expires_at = $validated['expires_at'];
        $systemNotice->is_dismissible = $validated['is_dismissible'];
        $systemNotice->is_active = $request->get('is_active', true);
        $systemNotice->save();

        return redirect()->route('admin.system-notice.index')->with('success', 'System notice updated successfully');
    }


    public function destroy(SystemNotice $systemNotice)
    {
        $systemNotice->delete();
        return redirect()->route('admin.system-notice.index')->with('success', 'System notice deleted successfully');
    }
}

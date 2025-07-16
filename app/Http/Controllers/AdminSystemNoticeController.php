<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SystemNotice;
use Illuminate\Http\Request;

class AdminSystemNoticeController extends Controller
{
    public function index()
    {
        $systemNotices = SystemNotice::all();
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
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
    }


    public function edit(SystemNotice $systemNotice)
    {
        return Inertia::render('Admin/Notice/EditPage', [
            'systemNotice' => $systemNotice
        ]);
    }
    

    public function update(Request $request, SystemNotice $systemNotice)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
    }


    public function destroy(SystemNotice $systemNotice)
    {
        $systemNotice->delete();
        return redirect()->route('admin.system-notices.index')->with('success', 'System notice deleted successfully');
    }
}

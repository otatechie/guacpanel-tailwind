<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::select('id', 'name', 'created_at', 'parent_id')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'created_at' => $category->created_at->diffForHumans(),
                    'parent_id' => $category->parent_id,
                    'parent_name' => $category->parent ? $category->parent->name : 'Top Level Category',
                ];
            });

        return Inertia::render('Admin/Category/IndexPage', [
            'categories' => $categories
        ]);
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id'
            ],
        ]);

        Category::create($validatedData);

        session()->flash('success', 'Category added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id'
            ],
        ]);

        $category->update($validatedData);

        session()->flash('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        session()->flash('success', 'Category deleted successfully');
    }
}

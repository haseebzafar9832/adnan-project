<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\Slugs;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()->withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', [
            'categories' => $categories,
            'icons' => $this->icons(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.form', [
            'category' => new Category(),
            'icons' => $this->icons(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Slugs::unique(Category::class, $data['name']);
        Category::query()->create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category added.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', [
            'category' => $category,
            'icons' => $this->icons(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validated($request);
        $data['slug'] = Slugs::unique(Category::class, $data['name'], $category->id);
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Move or delete the products in this category first.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'icon' => ['required', Rule::in(array_keys($this->icons()))],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function icons(): array
    {
        return [
            'basket3' => 'Groceries',
            'phone' => 'Electronics',
            'house' => 'Household',
            'droplet' => 'Personal care',
            'pencil-square' => 'Office',
            'bag' => 'General',
            'heart-pulse' => 'Health',
            'cup-hot' => 'Beverages',
        ];
    }
}

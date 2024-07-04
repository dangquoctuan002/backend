<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request)
    {
        $categories = $this->categoryRepository->listWhere(
            [
                "page" => $request->get('page', 1),
                "limit" => 11
            ]
        );
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response()->json(['status' => 1, 'data' => $category]);

    }

    public function store(Request $request)
    {
        $name = $request->input('name');

        if (Category::where('name', $name)->exists()) {
            return response()->json(['status' => 0, 'message' => 'The category name already exists'], 400);
        }

        $category = $this->categoryRepository->createData($request->all());

        return response()->json(['status' => 1, 'data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $name = $request->input('name');

        if (Category::where('name', $name)->where('id', '!=', $id)->exists()) {
            return response()->json(['status' => 0, 'message' => 'The category name already exists'], 400);
        }

        $category = $this->categoryRepository->updateWhere($request->all(), $id);

        return response()->json(['status' => 1, 'data' => $category]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $relatedDataCount = $category->products()->count();

        if ($relatedDataCount > 0) {
            return response()->json(['status' => 0, 'message' => 'This category cannot be deleted because there is associated data'], 400);
        }

        $category = $this->categoryRepository->deleteWhere($id);

        return response()->json(['status' => 1, 'message' => 'The category has been successfully deleted']);
    }


    public function getProductsByCategory(Request $request)
    {
        $categoryName = $request->category;
        $category = Category::where('name', $categoryName)->first();

        if ($category) {
            $products = Product::where('category_id', $category->id)->take(5)->get();
            return response()->json($products);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

}

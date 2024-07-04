<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Repositories\ColorRepository;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    protected $colorRepository;
    public function __construct(ColorRepository $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function index(Request $request)
    {
        $colors = $this->colorRepository->listWhere(
            [
                "page" => $request->get('page', 1),
                "limit" => 9
            ]
        );
        return response()->json($colors);
    }

    public function show($id)
    {
        $color = Color::findOrFail($id);

        return response()->json(['status' => 1, 'data' => $color]);

    }

    public function store(Request $request)
    {
        $name = $request->input('name');

        if (Color::where('name', $name)->exists()) {
            return response()->json(['status' => 0, 'message' => 'The color name already exists'], 400);
        }

        $color = $this->colorRepository->createData($request->all());

        return response()->json(['status' => 1, 'data' => $color]);
    }

    public function update(Request $request, $id)
    {
        $name = $request->input('name');

        if (Color::where('name', $name)->where('id', '!=', $id)->exists()) {
            return response()->json(['status' => 0, 'message' => 'The category name already exists'], 400);
        }

        $color = $this->colorRepository->updateWhere( $request->all(), $id);

        return response()->json(['status' => 1, 'data' => $color]);
    }
   
    public function destroy($id)
    {
        $color = Color::findOrFail($id);
        $relatedDataCount = $color->products()->count();
    
        if ($relatedDataCount > 0) {
            return response()->json(['status' => 0, 'message' => 'This category cannot be deleted because there is associated data'], 400);
        }
    
        $color = $this->colorRepository->deleteWhere($id);
    
        return response()->json(['status' => 1, 'message' => 'The category has been successfully deleted']);
    }

}

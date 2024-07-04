<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $brandRepository;
    public function __construct(BrandRepository $brandRepository){
        $this->brandRepository = $brandRepository;
    }
    
    public function index(Request $request)
    {
        $brands = $this->brandRepository->listWhere([
            "page"=>$request->get('page',1),
            "limit"=>8]
        );
        return response()->json($brands);
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json(['status' => 1, 'data' => $brand]);
    }

    public function store(Request $request)
    {
        $name = $request->input('name');

        if (Brand::where('name', $name)->exists()) {
            return response()->json(['status' => 0, 'message' => 'The brand name already exists'], 400);
        }

        $brand = $this->brandRepository->createData($request->all());

        return response()->json(['status' => 1, 'data' => $brand]);
    }
    //    return $this->brandRepository->createData($request->all());
    

    public function update(Request $request, $id)
    {
        $name = $request->input('name');

        if (Brand::where('name', $name)->where('id', '!=', $id)->exists()) {
            return response()->json([' status' => 0, 'message' => 'The brand name already exists'], 400);
        }

        $brand = $this->brandRepository->updateWhere( $request->all(), $id);

        return response()->json(['status' => 1, 'data' => $brand]);
    //    return $this->brandRepository->updateWhere($request->all(),$id);
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $relatedDataCount = $brand->products()->count();
    
        if ($relatedDataCount > 0) {
            return response()->json(['status' => 0, 'message' => 'This brand cannot be deleted because there is associated data'], 400);
        }
    
        $brand = $this->brandRepository->deleteWhere($id);
    
        return response()->json(['status' => 1, 'message' => 'The brand has been successfully deleted']);
    }
}

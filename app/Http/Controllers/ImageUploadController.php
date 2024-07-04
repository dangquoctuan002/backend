<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->file('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('images/products', $imageName, 'public');

            return response()->json([
                'status' => 'success',
                'image_name' => $imageName
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid image'
        ], 400);
    }
}

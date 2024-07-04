<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Repositories\ReviewRepository;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewRepository;
    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function index(Request $request)
    {
        $reviews = $this->reviewRepository->listWhere(
            [
                "page" => $request->get('page', 1),
                "limit" => 9
            ]
        );
        return response()->json($reviews);
    }

    public function show($id)
    {
        $review = Review::findOrFail($id);
        return response()->json($review);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        return $this->reviewRepository->createData($request->all());
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        return $this->reviewRepository->updateWhere($request->all(), $id);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return response()->json(null, 204);
    }
    public function getReviews($product_id)
    {
        $reviews = Review::where('product_id', $product_id)->with('user')->get();
        return response()->json($reviews);
    }
}

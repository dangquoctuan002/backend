<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Repositories\OrderItemRepository;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    protected $orderitemRepository;
    public function __construct(OrderItemRepository $orderitemRepository){
        $this->orderitemRepository = $orderitemRepository;
    }
    
    public function index(Request $request)
    {
        $orderitems = $this->orderitemRepository->listWhere([
            "page"=>$request->get('page',1),
            "limit"=>9]
        );
        return response()->json($orderitems);
    }

    public function show($id)
    {
        $orderitem = OrderItem::findOrFail($id);
        return response()->json($orderitem);
    }

    public function store(Request $request)
    {
       return $this->orderitemRepository->createData($request->all());
    }

    public function update(Request $request, $id)
    {
       return $this->orderitemRepository->updateWhere($request->all(),$id);
    }

    public function destroy($id)
    {
        $orderitem = OrderItem::findOrFail($id);
        $orderitem->delete();
        return response()->json(null, 204);
    }
}

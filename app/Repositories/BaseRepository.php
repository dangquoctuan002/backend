<?php

namespace App\Repositories;

abstract class BaseRepository
{
    public function listWhere(array $att = [], $with = [], $orderBy = '')
    {
        $query = $this->model()::query();
        $pageIndex = (int) $att['page'] - 1;
        $pageSize = (int) $att['limit'];
    
        if (count($with) > 0) {
            $query->with($with);
        }
    
        if (isset($att['keyword']) && $att['keyword'] != '') {
            $keyword = $att['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                    ->orWhere('description', 'like', "%$keyword%"); // Assuming you want to search in these fields
            });
        }
    
        if ($orderBy == '_name_') {
            $query->orderBy("name", "asc");
        }
    
        if (isset($att['star']) && $att['star'] != '') {
            $query->where('rating', '=', $att['star']);
        }
    
        if (isset($att['color']) && $att['color'] != '') {
            $query->where('color_id', '=', $att['color']);
        }
    
        if (isset($att['category']) && $att['category'] != '') {
            $query->where('category_id', '=', $att['category']);
        }
    
        if (isset($att['brand']) && $att['brand'] != '') {
            $query->where('brand_id', '=', $att['brand']);
        }
    
        if (isset($att['range']) && $att['range'] != '') {
            $query->whereBetween("price", explode('->', $att['range']));
        }
    
        // Đếm tổng số bản ghi trước khi phân trang
        $totalRecords = $query->count();
        $totalPage = ceil($totalRecords / $pageSize);
    
        // Thêm sắp xếp theo giá sau khi đếm tổng số bản ghi
        if ($orderBy == 'price') {
            $query->orderBy("price", "asc");
        }
    
        $dataResult = $query
            ->skip($pageSize * $pageIndex)
            ->take($pageSize)
            ->get();
    
        return [
            "data" => $dataResult,
            "totalPage" => $totalPage,
            "pageIndex" => $pageIndex + 1,
            'limit' => $pageSize
        ];
    }
    public function createData(array $data)
    {
        try {
            $newRecord = $this->model()::create($data);
            return [
                'status' => 'success',
                'message' => 'Record created successfully',
                'data' => $newRecord
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateWhere(array $data, $id)
    {
        try {
            $record = $this->model()::findOrFail($id);
            $record->update($data);
            return [
                'status' => 'success',
                'message' => 'Record updated successfully',
                'data' => $record
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteWhere($id)
    {
        try {
            $record = $this->model()::findOrFail($id);
            $record->delete();
            return [
                'status' => 'success',
                'message' => 'Record deleted successfully'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }



}


// public function listWhere(array $att = [], $with = [], $orderBy = '')
// {
//     $query        = $this->model()::query();
//     $pageIndex    = (int)$att['page'] - 1;
//     $pageSize     = (int)$att['limit'];

//     if (count($with) > 0) {
//         $query->with($with);
//     }

//     if ($orderBy != '' && $orderBy != '_name_') {
//         $query->orderBy("price", $orderBy);
//     }
//     if ($orderBy == '_name_') {
//         $query->orderBy("product_name", "asc");
//     }

//     if (isset($att['star']))
//         if ($att['star'] != '') {
//             $query->where(
//                 'rating',
//                 '=',
//                 $att['star'],
//             );
//         }

//     if (isset($att['color']))
//         if ($att['color'] != '') {
//             $query->where(
//                 'color_id',
//                 '=',
//                 $att['color'],
//             );
//         }

//     if (isset($att['range']))
//         if ($att['range'] != '') {
//             $range  = $att['range'];
//             $start  = explode('->', $range)[0];
//             $end    = explode('->', $range)[1];
//             if ($end == "max") {
//                 $query->where(
//                     "price",
//                     '>=',
//                     $start
//                 );
//             } else {
//                 $query->whereBetween(
//                     "price",
//                     [$start, $end]
//                 );
//             }
//         }
// if (isset($att['getCart'])) {
//     $query->where(
//         'status',
//         '=',
//         '0',
//     );
// }
// if (isset($att['where'])) {
//     $query->where(
//         $att['col'],
//         $att['math'],
//         $att['where'],
//     );
// }
//     $dataResult   =    $query
//         ->skip($pageSize * $pageIndex)
//         ->take($pageSize)
//         ->get();

//     if (isset($att['where'])) {
//         $totalPage    = ceil(
//             $this->model()::where(
//                 $att['col'],
//                 $att['math'],
//                 $att['where'],
//             )->count() / $pageSize
//         );
//     }else{
//         $totalPage    = ceil(
//             $this->model()::query()->count() / $pageSize
//         );
//     }

//     return [
//         "data"       => $dataResult,
//         "totalPage"  => $totalPage,
//         "pageIndex"  => $pageIndex + 1,
//         'limit'      => $pageSize
//     ];
// }
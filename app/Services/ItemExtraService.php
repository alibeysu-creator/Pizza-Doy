<?php

namespace App\Services;


use Exception;
use App\Models\ItemCategory;
use App\Models\ItemExtra;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ItemExtraRequest;

class ItemExtraService
{
    public $itemExtra;
    protected $itemExtraFilter = [
        'item_category_id',
        'name',
        'price',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, ItemCategory $itemCategory)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return ItemExtra::with('itemCategory')->where(['item_category_id' => $itemCategory->id])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->itemExtraFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(ItemExtraRequest $request, ItemCategory $itemCategory)
    {
        try {
            return ItemExtra::create($request->validated() + ['item_category_id' => $itemCategory->id]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ItemExtraRequest $request, ItemCategory $itemCategory, ItemExtra $itemExtra)
    {
        try {
            if ($itemCategory->id == $itemExtra->item_category_id) {
                return tap($itemExtra)->update($request->validated());
            } else {
                throw new Exception(trans('all.item_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(ItemCategory $itemCategory, ItemExtra $itemExtra)
    {
        try {
            if ($itemCategory->id == $itemExtra->item_category_id) {
                $itemExtra->delete();
            } else {
                throw new Exception(trans('all.item_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(ItemCategory $itemCategory, ItemExtra $itemExtra)
    {
        try {
            if ($itemCategory->id == $itemExtra->item_category_id) {
                return $itemExtra;
            } else {
                throw new Exception(trans('all.item_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
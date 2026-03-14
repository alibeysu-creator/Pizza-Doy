<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ItemCategory;
use App\Models\ItemExtra;
use App\Services\ItemExtraService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ItemExtraRequest;
use App\Http\Resources\ItemExtraResource;

class ItemExtraController extends AdminController
{
    public ItemExtraService $itemExtraService;

    public function __construct(ItemExtraService $itemExtraService)
    {
        parent::__construct();
        $this->itemExtraService = $itemExtraService;
        $this->middleware(['permission:items_show'])->only('index', 'show', 'store', 'update', 'destroy');
    }

    public function index(PaginateRequest $request, ItemCategory $itemCategory) : \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ItemExtraResource::collection($this->itemExtraService->list($request, $itemCategory));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function store(ItemExtraRequest $request, ItemCategory $itemCategory) : ItemExtraResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemExtraResource($this->itemExtraService->store($request, $itemCategory));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function update(ItemExtraRequest $request, ItemCategory $itemCategory, ItemExtra $itemExtra) : ItemExtraResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemExtraResource($this->itemExtraService->update($request, $itemCategory, $itemExtra));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(ItemCategory $itemCategory, ItemExtra $itemExtra) : ItemExtraResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemExtraResource($this->itemExtraService->show($itemCategory, $itemExtra));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(ItemCategory $itemCategory, ItemExtra $itemExtra) : \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->itemExtraService->destroy($itemCategory, $itemExtra);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}

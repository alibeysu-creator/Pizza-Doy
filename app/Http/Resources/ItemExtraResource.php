<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemExtraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) : array
    {
        return [
            'id'             => $this->id,
            'item_category_id' => $this->item_category_id,
            'name'           => $this->name,
            'price'          => $this->price,
            'currency_price' => AppLibrary::currencyAmountFormat($this->price),
            'flat_price'     => AppLibrary::flatAmountFormat($this->price),
            'convert_price'  => AppLibrary::convertAmountFormat($this->price),
            'status'         => $this->status,
            'apply_to_all'   => $this->apply_to_all,
            "item_category"  => optional($this->itemCategory)->name,
        ];
    }
}

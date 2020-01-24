<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BooleanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'result' => boolval($this->resource),
        ];
    }
}

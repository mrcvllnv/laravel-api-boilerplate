<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Exceptions\NotJWTSubjectException;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return new UserResource($this->resource);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws NotJWTSubjectException
     */
    public function with($request)
    {
        if (!$this->resource instanceof JWTSubject) {
            throw new NotJWTSubjectException;
        }

        return [
            'meta' => [
                'token' => [
                    'access_token' => auth()->login($this->resource),
                    'type'         => 'Bearer',
                    'expires_in'   => auth()->factory()->getTTL(),
                ],
            ],
        ];
    }
}

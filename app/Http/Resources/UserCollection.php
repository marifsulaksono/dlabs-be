<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $limit = $request->query('limit', 1);
        $links = collect($this->getUrlRange(1, $this->lastPage()))->map(function ($url) use ($limit) {
            return $url."&limit={$limit}";
        });

        return [
            'list' => $this->collection,
            'meta' => [
                'links' => $links,
                'total' => $this->total(),
            ],
        ];
    }
}

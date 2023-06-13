<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'summary'               => $this->summary,
            'rating'                => $this->rating,
            'price'                 => $this->price,
            'date_published'        => $this->date_published,
            'copies_purchased'      => (int) $this->purchases_sum_copies,
            'authors'               => AuthorResource::collection($this->whenLoaded('authors')),
        ];
    }
}

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
        $attrs = [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'summary'               => $this->summary,
            'rating'                => $this->rating,
            'price'                 => $this->price,
            'date_published'        => $this->date_published,
            'authors'               => AuthorResource::collection($this->whenLoaded('authors')),
        ];
        
        if ($this->purchases_sum_copies) {
            $attrs['copies_sold_in_month'] = (int) $this->purchases_sum_copies;
        }
        
        if ($this->relationLoaded('purchases')) {
            $attrs['copies_sold_in_total'] = $this->purchases->sum('copies');
        }

        return $attrs;
    }
}

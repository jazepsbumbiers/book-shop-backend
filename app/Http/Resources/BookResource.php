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
        
        // TODO include in base attrs arr,remove cast, check in frontend
        if ($this->purchases_sum_copies) {
            $attrs['copies_purchased_in_month'] = (int) $this->purchases_sum_copies;
        }

        $this->load('purchases'); // TODO: is this needed?
        
        // TODO include in base attrs arr
        $attrs['copies_purchased_in_total'] = $this->purchases->sum('copies');

        return $attrs;
    }
}

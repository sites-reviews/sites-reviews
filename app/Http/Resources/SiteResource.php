<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'title' => $this->title,
            'description' => $this->description,
            'rating' => $this->rating,
            'number_of_views' => $this->number_of_views,
            'number_of_reviews' => $this->number_of_reviews,
            'url' => (string)$this->getUrl()
        ];
    }
}

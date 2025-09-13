<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'kode_divisi' => $this->kode_divisi,
            'username' => $this->username,
            'nama' => $this->nama,
            'display_name' => $this->nama . ' (' . $this->username . ')',
            
            // Relationship data
            'divisi' => $this->whenLoaded('divisi', function () {
                return [
                    'kode_divisi' => $this->divisi->kode_divisi,
                    'divisi' => $this->divisi->divisi,
                ];
            }),
            
            // Additional metadata
            'composite_key' => $this->kode_divisi . '-' . $this->username,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

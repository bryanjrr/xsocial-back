<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        $imageMimeTypes = ['image', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'media' => $this->mediaPosts->map(function ($media) use ($imageMimeTypes) {
                return [
                    'file_url' => in_array($media->media_type, $imageMimeTypes)
                        ? base64_encode($media->file_url)
                        : $media->file_url,
                    'media_type' => $media->media_type,
                ];
            })->toArray(),
            'user' => [
                'id' => $this->posteable->id_user,
                'username' => $this->posteable->username,
                'photo' => $this->posteable->photo
                    ? (preg_match('/^https?:\/\//', $this->posteable->photo)
                        ? $this->posteable->photo
                        : base64_encode($this->posteable->photo))
                    : null,
            ],
        ];
    }
}

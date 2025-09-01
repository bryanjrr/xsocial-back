<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{

    public function toArray($request)
    {
        $imageContentTypes = ['photo'];

        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'media_debug' => $this->mediaPosts,
            'media' => (function () use ($imageContentTypes) {
                $media = $this->mediaPosts[0] ?? null;
                if (!$media) return null;
                $contentTypeName = $media->contentType?->name ?? null;
                $fileUrl = $media->file_url;
                if ($contentTypeName && in_array($contentTypeName, $imageContentTypes)) {
                    $fileUrl = base64_encode($fileUrl);
                }
                return [
                    'file_url' => $fileUrl,
                    'media_type' => $media->media_type ?? null,
                    'content_type' => $contentTypeName,
                ];
            })(),
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

<?php

namespace Modules\User\Transformers\V1\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoggedInProfileResource extends JsonResource
{
    public function __construct($resource, private $accessToken)
    {
        parent::__construct($resource);
        $this->accessToken = $accessToken;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'full_name' => $this->full_name,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'display_img' => $this->display_img,
            'cover_img' => $this->cover_img,
            'level' => $this->level,
            'access_token' => $this->accessToken,
        ];
    }
}

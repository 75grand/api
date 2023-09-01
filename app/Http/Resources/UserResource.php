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
        $sameUser = $request->user()->id === $this->id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'position' => $this->position,
            'class_year' => $this->class_year,
            'moodle_enabled' => $this->moodle_url !== null,
            $this->mergeWhen($sameUser, [
                'referral_code' => $this->referral_code,
                'referrals_count' => $this->whenCounted('referrals',
                    fn($count) => $count - $this->referrals_redeemed),
                'referrals_per_prize' => 5,
                'created_at' => $this->created_at
            ])
        ];
    }
}

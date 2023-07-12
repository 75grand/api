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
            'avatar' => $this->avatar,
            $this->mergeWhen($sameUser, [
                'referral_code' => $this->referral_code,
                'referrals_count' => $this->whenCounted('referrals'),
                'macpass_number' => $this->macpass_number,
                'class_year' => $this->class_year,
                'position' => $this->position,
                'mailbox_combination' => $this->mailbox_combination,
                'mailbox_number' => $this->mailbox_number,
                'created_at' => $this->created_at
            ])
        ];
    }
}

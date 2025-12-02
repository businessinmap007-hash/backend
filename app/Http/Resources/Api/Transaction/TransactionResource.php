<?php

namespace App\Http\Resources\Api\Transaction;

use App\Http\Resources\UserBasicResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => 'deposit',
            'operation' => $this->operation,
            'price' => $this->price,
            'user' => UserBasicResource::make($this->user),
            $this->mergeWhen($this->target_id != null, [
                'targetUser' => UserBasicResource::make(User::whereId($this->target_id)->first()),
            ]),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'notes' => $this->notes

        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDemoStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('demos.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:new,contacted,demo_scheduled,demo_done,converted,lost',
            'admin_notes' => 'nullable|string|max:5000',
        ];
    }
}

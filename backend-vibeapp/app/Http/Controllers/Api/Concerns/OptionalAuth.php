<?php

namespace App\Http\Controllers\Api\Concerns;

trait OptionalAuth
{
    /**
     * Resolve user dari Bearer token kalau ada, return null kalau guest.
     * Aman dipanggil di route tanpa auth:sanctum middleware.
     */
    protected function resolveOptionalUser()
    {
        return auth('sanctum')->user();
    }

    protected function isGuest(): bool
    {
        return $this->resolveOptionalUser() === null;
    }
}
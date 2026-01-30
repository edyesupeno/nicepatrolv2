<?php

namespace App\Helpers;

class AuthHelper
{
    private static $cachedUser = null;
    private static $cachedUserId = null;
    private static $cachedPerusahaanId = null;

    /**
     * Get current user ID without loading full user model
     */
    public static function id(): ?int
    {
        if (self::$cachedUserId === null) {
            self::$cachedUserId = auth()->id();
        }
        return self::$cachedUserId;
    }

    /**
     * Get current user's perusahaan_id without loading full user model
     */
    public static function perusahaanId(): ?int
    {
        if (self::$cachedPerusahaanId === null && auth()->check()) {
            // Only select the columns we need for global scopes
            $user = auth()->user();
            if ($user) {
                self::$cachedPerusahaanId = $user->perusahaan_id;
            }
        }
        return self::$cachedPerusahaanId;
    }

    /**
     * Check if user is authenticated without loading full model
     */
    public static function check(): bool
    {
        return auth()->check();
    }

    /**
     * Clear cached values (useful for testing or when user changes)
     */
    public static function clearCache(): void
    {
        self::$cachedUser = null;
        self::$cachedUserId = null;
        self::$cachedPerusahaanId = null;
    }
}
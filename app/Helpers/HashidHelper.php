<?php

namespace App\Helpers;

use Hashids\Hashids;

class HashidHelper
{
    private static $hashids;
    
    /**
     * Get Hashids instance
     */
    private static function getHashids()
    {
        if (!self::$hashids) {
            self::$hashids = new Hashids(config('app.key'), 8);
        }
        return self::$hashids;
    }
    
    /**
     * Encode ID to hash
     */
    public static function encode($id)
    {
        return self::getHashids()->encode($id);
    }
    
    /**
     * Decode hash to ID
     */
    public static function decode($hash)
    {
        $decoded = self::getHashids()->decode($hash);
        return $decoded[0] ?? null;
    }
    
    /**
     * Encode multiple IDs
     */
    public static function encodeMultiple(array $ids)
    {
        return self::getHashids()->encode(...$ids);
    }
    
    /**
     * Decode to multiple IDs
     */
    public static function decodeMultiple($hash)
    {
        return self::getHashids()->decode($hash);
    }
}
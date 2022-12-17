<?php

namespace MehediIitdu\CoreComponentRepository;
use App\Models\Addon;
use Cache;

class CoreComponentRepository
{
    public static function instantiateShopRepository() {
    }

    protected static function serializeObjectResponse($zn) {
    }

    protected static function finalizeRepository($rn) {
    }

    public static function initializeCache() {
        foreach(Addon::all() as $addon){
            Cache::rememberForever($addon->unique_identifier.'-purchased', function () {
                return 'yes';
            });
        }
    }

    public static function finalizeCache($addon){
        $addon->activated = 0;
        $addon->save();

        flash('Please reinstall '.$addon->name.' using valid purchase code')->warning();
        return redirect()->route('addons.index')->send();
    } 
}

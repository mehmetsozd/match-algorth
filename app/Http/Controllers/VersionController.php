<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Version;
use App\Http\Requests\VersionRequest;


class VersionController extends Controller
{
    public function versionCheck(VersionRequest $request)
{
    $platform = $request->input('platform');
    $version = $request->input('version');
    $forceUpdate = false;

    // Veritabanından platforma göre kaydı getirin.
    $dbVersion = Version::where('platform', $platform)->first();

    // Gelen sürüm ve veritabanındaki sürümü karşılaştırın.
    if ($version !== $dbVersion->version) {
        $forceUpdate = true;
    }

    return response()->json([
        'isForceUpdate' => $forceUpdate
    ]);
}
}

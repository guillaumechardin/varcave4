<?php

namespace App\Http\Controllers;

use App\Models\CaveChangelog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;


class CaveChangelogController extends Controller
{
    public function show(Request $request): View
    {
        $chgLogs = [];
        $nbr = 100;
        $chgLogs = CaveChangelog::lastestCaveChangeLog($nbr, onlyHomepageVisible: 0);

        return view('varcave.changelogs.index', [
            'pageTitle' => __('varcave.change_logs.page_title'),
            'chgLogs' => $chgLogs,
            'nbr' => $nbr,

        ]);
    }
}

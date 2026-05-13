<?php

namespace Modules\Auth\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController
{
    public function __invoke(Request $request): View
    {
        return view('dashboard.index', [
            'user' => $request->user()->loadMissing('role', 'organization'),
        ]);
    }
}

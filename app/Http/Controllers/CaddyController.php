<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaddyController extends Controller
{
    public function check(Request $request)
    {
        $authorizedDomains = [
            'filament.test',
            'www.filament.test',
        ];

        if (in_array($request->query('domain'), $authorizedDomains, true)) {
            return response('Domain Authorized');
        }

        abort(503);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\SunatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DespatchController extends Controller
{
    public function send(Request $request)
    {

        $data = $request->all();

        $company = Company::where('user_id', auth()->id())
            ->where('ruc', $data['company']['ruc'])
            ->firstOrFail();

        $sunat = new SunatService();

        $despatch = $sunat->getDespatch($data);

        $api = $sunat->getSeeApi($company);
        $result = $api->send($despatch);


        $ticket = $result->getTicket();

        if ($api->getStatus($ticket)) {
            return "Enviado Correctamente";
        }else{
            return "Error";
        }
    }

    public function xml()
    {
    }

    public function pdf()
    {
    }
}

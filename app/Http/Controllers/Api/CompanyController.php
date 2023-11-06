<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $companies = Company::where('user_id', auth()->id())->get();

       return response()->json($companies, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'razon_social' =>'required|string|max:255',
            'ruc' =>[
                        'required',
                        'string',
                        'regex:/^(10|20)\d{9}$/',
                    'unique:companies,ruc'
                   // new \App\Rules\UniqueRucRule(JWTAuth::user()->id)

            ],
            'direccion' =>'required|string',
            'logo' =>'nullable|image',
            'sol_user' =>'required|string',
            'sol_pass' =>'required|string',
            //extension .pem
            'cert' =>'required|File|mimes:pem,txt',
            'cliente_id' =>'nullable|string',
            'client_secret' =>'nullable|string',
            'production' =>'nullable|boolean',

        ]);

        if ($request->hasFile('logo')){
            $data['logo_path'] = $request->file('logo')->store('logos');
        }

        $data['cert_path'] = $request->file('cert')->store('certs');
        $data['user_id'] = JWTAuth::user()->id;

        $company = Company::create($data);

        return response()->json([
            'message' =>'Empresa creada correctamente',
            'company' => $company
        ], 201);

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show( $company)
    {
            $company = Company::where('ruc', $company)
                                ->where('user_id', auth()->user()->id)
                                ->firstOrfail();

                    return response()->json($company, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $company)
    {

        $company = Company::where('ruc', $company)
                            ->where('user_id', auth()->user()->id)
                            ->firstOrfail();


        $data = $request->validate([
            'razon_social' =>'nullable|string',
            'ruc' =>[
                        'nullable',
                        'string',
                        'regex:/^(10|20)\d{9}$/',
                        //'unique:companies,ruc'
                   // new \App\Rules\UniqueRucRule(JWTAuth::user()->id)

            ],
            'direccion' =>'nullable|string|min:5',
            'logo' =>'nullable|image',
            'sol_user' =>'nullable|string',
            'sol_pass' =>'nullable|string',
            //extension .pem
            'cert' =>'nullable|File|mimes:pem,txt',
            'cliente_id' =>'nullable|string',
            'client_secret' =>'nullable|string',
            'production' =>'nullable|boolean',

        ]);


        if ($request->hasFile('logo')){
            $data['logo_path'] = $request->file('logo')->store('logos');
        }
        if ($request->hasFile('cert')) {
            $data['cert_path'] = $request->file('cert')->store('certs');
        }

        $company->update($data);

        return response()->json([
            'message' =>'Empresa actualizada',
            'company'  => $company

        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy( $company)
    {
        $company = Company::where('ruc', $company)
        ->where('user_id', auth()->user()->id)
        ->firstOrfail();

        $company->delete();


        return response()->json([
            'message' =>'Empresa Eliminada',
            'company'  => $company

        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushHooksToAcademyController extends Controller
{
    public function __invoke(Request $request)
    {
        $rules = [
            'event' => 'required',
            'contactId' => 'required|numeric',
            'email' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            activity('infusionsoft-hook-failed')
                ->withProperties([
                    'contactID' => $request->get('contactId'),
                ])
                ->log('User with Infusionsoft ID <strong>:properties.contactID</strong> hook failed.');

            \Log::warning('InfusionSoft hook failed to proceed');
            return;
        }

        $data = $request->all();

        $curl = curl_init();

        curl_setopt_array($curl,[
            CURLOPT_URL => env('LMSV2_URL')."/hooks/chfy8356md",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ]
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
    }
}

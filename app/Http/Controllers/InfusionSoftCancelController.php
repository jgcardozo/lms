<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfusionSoftCancelController extends Controller
{
    public function __invoke(Request $request)
    {
        $rules = [
            'contactId' => 'required|numeric',
            'email' => 'required',
            'cancelTag' => 'required'
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            activity('infusionsoft-cancel-failed')
                ->withProperties([
                    'contactID' => $request->get('contactId'),
                    'cancelTag' => $request->get('cancelTag')
                ])
                ->log('User with Infusionsoft ID <strong>:properties.contactID</strong> failed to cancel.');

            \Log::warning('InfusionSoft cancel tag failed to proceed');
            return;
        }

        $data = [
            'event' => 'infusionsoft_cancel_tag',
            'email' => $request->get('email'),
            'contact_id' => $request->get('contact_id'),
            'cancel_tag' => $request->get('cancel_tag')
        ];

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

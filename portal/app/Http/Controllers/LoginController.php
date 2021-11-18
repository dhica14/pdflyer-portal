<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;

class LoginController extends Controller
{
    public function curlRequest($url, $method, $body, $headers)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);


        if (!empty($body)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        $responseJsonText = curl_exec($curl);
        $body = json_decode($responseJsonText, true);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $result = array(
            'status_code' => $httpCode,
            'body' => $body
        );

        return $result;
    }

    public function login()
    {
        $objDateTime = new DateTime();

        return view('login')
            ->with(['message' => $objDateTime->format('Ymd-H_i_s')]);
    }

    public function argoAuth(Request $req)
    {
        $body = array(
            "email" => $req['id'],
            "password" => $req['password'],
        );
        $headers = array();
        $result = $this->curlRequest('https://api.pdflyer.jp/auth', 'POST', $body, $headers);

        if ($result['status_code'] != '200') {
            return view('login')
                ->with(['message' => 'ID or PASSWORDが異なります']);
        } else {
            $result_body = $result['body']['token'];
            $req->session()->regenerate();

            session()->put('token', $result_body);
            session()->put('id', $body['email']);
            return redirect()->action([ProjectController::class, 'project']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function curlRequest($url, $method, $headers)
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

    public function project(Request $req)
    {
        if (!$req->session()->has('token')) {
            return redirect()->action([LogoutController::class, 'logout']);
        }

        $headers = array(
            'Authorization: ' . session()->get('token')
        );
        $result = $this->curlRequest('https://api.pdflyer.jp/smartlinks?__sort%5Bid%5D=asc', 'GET', $headers);
        $smartlink_lists = $result['body']['data'];

        return view('projects.list')
            ->with(['smartlink_lists' => $smartlink_lists, 'smartlink_count' => count($smartlink_lists)]);
    }

    public function projectSelect(Request $req)
    {

        $params = array(
            'projename' => $req['title'],
            'sl_id' => $req['id'],
            'code' => '',
            'message' => '',
            'result' => ''
        );

        return view('projects.query')
            ->with($params);
    }
}

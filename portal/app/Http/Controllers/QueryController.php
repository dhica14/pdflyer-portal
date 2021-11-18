<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;

/**
 * QueryController
 * 統計情報を取得クラス
 */
class QueryController extends Controller
{
    /**
     * curlRequest
     * APIコール
     * @param  string $url　argoAPIを指定
     * @param  string $method　POST固定
     * @param  array $body　Request body
     * @param  array $headers Request header
     * @return void
     */
    public function curlRequest(string $url, string $method, array $body, array $headers)
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

    /**
     * statisticsConfig
     * csvカラム指定、query文制御
     * @param  object $req Request情報
     * @return  array 設定値の配列
     */
    public function statisticsConfig(object $req)
    {

        $columns = [
            'rowNum',
            'fp',
            'userName',
            'firstVisit',
            'lastVisit',
            'timezone',
            'ua',
            'osName',
            'osVersion',
            'deviceModel',
            'deviceType',
            'deviceVendor',
            'cpuArchitecture',
            'browserMajor',
            'browserName',
            'browserVersion',
            'engineName',
            'engineVersion',
            'opened',
            'downloaded',
            'printed',
            'pagesViewed',
            'pageViews',
            'viewsSeconds'
        ];

        $sp1 = " ";
        $query_1 = "select rowNumberInAllBlocks() rowNum, fp, substr( max(concat(toString(dt),user)), 20) userName, min(dt) firstVisit, max(dt) lastVisit, any(timezone) timezone, any(ua) ua, any(os_name) osName, any(os_version) osVersion, any(device_model) deviceModel, any(device_type) deviceType, any(device_vendor) deviceVendor, any(cpu_architecture) cpuArchitecture, any(browser_major) browserMajor, any(browser_name) browserName, any(browser_version) browserVersion, any(engine_name) engineName, any(engine_version) engineVersion, countIf(action, action='ENTER') opened, countIf(action, action in ('DOWNLOAD','DOWNLOAD_VIEW')) downloaded, countIf(action, action='PRINT') printed, uniqExact(page, action='OPEN') pagesViewed, countIf(action, action='OPEN') pageViews,";
        $query_2 = "sumIf(duration, action='PAGE_VIEW') viewsSeconds from wizeflow.tracks where document_id = '" . $req['sl'] . "' AND fp!='' AND user!='anonymous'";
        $query_3 = $sp1 . "group by fp";
        $query_4 = $sp1 . "having firstVisit >= toDate('" . $req['from'] . "') AND firstVisit <= toDate('" . $req['to'] . "')";
        $query_5 = $sp1 . "having firstVisit >= toDate('" . $req['from'] . "')";
        $query_6 = $sp1 . "having firstVisit <= toDate('" . $req['to'] . "')";
        $query_7 = $sp1 . "order by rowNum asc";

        $query = '';

        /**
         * query文作成（各項目の空値によって変更）
         */
        if ($req['sl'] != '' && $req['from'] != '' && $req['to'] != '') {
            $query = $query_1 . $query_2 . $query_3 . $query_4 . $query_7;
        } elseif ($req['sl'] != '' && $req['from'] != '' && $req['to'] == '') {
            $query = $query_1 . $query_2 . $query_3 . $query_5 . $query_7;
        } elseif ($req['sl'] != '' && $req['from'] == '' && $req['to'] != '') {
            $query = $query_1 . $query_2 . $query_3 . $query_6 . $query_7;
        } elseif ($req['sl'] != '') {
            $query = $query_1 . $query_2 . $query_3 . $query_7;
        } else {
            $query = '';
        }

        $configs = array(
            'columns' => $columns,
            'query' => $query
        );

        return $configs;
    }

    /**
     * query
     * query実行
     * @param object $req Request情報
     * @return array 処理結果
     */
    public function query(object $req)
    {

        if (!$req->session()->has('token')) {
            return redirect()->action([LogoutController::class, 'logout']);
        }

        $configs = $this->statisticsConfig($req);
        $query = $configs['query'];
        // $query = "select rowNumberInAllBlocks() rowNum, fp, substr( max(concat(toString(dt),user)), 20) userName, any(email) email, if( email = '', if(userName = 'none', '', userName),if(userName = 'none', email,concat(email, ' (send by ', userName, ')'))) lastUser, min(dt) firstVisit, max(dt) lastVisit, any(timezone) timezone, any(ua) ua, any(os_name) osName, any(os_version) osVersion, any(device_model) deviceModel, any(device_type) deviceType, any(device_vendor) deviceVendor, any(cpu_architecture) cpuArchitecture, any(browser_major) browserMajor, any(browser_name) browserName, any(browser_version) browserVersion, any(engine_name) engineName, any(engine_version) engineVersion, countIf(action, action='ENTER') opened, countIf(action, action in ('DOWNLOAD','DOWNLOAD_VIEW')) downloaded, countIf(action, action='PRINT') printed, uniqExact(page, action='OPEN') pagesViewed, countIf(action, action='OPEN') pageViews, sumIf(duration, action='PAGE_VIEW') viewsSeconds from wizeflow.tracks  where document_id = '" . $req['sl'] . "' AND fp!='' AND user!='anonymous' group by fp order by viewsSeconds desc";
        // 618b7f1d453dcb0019dc9000

        $body = array(
            "query" => $query
        );
        $headers = array(
            'Authorization: ' . session()->get('token')
        );
        $result = $this->curlRequest('https://api.pdflyer.jp/stats/query', 'POST', $body, $headers);

        return $result;
    }

    /**
     * makeHeader
     *
     * @param  string csvファイル名
     * @return array header設定値
     */
    public function makeHeader(string $csv_name)
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $csv_name,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return $headers;
    }

    /**
     * isSuccess
     * APIコールの処理結果判定
     * @param  string $status_code
     * @return boolean true or false
     */
    public function isSuccess(string $status_code)
    {
        if ($status_code != '200') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * export
     *
     * @param  mixed $req Request情報
     * @return void
     */
    public function export(Request $req)
    {
        if (!$req->session()->has('token')) {
            return redirect()->action([LogoutController::class, 'logout']);
        }

        $objDateTime = new DateTime();
        $csv_name = '';

        // 設定ファイルから値取得
        $configs = $this->statisticsConfig($req);
        $columns = $configs['columns'];

        // クエリ処理結果
        $result = $this->query($req);
        $is_status_code = $this->isSuccess($result['status_code']);

        // コールバック処理
        $callback = function () use ($columns, $result, $is_status_code) {

            $createCsvFile = fopen('php://output', 'w');

            // 文字化け対策
            mb_convert_variables('SJIS-win', 'UTF-8', $columns);

            fputcsv($createCsvFile, $columns);

            if ($is_status_code) {

                /**
                 * データを１行ずつ処理する
                 */
                foreach ($result['body']['data'] as $index => $row) {
                    $header_columns = array();
                    for ($i = 0; $i < count($columns); $i++) {
                        array_push($header_columns, $row[$columns[$i]]);
                    }
                    $csv = $header_columns;

                    // 文字化け対策
                    mb_convert_variables('SJIS-win', 'UTF-8', $csv);
                    // ファイルに追記する
                    fputcsv($createCsvFile, $csv);
                }
            } else {
                // var_dumpで処理結果をcsvへ出力
                var_dump($result['status_code']);
                var_dump($result['body']['error']);
            }
            // ファイルクローズ
            fclose($createCsvFile);
        };

        /**
         * csvファイル名を変更（ステータスコードで判定）
         */
        if ($is_status_code) {
            $csv_name = $objDateTime->format('Ymd-H_i_s') . '_' . $req['title'] . '.csv';
            return response()->stream($callback, 200, $this->makeHeader($csv_name));
        } else {
            $csv_name = $objDateTime->format('Ymd-H_i_s') . '_' . 'error.csv';
            return response()->stream($callback, 200, $this->makeHeader($csv_name));
        }
    }

    public function queryTest(Request $req)
    {
        if (!$req->session()->has('token')) {
            return redirect()->action([LogoutController::class, 'logout']);
        }
        $params = array(
            'sl_id' => '',
            'from' => '',
            'to' => '',
            'code' => '',
            'message' => '',
            'result' => ''
        );

        return view('querytest')
            ->with($params);
    }
    public function queryExecute(Request $req)
    {
        if (!$req->session()->has('token')) {
            return redirect()->action([LogoutController::class, 'logout']);
        }

        // クエリ処理結果
        $result = $this->query($req);
        $is_status_code = $this->isSuccess($result['status_code']);

        if ($is_status_code) {
            $params = array(
                'sl_id' => $req['sl'],
                'from' => $req['from'],
                'to' => $req['to'],
                'code' => $result['status_code'],
                'message' => '{status : success, sl:' . $req['sl'] . ', from:' . $req['from'] . ', to:' . $req['to'] . '}',
                'result' => json_encode($result['body']['data'])
            );
            return view('querytest')
                ->with($params);
        } else {
            $params = array(
                'sl_id' => $req['sl'],
                'from' => $req['from'],
                'to' => $req['to'],
                'code' => $result['status_code'],
                'message' => '{status : fail, sl:' . $req['sl'] . ', from:' . $req['from'] . ', to:' . $req['to'] . '}',
                'result' => json_encode($result['body']['error'])
            );
            return view('querytest')
                ->with($params);
        }
    }
}

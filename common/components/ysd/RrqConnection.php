<?php
namespace common\components\ysd;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/ysd/RrqConnection.php $
 * $Id: RrqConnection.php 4245 2020-03-22 02:57:32Z sakai $
 */

use Yii;
use GuzzleHttp\Client;
use GuzzleHttp\RequestException;
use GuzzleHttp\Exception\ConnectException;

class RrqConnection extends \yii\base\Component
{
    const  HTTP_BODY_MAXLEN = 1024;

    /**
     * @var YSD が 農業生産法人豊受自然農株式会社 へ払い出した 会社コード
     */
    public $corpcd = '50803';

    /**
     * @var corpcd に対応するパスワード
     */
    public $password = null;

    /**
     * @var corpcd に対応するサービスコード
     */
    public $svcno    = '004';

    /**
     * @var method to send http request
     */
    public $method = 'POST';

    /**
     * @var url to send http request
     * e.g., 'http://218.40.13.161/webgw_ml/WRP01010Action_doBatInit.action' //テスト環境@ysd
     */
    public $url    = '';

    // @var \common\models\ysd\RegisterRequest, must not be null
    public $model;

    // @var GuzzleHttp\Response or null. If null, it means http connection has not established.
    public $response;

    // @var GuzzleHttp\RequestException or null. If null, it means no http error have occured.
    public $error;

    /**
     * Send http request to the server with specific params
     * if connection has established, then feed http body to the model
     * @return boolean whether got http response or not
     * (see $this->model->feedback to check the response body was correct)
     */
    public function send()
    {
        $param = array_merge($this->model->postData,[
            'CORPCD'   => $this->corpcd,
            'PASSWORD' => $this->password,
            'SVCNO'    => $this->svcno,
        ]);

        if (!defined('CURL_SSLVERSION_TLSv1_2')) define('CURL_SSLVERSION_TLSv1_2', 6);
        $client = new Client([
            'defaults' => [
            //    "verify" => Yii::getAlias('@common/config/cacert.pem')
                'config' => [
                       'curl' => [
                                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
                       ]
                ]

            ],
        ]);

if($param['USERNO'] == '0000019172') {
    $this->url = 'https://web-koufuri.test.data-sec-sv.com/webgw_ml/WRP01010Action_doBatInit.action';
    $param['PASSWORD'] = '4b769f5d';
}



        $request = $client->createRequest($this->method, $this->url, [
            'body' => $param,
        ]);

        try
        {
            $res = $client->send($request);
        }
        catch (ConnectException $e)
        {
            $this->error = $e;
        }
        catch (RequestException $e)
        {
            $this->error = $e;
        }
        if($this->error)
            return false;

        $this->response = $res;

        $body    = $res->getBody();
        $bodyLen = min(self::HTTP_BODY_MAXLEN, $body->getSize());
        $text    = $body->read($bodyLen);

        $this->model->parseResponse($text);

        return (200 == $res->getStatusCode());
    }

}

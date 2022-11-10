<?php

namespace app\textnow\library;

use \Exception;
use GuzzleHttp\Client;

/**
 * Class TextNow
 */
class TextNowSms
{
    /**
     * @var string
     */
    private $cookie;

    /**
     * @var bool
     */
    private $enableProxy = false;

    /**
     * @var string
     */
    private $proxy;

    /**
     * @var string
     */
    private $proxyAccount;

    /**
     * @var string
     */
    protected $proxyPassword;
    /**
     *
     */
    protected $account;

    /**
     * @var string
     */
    private $cookieStoragePath = './cookies';

    /**
     * @param string $cookie
     * @param string $proxy
     * @param string $proxyAccount
     * @param string $proxyPassword
     */
    public function __construct($cookie, $proxy = '', $proxyAccount = '', $proxyPassword = '',$account = '')
    {
        $this->cookie = $cookie;
        $this->proxy = $proxy;
        $this->proxyAccount = $proxyAccount;
        $this->proxyPassword = $proxyPassword;
        $this->account = $account;
    }

    /**
     * 发送文本消息
     *
     * @param string $number
     * @param string $text
     * @return mixed
     * @throws Exception
     */
    public function sendText($number, $text)
    {
        //headers
        $origin = "https://www.textnow.com";
        $referer = "https://www.textnow.com/messaging";

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/json',
            'X-Origin: ' . $origin,
            'X-Referer: ' . $referer,
            'X-Requested-With: XMLHttpRequest',
            'Cookie: ' . $this->cookie,
            'Host: www.textnow.com',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
        ];

        //body
        $data = [
            "from_name" => "",
            "has_video" => false,
            "contact_value" => "",
            "contact_type" => 2,
            "message" => "",
            "read" => 1,
            "message_direction" => 2,
            "message_type" => 1,
            "new" => true,
            "date" => ""
        ];

        $data["message"] = $text;
        $number = "+1" . $number;
        $data["contact_value"] = $number;
        $data["date"] = $this->isoDateUTC();

        //网络请求
        $body = http_build_query([
            'json' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);

        $url = 'https://www.textnow.com/api/users/'.$this->account.'/messages';
        return $this->request("post", $url, $headers, $body);
    }

    /**
     * 发送图片消息
     *
     * @param string $number
     * @param string $image
     * @return mixed
     * @throws Exception
     */
    public function sendImage($number, $image)
    {
        //headers
        $origin = "https://www.textnow.com";
        $referer = "https://www.textnow.com/messaging";

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/json',
            'X-Origin: ' . $origin,
            'X-Referer: ' . $referer,
            'X-Requested-With: XMLHttpRequest',
            'Cookie: ' . $this->cookie,
            'Host: www.textnow.com',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
        ];

        //body
        $data = [
            "from_name" => "",
            "has_video" => false,
            "contact_value" => "",
            "contact_type" => 2,
            "read" => 1,
            "message_direction" => 2,
            "message_type" => 2,
            "new" => true,
            "date" => "",
            "attachment_url" => "",
            "media_type" => "images"
        ];

        $imageString = file_get_contents($image);
        $imageUrl = $this->uploadImage($imageString);

        $data["attachment_url"] = $imageUrl;
        $number = "+1" . $number;
        $data["contact_value"] = $number;
        $data["date"] = $this->isoDateUTC();

        //网络请求
        $body = http_build_query([
            'json' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);
        $url = 'https://www.textnow.com/api/v3/send_attachment';

        return $this->request('post', $url, $headers, $body);
    }

    /**
     * 聊天记录图片下载
     *
     * @param $imageUrl
     * @return void
     * @throws Exception
     */
    public function downloadImage($imageUrl)
    {
        //headers
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
        ];

        $imageString = $this->request('get', $imageUrl, $headers);
        $imageInfo = getimagesizefromstring($imageString);
        switch ($imageInfo[2]) {
            case 1:
                $ext = 'gif';
                break;
            case 2:
                $ext = 'jpg';
                break;
            case 3:
                $ext = 'png';
                break;
            case 4:
                $ext = 'swf';
                break;
            case 6:
                $ext = 'bmp';
                break;
            default:
                $ext = '';
        }

        if (!$ext) {
            throw new Exception('图片类型无效');
        }

        $filename = md5($imageString) . '.' . $ext;
        file_put_contents("{$filename}", $imageString);
    }

    /**
     * 上传图片
     *
     * @param string $imageString
     * @return mixed
     * @throws Exception
     */
    protected function uploadImage(&$imageString)
    {
        $imageInfo = getimagesizefromstring($imageString);
        $headers = [
            'Content-Type: ' . $imageInfo['mime'],
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
        ];

        $url = $this->getImagePutUrl();
        $this->request('put', $url, $headers, $imageString);

        return $url;
    }

    /**
     * 获取图片上传链接
     *
     * @return mixed
     * @throws Exception
     */
    protected function getImagePutUrl()
    {
        $headers = [
            'Cookie: ' . $this->cookie,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
        ];

        $url = 'https://www.textnow.com/api/v3/attachment_url?message_type=2';
        $result = $this->request("get", $url, $headers);

        $data = json_decode($result, true);
        if (array_key_exists('error_code', (array)$data) && is_null($data['error_code'])) {
            return $data['result'];
        }

        throw new Exception('请求接口失败');
    }

    /**
     * 获取ISO标准时间
     *
     * @return string
     */
    protected function isoDateUTC()
    {
        $dateTime = new \DateTime();
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        return $dateTime->format("Y-m-d\TH:i:s.u\Z");
    }

    /**
     * @param $cookie
     * @return bool
     */
    protected function checkCookie($cookie)
    {
        $headers = [
            'Cookie: ' . $cookie,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
        ];

        $url = 'https://www.textnow.com/api/v3/attachment_url?message_type=2';

        try {
            $result = $this->request("get", $url, $headers);
        } catch (Exception $e) {

        }


        $data = json_decode($result, true);
        if (array_key_exists('error_code', (array)$data) && is_null($data['error_code'])) {
            return true;
        }

        return false;
    }

    /**
     * 网络请求
     *
     * @param $method
     * @param $url
     * @param array $headers
     * @param null $body
     * @param int $timeout
     * @return bool|string
     * @throws Exception
     */
    protected function request($method, $url, array $headers = [], $body = null, $timeout = 30)
    {
        //初始化
        $ch = curl_init();    // 启动一个CURL会话
        curl_setopt($ch, CURLOPT_URL, $url);     // 要访问的地址
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 对认证证书来源的检查   // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        //curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);     // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     // 获取的信息以文件流的形式返回

        //proxy
        if ($this->proxy) {
            //$proxyInfo = parse_url($this->proxy);
            //dump($proxyInfo);die;
//            if ($this->proxyAccount) {
//                $proxyHost = "http://".$proxyInfo['host'].':'.$proxyInfo['port'];
//            } else {
//                $proxyHost = "http://".$proxyInfo['host'].':'.$proxyInfo['port'];
//            }
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            curl_setopt($ch,CURLOPT_PROXYUSERPWD, "{$this->proxyAccount}:{$this->proxyPassword}");
        }

        //method
        $method = strtoupper($method);
        switch ($method) {
            case "GET" :
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                break;
            case "PUT" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                break;
        }

        //headers
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //模拟的header头
        }

        //request
        $result = curl_exec($ch);

        //response
        $errno = curl_errno($ch);
        if ($errno !== 0) {
            throw new Exception('网络异常');
        }

        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        if ($curlInfo['http_code'] == 200) {
            return $result;
        }
        dump($result);
        die;

        throw new Exception('请求接口失败');
    }
}
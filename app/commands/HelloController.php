<?php
namespace app\commands;
use GuzzleHttp\Client;
use yii\console\ExitCode;
use yii\console\Controller;
/**
 * HelloController
 */
class HelloController extends Controller
{
    /**
     * @param string $message
     * @return int
     */
    public function actionIndex($message = 'hello world'): int
    {
        return ExitCode::OK;
    }

    public function actionRun(){
        $start = microtime(true);
        try{
            $client = new Client(['verify'=>false,'timeout'=>20.0]);
            $response = $client->request('GET','https://github.com',['proxy'=>'http://nlvrgdjw:1v0sfxs7s2xu@66.43.6.123:7994']);
            $response->getStatusCode();
            $close = microtime(true);
            dump($close-$start);
        }catch(\Throwable $e){
            dump($e->getMessage());
        }
    }
}

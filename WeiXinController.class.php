<?php

namespace Home\Controller;

use Think\Controller;

use Gaoming13\WechatPhpSdk\Wechat;

class WeiXinController extends Controller
{

	protected static $token;
	protected $cacheFile = './token.sql';
    protected $cacheTime = 7200;
	protected $openid;
	protected $wechatid;
	protected $appid     = 'wxb8b4bea929abbe7a';
	protected $appsecret = 'cc473a3d2b468589f0e4b11acfabd473';

	public function index()
	{
		// $wechat = new Wechat(array(
		// 		'appId' => 'wx8440d5d993192c13',
		// 		'token' => 'jinkuang'
		// 	));

		// $msg_obj = $wechat->serve();
		// // var_dump($wechat);
		// if($msg_obj->Content == 'hello'){
		// 	$wechat->reply('你好');
		// }else{
		// 	$wechat->reply('对不起，我不知道你说什么？sdfasdfasd');
		// }
		//接收微信服务器发送过来的信息
		$postStr=file_get_contents('php://input');
		file_put_contents('./22.txt',json_decode($postStr));
		//把xml转化为对象
		$postObj=simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);

		$content=$postObj->Content;
		$this->openid=$postObj->FromUserName;

		$this->wechatid=$postObj->ToUserName;
		$msgType=$postObj->MsgType;

		switch ($msgType) {
			case 'text':
				switch ($content) {
					case 'hello':
						$this->responseText('你好');
						break;
					case '你好':
						$this->responseText('hello');
						break;
					case '图文':
						$data=array(
							array(
								'Title'      => '微信开发简单入门',
								'Description'=> '拥有专业微信开发团队',
								'PicUrl'     => 'https://imgcache.qq.com/open_proj/proj_qcloud_v2/gateway/portal/css/img/home/solution/video/1.png',
								'Url'		=> 'http://www.baidu.com'
								),
							array(
								'Title'      => '很好玩喽',
								'Description'=> '来开黑呀，很不错呀',
								'PicUrl'     => 'https://mc.qcloudimg.com/static/img/b3ca24fb2f6420a34d4d633e517bd7e2/image.png',
								'Url'		=> 'http://www.baidu.com'
								),
							array(
								'Title'      => '微信开发简单入门',
								'Description'=> '拥有专业微信开发团队',
								'PicUrl'     => 'https://imgcache.qq.com/open_proj/proj_qcloud_v2/gateway/portal/css/img/home/solution/video/1.png',
								'Url'		=> 'http://www.baidu.com'
								)
							);
						$this->responsePicText($data);
						break;
					case '图片':
							
						break;
					default:
						$this->responseText('对不起，我不知道你说什么？');
						break;
				}
				break;
			case '':
				#code....
				break;
			case '':
				#code....
				break;
			default:
				$this->responseText('不要乱搞喽...');
				break;
		}
	}


	//回复文本
	protected function responseText($data='')
	{
			$textTpl="<xml>
				<ToUserName><![CDATA[".$this->openid."]]></ToUserName>
				<FromUserName><![CDATA[".$this->wechatid."]]></FromUserName>
				<CreateTime>".time()."</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[".$data."]]></Content>
				</xml>";
			echo $textTpl;
	}

	
	//图文回复
	protected function responsePicText($data)
	{
		$picTextTpl="<xml>
			<ToUserName><![CDATA[".$this->openid."]]></ToUserName>
			<FromUserName><![CDATA[".$this->wechatid."]]></FromUserName>
			<CreateTime>".time()."</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>".count($data)."</ArticleCount>
			<Articles>";
		foreach ($data as $key => $value) {	
			$picTextTpl.="<item>
				<Title><![CDATA[".$value['Title']."]]></Title> 
				<Description><![CDATA[".$value['Description']."]]></Description>
				<PicUrl><![CDATA[".$value['PicUrl']."]]></PicUrl>
				<Url><![CDATA[".$value['Url']."]]></Url>
				</item>";
		}
		$picTextTpl.="</Articles></xml>";
		echo $picTextTpl;
	}

	/**
     * 使用cURL模拟get请求
     * @param 请求的地址
     * @param 请求头(将apikey设置)
     * @return [json] 返回的结果
     */
	public static function getRequest($url,$data_type='text')
	{
        $cl = curl_init();
        if(stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($data_type == 'json') {
                $content = json_decode($content);
            }
            return $content;
        } else {
            return FALSE;
        }        
    }
	// public static function getRequest($url,$data=array())
	// {
	// 	$curl=curl_init($url);

	// 	curl_setopt($curl,CURLOPT_HTTPHEADER,$data);

	// 	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	// 	return curl_exec($curl);
	// }

	/**
     * 使用cURL模拟post请求
     * @param [string] $url [请求的地址(必须是绝对路径)]
     * @param [array]  $data [提交的键值对信息]
     * @return [json] 返回的结果
     */
    public static function postRequest($url ,$fields, $data_type='text')
    {
        $cl = curl_init();
        if(stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($cl, CURLOPT_POST, true);
        // convert @ prefixed file names to CurlFile class
        // since @ prefix is deprecated as of PHP 5.6
        if (class_exists('\CURLFile')) {
            foreach ($fields as $k => $v) {
                if (strpos($v, '@') === 0) {
                    $v = ltrim($v, '@');
                    $fields[$k] = new \CURLFile($v);
                }
            }
        }
        curl_setopt($cl, CURLOPT_POSTFIELDS, $fields);
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($data_type == 'json') {
                $content = json_decode($content);
            }
            return $content;
        } else {
            return FALSE;
        }
        
    }
    // {
    //     // 1.初始化cURL
    //     $curl = curl_init( $url );
    //     // 2.获取的信息以字符串返回，而不是直接输出
    //     curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    //     // 3.声明为POST请求
    //     curl_setopt($curl , CURLOPT_POST , 1);
    //     // 4.设置请求体
    //     curl_setopt($curl , CURLOPT_POSTFIELDS , $data );
    //     // 5.发起请求
    //     return curl_exec($curl);
    // }

    protected function getAccessToken()
    {
    	$getRequestUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
    	if(file_exists($this->cacheFile) && time() - filemtime($this->cacheFile) < $this->cacheTime){
    		self::$token=file_get_contents($this->cacheFile);
    	}else{
    		$urlArr=json_decode(self::getRequest($getRequestUrl),ture);
    		self::$token=$urlArr['access_token'];
    		file_put_contents($this->cacheFile, self::$token);
    	}

    	return self::$token;

    }

    //添加素材
    public function addMaterial()
    {
    	$formPostUrl="https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=".$this->getAccessToken().'&type=image';
    	//获取图片的绝对路径，这张图片必须是在公网的上的图片，也就是服务器上的图片绝对路径
    	$picTath='/var/www/weixin/Public/images/1.png
';
    	$picArr=array('media'=>'@'.$picTath);
    	$result=$this->postRequest($formPostUrl,$picArr,'json');
    	dump($result);
    }

}

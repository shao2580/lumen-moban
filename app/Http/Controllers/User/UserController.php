<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserModel;

class UserController extends Controller
{
    /*注册*/
    public function reg(Request $request)
    {
        $name= $request->input('name');
        $pass= $request->input('pass');
        $pass1= $request->input('pass1');
        
        /*注册逻辑*/
        if ($pass!==$pass1) {
            echo '两次密码不一致，请重新输入';
        }else{
            $res = UserModel::insert(['u_name'=>$name,'u_pass'=>$pass]);
            if ($res) {
                echo '注册成功';
            }
        }
    }	

    /*登录*/
    public function login(Request $request)
    {
        $name= $request->input('name');
        $pass= $request->input('pass');
        /*登录逻辑*/
        $res = UserModel::where(['u_name'=>$name,'u_pass'=>$pass])->first();
        if ($res) {
            echo '登陆成功';
        }else{
            echo '账号或密码错误，请重新输入！';
        }
    }

    /*修改密码*/
    public function update(Request $request)
    {
        $name= $request->input('name');
        $pass = $request->input('pass');
        if (!empty($name) && !empty($pass)) {
            $res =UserModel::where(['u_name'=>$name,'u_pass'=>$pass])->first();
            if ($res) {
                echo '没有改动';
            }
        }
        $res = UserModel::where(['u_name'=>$name])->update(['u_pass'=>$pass]);
        if ($res) {
            echo '修改成功';
        }

    }

    /*调用天气接口*/
  public function weather(Request $request){
        $city=$request->input('city');
        // dd($city);
        $url = "http://api.k780.com/?app=weather.future&weaid={$city}&appkey=42256&sign=cfd54d0fd3a6f403990d0446a23818dd&format=json";
        //调用接口
        // dd($url);
        $weathData = file_get_contents($url);
        //转成数组  不加true 成对象
        
        $weathData = json_decode($weathData,true);
        
        if($weathData['success']==0){
            echo '请输入要查询天气的城市';
        }else{
            $msg = '';
            foreach ($weathData['result'] as $key => $value) {
                $msg .= $value['days']." ".$value['citynm']." ".$value['week']." ".$value['weather']." ".$value['temperature']."\n";

            }
            return $msg;  
        }    
    }
     
     /*解密*/
    public function decrypt()
    {
        $data = file_get_contents('php://input');
        echo($data);

        //解密
        $data = base64_decode($data);
        dd($data);
    }

    /*对称解密*/
    public function decrypt1()
    {
        $data = file_get_contents('php://input');
       
        echo($data);echo '<hr/>';

        $method = 'AES-128-CBC';       //密码方式
        $key = 'password';             //密码
            //  OPENSSL_RAW_DATA       //ssl原始数据  或  OPENSSL_ZERO_PADDING 填充 0
        $iv = 'qazwsxedcrfvtgby'; 
        //解密
        $dec_data = openssl_decrypt($data,$method,$key,OPENSSL_RAW_DATA,$iv);
        echo($dec_data);echo '<hr/>';
        $dec_data = base64_decode($dec_data);
        echo($dec_data);
    }
    
    /*非对称解密   --- 公钥解密*/
    public function decrsa()
    {
        $data = file_get_contents('php://input');
        var_dump($data);echo '<hr/>';

        //公钥路径
        $dec_path = storage_path('keys/pub.key');
        $dec_key = openssl_get_publickey('file://'.$dec_path);
        //公钥解密
        openssl_public_decrypt($data,$dec_data,$dec_key);

        var_dump($dec_data);
    }

    /*非对称解密 签名*/
    public function decsign()
    {
        $data = $_POST;

        $sign = base64_decode($data['sign']);
        // var_dump($sign);
        unset($data['sign']);

        // echo '服务器接收端：'.dump($sign);echo '<hr/>';

        $str0 = '';
        foreach ($data as $k => $v) {
            $str0 .=$k.'='.$v.'&';
        }
        $str = rtrim($str0,'&');
        // dd($str);
        /*直接 公钥解密-- 验签*/
        $dec_key = storage_path('keys/pub.key');
        $res = openssl_verify($str,$sign,openssl_get_publickey('file://'.$dec_key));
        // dd($res);
        if ($res) {
            echo '成功';
        }else{
            echo '失败';
        }
        

    }

}//最后一行
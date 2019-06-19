<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UsersModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;


class LoginController extends Controller
{
    /*注册*/
    public function reg(Request $request)
    {
      // header("Access-Control-Allow-Origin:*");
        $name= $request->input('name');
        $password= $request->input('password');
        $pass= $request->input('pass');
        $email= $request->input('email');

        //判断邮箱唯一
        $res_email = UsersModel::where(['email'=>$email])->first();
        if ($res_email) {
             $returnMsg = [
                    'ret' =>00,
                    'msg'=>'邮箱已注册，请重新输入'
                ];
                return $returnMsg;
        }

        /*注册逻辑*/
        if ($password!==$pass) {
            $returnMsg = [
                    'ret' =>00,
                    'msg'=>'两次密码不一致，请重新输入'
                ];
                return $returnMsg;
        }else{
            //密码哈希处理
            $password = password_hash($password,PASSWORD_BCRYPT);

            $data = [
                'name'=>$name,
                'email'=>$email,
                'password'=>$password
            ];

            $res = UsersModel::insert($data);
            if ($res) {
               $returnMsg = [
                    'ret' =>1,
                    'msg'=>'注册成功'
                ];
                return $returnMsg; 
            }else{
                 $returnMsg = [
                    'ret' =>0,
                    'msg'=>'注册失败'
                ];
                return $returnMsg; 
            }
        }
    }	

    /*登录*/
    public function login(Request $request)
    {
        // header("Access-Control-Allow-Origin:*");
        $name= $request->input('name');
        $password= $request->input('password');

        /*登录逻辑*/
        $res = UsersModel::where(['name'=>$name])->first();
        if ($res) {
            //验证密码
            if (password_verify($password,$res->password)) {
                //登录成功  生成token 
                $token = substr(md5($res->id.Str::random(8).mt_rand(11,9999)),10,10);
                $redis_key = 'user-token:'.$res->id.'';
                // dd($redis_key);
                 Redis::set($redis_key,$token);
            
                $returnMsg = [
                    'ret' =>1,
                    'msg'=>'登陆成功',
                    'data'=>[
                        'token'=>$token,
                        'id'=>$res->id
                    ]
                ];
                return $returnMsg; 

            }else{
                $returnMsg = [
                    'ret' =>00,
                    'msg'=>'账号或密码错误，请重新输入！'
                ];
                return $returnMsg; 
            }
             
        }else{
              $returnMsg = [
                    'ret' =>0,
                    'msg'=>'用户不存在，请重新输入！'
                ];
                return $returnMsg; 
        }
    }

    /*个人中心*/
    public function center(Request $request)
    {
        $id = $request->input('id');
        //验证token
        $token = $request->input('token');

        // 获取服务器中token
        $redis_key = 'user-token:'.$id;
        $cache_token = Redis::get($redis_key);
        if ($cache_token != $token) {
            $returnMsg = [
                'ret' =>40001,
                'msg'=>'token 验证失败'
            ];
            return $returnMsg;
        }

        
        $res = UsersModel::where(['id'=>$id])->first();
        if ($res) {
            $returnMsg = [
                    'ret' =>0,
                    'msg' =>'ok',
                    'data'=>[
                        'res'=>$res
                    ]
             ];              
        }else{
            $returnMsg = [
                'ret' =>00,
                'msg'=>'无法获取用户信息'
            ];
        }
        return $returnMsg; 
    }
  

}//最后一行
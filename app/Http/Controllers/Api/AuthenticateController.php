<?php

namespace App\Http\Controllers\App;

use App\Core\Library\JWT;
use App\Models\User;
use App\Http\Controllers\Controller;
use Psr\Log\InvalidArgumentException;


class AuthenticateController extends Controller
{

    public function __construct()
    {
        $this->class = new User();
        parent::__construct();
    }

    public function getList()
    {
        try {

            if(!isset($this->params['token']))
                throw new \Exception(__('translation.Parametros invalidos'));

            $user = JWT::decode($this->params['token']);

            $data = [];

            if (isset($user->sub) && !empty($user->sub)) {
                $rsLogin = $this->class->loginById($user->sub);

                $data['user'] = $rsLogin;
                $data['token'] = JWT::encode($rsLogin);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => __('translation' . $this->class->getMessage($e->getMessage()))], 400);
        }
    }

    public function postCreate()
    {
        try {

            $rsLogin = $this->class->login($this->params['login'], $this->params['senha']);

            if (!$rsLogin)
                throw new  InvalidArgumentException(__('translation.Login e senha invalido!'));

            $data = [];

            $data['user'] = $rsLogin;
            $data['token'] = JWT::encode($rsLogin);

            return response()->json($data);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => __('translation.Erro interno no servidor')], 400);
        }
    }
}

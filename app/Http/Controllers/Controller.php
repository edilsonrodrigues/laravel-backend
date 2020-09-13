<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Core\Library\JWT;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Class dos models
     */
    protected $class;

    /**
     * Class para validacao das requisições
     */
    protected $classRequest;

    /**
     * Class de tradução
     */
    private $classTranslation;

    /**
     * Parametros passado para o serviço
     */
    protected $params = [];

    protected $redisKey;

    /**
     * Configuracao inicial
     */
    public function __construct()
    {
        $this->params = request()->all();
    }

    /**
     * Listagem dos dados
     * @middleware is-logged
     */
    public function getList()
    {
        try {
            $r = $this->class->getList($this->params);
            $r = $this->formatsData($r);
            return response()->json(['data' => $r], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => __('translation' . $this->class->getMessage($e->getMessage()))], 400);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Cria registro
     * @middleware is-logged
     */
    public function postCreate()
    {
    }
    /**
     * Atualizar registro
     * @middleware is-logged
     */
    public function putUpdate()
    {
    }
    /**
     * Deleta os registros
     * @middleware is-logged
     */
    public function deleteRemove()
    {
        try {
            $rs = $this->class->destroy($this->params['id']);

            if ($rs != 1)
                throw new Exception(__('translation.Nenhum registro deletado'));

            return response()->json(['data' => $this->params['id']]);
        } catch (QueryException $e) {
            return response()->json(['message' => __('translation' . $this->class->getMessage($e->getMessage()))], 400);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Retorna o usuario logado pela api
     * @return mixed
     */
    protected function getUserLoggedId()
    {
        $request = new Request();
        $headerAuth = $request->bearerToken();

        if (!isset($headerAuth))
            return 1;


        $rsJWT = JWT::decode($headerAuth, csrf_token());
        return $rsJWT->sub;
    }


    /**
     * @param $id
     * @return mixed
     */
    public function rowReturn($id)
    {
        return $this->class->findRow($id);
    }

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     */
    public function validate(
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        $validator = Validator::make(request()->all(), $rules, $messages);
        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $item) {
                if (is_array($item)) {
                    foreach ($item as $i) {
                        throw new Exception($i);
                    }
                }
            }
        }
    }

    protected function formatsData($data)
    {
        return $data;
    }
}

<?php

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

use function Hyperf\Support\env;

use Ramsey\Uuid\Uuid;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ControllerJWT
{
	#[Inject]
	protected RequestInterface $request;

	#[Inject]
	protected ResponseInterface $response;

	protected $jwt_key;

	public function __construct()
	{
		$this->jwt_key=env('JWT_KEY', 'key');
	}

	public function generate()
	{
		$payload=[
			'uuid'=>Uuid::uuid4()->toString(),
			'token'=>sha1(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz')),
		];
		$token=JWT::encode($payload, $this->jwt_key, 'HS256');
		return [
			'payload'=>$payload,
			'token'=>$token,
		];
	}

	public function decode()
	{
		$token=$this->request->getHeader('Authorization')[0] ?? '';
		$token=str_replace('Bearer ', '', $token);
		try {
			$decode=JWT::decode($token, new Key($this->jwt_key, 'HS256'));
		} catch (\Exception $e){
			return $this->response->withStatus(401)->json(['token'=>'invalid']);
		}
		return [
			'token'=>$token,
			'decode'=>$decode,
		];
	}

}

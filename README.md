# PHP HyperF -> Firebase JWT

- PHP: 8.3.7
- PHP HyperF: 3.1.23
- Tecnology: JWT - JSON Web Tokens

![Image](_img/post.jpg)

## HyperF - Project

Simple system to validate JWT tokens and ensure authenticity and integrity in authentication processes.

#### Create - Project

```console
composer create-project hyperf/hyperf-skeleton "project"
```

#### Install - Watcher

```console
composer require hyperf/watcher --dev
```

#### Install - Firebase JWT

```console
composer require firebase/php-jwt
```

#### Server - Start

```console
cd project ;
php bin/hyperf.php server:watch ;
```

## HyperF - APP

#### APP - Environment

```bash
JWT_KEY="***"
```

> path: /project/.env

#### APP - Router

```php
Router::addRoute(['GET', 'POST'], '/generate', 'App\Controller\ControllerJWT@generate');
Router::addRoute(['GET', 'POST'], '/decode', 'App\Controller\ControllerJWT@decode');
```

> path: /project/config/routes.php

#### APP - Controller

```php
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
		$this->jwt_key=env('JWT_KEY', '***');
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
```

> path: /project/app/Controller/ControllerJWT.php

## Execute

#### GET - Generate Token

```console
curl "http://127.0.0.1:9501/generate"

Response:
{
	"payload": {
		"uuid": "...0123",
		"token": "***"
	},
	"token": "***"
}
```

#### GET - Decode Token

```console
curl "http://127.0.0.1:9501/decode" -H "Authorization: Bearer %token%"

Response:
{
	"token": "***",
	"decode": {
		"uuid": "...0123",
		"token": "***"
	}
}
```

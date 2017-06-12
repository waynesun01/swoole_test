<?php

/**
 * 解决跨域问题公共类
 * @encoding UTF-8
 * @author WayneSun
 * @datetime 2017-6-8 10:49:34
 * require(__DIR__.'/../Cors.php');
 * $cors=new Cors();
 * //自定义cors
 * $cors->run();

 */
class Cors {

	/**
	 * CORS请求headers信息
	 * @var array
	 */
	public $cors = [
		'Origin' => ['*'],
		'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
		'Access-Control-Request-Headers' => ['*'],
		'Access-Control-Allow-Credentials' => null,
		'Access-Control-Max-Age' => 86400,
		'Access-Control-Expose-Headers' => [],
	];

	public function run() {
		$requestCorsHeaders = $this->extractHeaders();
		\Yii::warning(['requestCorsHeaders' => $requestCorsHeaders]);
		$responseCorsHeaders = $this->prepareHeaders($requestCorsHeaders);
		\Yii::warning(['responseCorsHeaders' => $responseCorsHeaders]);
		$this->addCorsHeaders($responseCorsHeaders);
	}
	public function getHeaders() {
		$headers = [];
		if (function_exists('getallheaders')) {
			$headers = getallheaders();
		} else {
			foreach ($_SERVER as $name => $value) {
				if (substr($name, 0, 5) == 'HTTP_') {
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
		}
		return $headers;
	}

	public function extractHeaders() {
		$headers = [];
		$requestHeaders = array_keys($this->cors);
		foreach ($requestHeaders as $headerField) {
			$serverField = $this->headerizeToPhp($headerField);
			$headerData = isset($_SERVER[$serverField]) ? $_SERVER[$serverField] : null;
			if ($headerData !== null) {
				$headers[$headerField] = $headerData;
			}
		}
		return $headers;
	}

	public function prepareHeaders($requestHeaders) {
		$responseHeaders = [];
		if (isset($requestHeaders['Origin'], $this->cors['Origin'])) {
			if (in_array('*', $this->cors['Origin']) || in_array($requestHeaders['Origin'], $this->cors['Origin'])) {
				$responseHeaders['Access-Control-Allow-Origin'] = $requestHeaders['Origin'];
			}
		}

		if (isset($requestHeaders['Access-Control-Request-Headers'], $this->cors['Access-Control-Request-Headers'])) {
			if (in_array('*', $this->cors['Access-Control-Request-Headers'])) {
				$responseHeaders['Access-Control-Allow-Headers'] = $this->headerize($requestHeaders['Access-Control-Request-Headers']);
			} else {
				$requestedData = preg_split("/[\\s,]+/", $requestHeaders['Access-Control-Request-Headers'], -1, PREG_SPLIT_NO_EMPTY);
				$acceptedData = array_uintersect($requestedData, $this->cors['Access-Control-Request-Headers'], 'strcasecmp');
				if (!empty($acceptedData)) {
					$responseHeaders['Access-Control-Allow-Headers'] = implode(', ', $acceptedData);
				}
			}
		}

		if (isset($requestHeaders['Access-Control-Request-Method'])) {
			$responseHeaders['Access-Control-Allow-Methods'] = implode(', ', $this->cors['Access-Control-Request-Method']);
		}

		if (isset($this->cors['Access-Control-Allow-Credentials'])) {
			$responseHeaders['Access-Control-Allow-Credentials'] = $this->cors['Access-Control-Allow-Credentials'] ? 'true' : 'false';
		}


		if (isset($this->cors['Access-Control-Max-Age']) && $this->getMethod() === 'OPTIONS') {
			$responseHeaders['Access-Control-Max-Age'] = $this->cors['Access-Control-Max-Age'];
		}

		if (isset($this->cors['Access-Control-Expose-Headers'])) {
			$responseHeaders['Access-Control-Expose-Headers'] = implode(', ', $this->cors['Access-Control-Expose-Headers']);
		}

		return $responseHeaders;
	}

	public function addCorsHeaders($headers) {
		if (!empty($headers) && !headers_sent()) {
			foreach ($headers as $name => $values) {
				$name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
				header("$name: $value", $replace);
			}
		}
	}

	protected function headerizeToPhp($string) {
		return 'HTTP_' . strtoupper(str_replace([' ', '-'], ['_', '_'], $string));
	}

	protected function headerize($string) {
		$headers = preg_split("/[\\s,]+/", $string, -1, PREG_SPLIT_NO_EMPTY);
		$headers = array_map(function ($element) {
			return str_replace(' ', '-', ucwords(strtolower(str_replace(['_', '-'], [' ', ' '], $element))));
		}, $headers);
		return implode(', ', $headers);
	}

	protected function getMethod() {
		if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
			return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
		}

		if (isset($_SERVER['REQUEST_METHOD'])) {
			return strtoupper($_SERVER['REQUEST_METHOD']);
		}

		return 'GET';
	}

}

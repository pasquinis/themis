<?php
namespace Themis\Payload;

use ArrayAccess;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestCariparma implements ArrayAccess
{
    private $request;

    public function __construct($request)
    {
        $this->mapRequest($request);
        $this->isWellformed();
    }

    private function mapRequest($request)
    {
        if (count($request) != 7) {
            throw new BadRequestHttpException("Error, see request: " . var_export($request, true));
        }
        $this->request['operationDate'] = $request[0];
        $this->request['valueDate'] = $request[1];
        $this->request['description'] = $request[2];
        $this->request['reason'] = $request[3];
        $this->request['revenue'] = $request[4];
        $this->request['expenditure'] = $request[5];
        $this->request['currency'] = $request[6];
    }

    public static function box(HttpRequest $request)
    {
        $payload = $request->request->all();
        return new self(str_getcsv($payload['data']));
    }

    public function isWellformed()
    {
        preg_match('#[\d]{1,2}/[\d]{1,2}/[\d]{4}#', $this->request['valueDate'], $matches);
        if (empty($matches)) {
            throw new BadRequestHttpException("Error, the valueDate values is wrong, the full payload is " . var_export($this->request, true));
        }

        return true;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->request);
    }

    public function offsetGet($offset)
    {
        return isset($this->request[$offset]) ? $this->request[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->request[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->request[$offset]);
    }
}

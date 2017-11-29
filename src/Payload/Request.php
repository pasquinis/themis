<?php
namespace Themis\Payload;

use ArrayAccess;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Request implements ArrayAccess
{
    private $request;

    public function __construct($request)
    {
        $this->mapRequest($request);
        $this->isWellformed();
        $this->isNotProcessable();
    }

    private function mapRequest($request)
    {
        if (count($request) != 8) {
            throw new BadRequestHttpException("Error, see request: " . var_export($request, true));
        }
        $this->request['data'] = $request[0];
        $this->request['operation'] = $request[1];
        $this->request['details'] = $request[2];
        $this->request['bank_account'] = $request[3];
        $this->request['accounting'] = $request[4];
        $this->request['category'] = $request[5];
        $this->request['currency'] = $request[6];
        $this->request['amount'] = $request[7];
    }

    public static function box(HttpRequest $request)
    {
        $payload = $request->request->all();
        return new self(str_getcsv($payload['data']));
    }

    public function isWellformed()
    {
        preg_match('#[\d]{1,2}/[\d]{1,2}/[\d]{4}#', $this->request['data'], $matches);
        if (empty($matches)) {
            throw new BadRequestHttpException("Error, the data values is wrong, the full payload is " . var_export($this->request, true));
        }

        return true;
    }

    public function isNotProcessable()
    {
        preg_match('#(NON CONTABILIZZATO)#', $this->request['accounting'], $matches);
        if (!empty($matches)) {
            throw new BadRequestHttpException("Error, the transaction is not yet accounted " . var_export($this->request, true));
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

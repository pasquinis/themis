<?php
namespace Themis\Payload;

use ArrayAccess;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Request implements ArrayAccess
{
    private $request;

    const UNACCOUNTED = 'NON CONTABILIZZATO';

    public function __construct($request)
    {
        $this->mapRequest($request);
        $this->isUnaccounted();
        $this->isWellformed();
    }

    //TODO this is when BancaIntesa create a different payload
    // private function mapRequest($request)
    // {
    //     if (count($request) != 7) {
    //         throw new BadRequestHttpException("Error, see request: " . var_export($request, true));
    //     }
    //     $this->request['operationDate'] = $request[0];
    //     $this->request['valueDate'] = $request[1];
    //     $this->request['description'] = $request[2];
    //     $this->request['revenue'] = $request[3];
    //     $this->request['expenditure'] = $request[4];
    //     $this->request['description_extended'] = $request[5];
    //     $this->request['bank_account'] = $request[6];
    // }

    private function mapRequest($request)
    {
        if (count($request) != 8) {
            throw new BadRequestHttpException("Error, see request: " . var_export($request, true));
        }
        $this->request['operationDate'] = $request[0];
        $this->request['valueDate'] = $request[0];
        $this->request['description'] = $request[5];
        $this->request['revenue'] = $this->getAmount($request[7], 'revenue');
        $this->request['expenditure'] = $this->getAmount($request[7], 'expenditure');
        $this->request['description_extended'] = $request[2];
        $this->request['bank_account'] = $request[3];
        $this->request['state'] = $request[4];
    }

    public static function box(HttpRequest $request)
    {
        $payload = $request->request->all();
        return new self(str_getcsv($payload['data']));
    }

    //TODO this is when BancaIntesa create a different payload
    // public function isWellformed()
    // {
    //     preg_match('#[\d]{1,2}/[\d]{1,2}/[\d]{2}#', $this->request['operationDate'], $matches);
    //     if (empty($matches)) {
    //         throw new BadRequestHttpException("Error, the data values is wrong, the full payload is " . var_export($this->request, true));
    //     }

    //     return true;
    // }

    public function isWellformed()
    {
        preg_match('#[\d]{2}-[\d]{2}-[\d]{2}#', $this->request['operationDate'], $matches);
        if (empty($matches)) {
            throw new BadRequestHttpException("Error, the data values is wrong, the full payload is " . var_export($this->request, true));
        }

        return true;
    }

    public function isUnaccounted()
    {
        if (self::UNACCOUNTED === $this->request['state']) {
            throw new BadRequestHttpException(
                "Error, this data is UNACCOUNTED " . var_export($this->request, true)
            );
        }

        return false;
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

    private function getAmount($amount, $label)
    {
        $floatAmount = floatval($amount);
        if ($label === 'revenue') {
            return ($floatAmount) > 0 ? $floatAmount : '';
        }
        if ($label === 'expenditure') {
            return ($floatAmount) < 0 ? $floatAmount : '';
        }
    }
}

<?php
namespace Themis\Transaction;

use ArrayAccess;
use DateTime;
use Themis\Application;
use Themis\Payload\Request;
use Themis\Payload\RequestCariparma;

class Transaction implements ArrayAccess
{
    private $request;
    private $transaction;

    private function __construct($request)
    {
        $this->request = $request;
        if ($this->request instanceof Request) {
            $this->transaction = $this->forgeTransaction();
        }
        if ($this->request instanceof RequestCariparma) {
            $this->transaction = $this->forgeTransactionCariparma();
        }
    }

    public function byRequestCariparma(RequestCariparma $request)
    {
        return new self($request);
    }

    public function byRequest(Request $request)
    {
        return new self($request);
    }

    public function __call($name, $arguments)
    {
        return $this->transaction[$name];
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

    public function toArray()
    {
        return $this->transaction;
    }

    private function forgeAmount($amount)
    {
        return number_format(str_replace(',', '', $amount), 2);
    }

    private function forgeTransaction()
    {
        $forgingDate = function ($date) {
            $date = DateTime::createFromFormat('m-d-y', $date);
            return $date->format('Y-m-d');
        };

        $forgingAmount = function ($amount, $type) {
            if (
                $type == 'revenue' && (int)$amount > 0
            ) {
                return $this->forgeAmount($amount);
            }
            if (
                $type == 'expenditure' && (int)$amount < 0
            ) {
                return $this->forgeAmount($amount);
            }
            return '';
        };

        //TODO add Interface::
        return [
            'operationDate' => $forgingDate($this->request['operationDate']),
            'valueDate' => $forgingDate($this->request['valueDate']),
            'description' => $this->request['description'],
            'reason' => $this->request['description_extended'],
            'revenue' => $forgingAmount($this->request['revenue'], 'revenue'),
            'expenditure' => $forgingAmount($this->request['expenditure'], 'expenditure'),
            'currency' => 'EUR',
        ];
    }

    // TODO this is when BancaIntesa use a different CSV output
    //private function forgeTransaction()
    //{
    //    $forgingDate = function ($date) {
    //        //Intesa CSV has date in DD/MM/YY format
    //        $date = DateTime::createFromFormat('d/m/y', $date);
    //        return $date->format('Y-m-d');
    //    };

    //    $forgingAmount = function ($amount, $type) {
    //        if (
    //            $type == 'revenue' && (int)$amount > 0
    //        ) {
    //            return $this->forgeAmount($amount);
    //        }
    //        if (
    //            $type == 'expenditure' && (int)$amount < 0
    //        ) {
    //            return $this->forgeAmount($amount);
    //        }
    //        return '';
    //    };

    //    //TODO add Interface::
    //    return [
    //        'operationDate' => $forgingDate($this->request['operationDate']),
    //        'valueDate' => $forgingDate($this->request['valueDate']),
    //        'description' => $this->request['description'],
    //        'reason' => $this->request['description_extended'],
    //        'revenue' => $forgingAmount($this->request['revenue'], 'revenue'),
    //        'expenditure' => $forgingAmount($this->request['expenditure'], 'expenditure'),
    //        'currency' => 'EUR',
    //    ];
    //}

    private function forgeTransactionCariparma()
    {
        $forgingDate = function ($date) {
            $date = DateTime::createFromFormat('j/n/Y', $date);
            return $date->format('Y-m-d');
        };
        $forgingAmount = function ($amount, $type) {
            if (
                $type == 'revenue' && (int)$amount > 0
            ) {
                return $this->forgeAmount($amount);
            }
            if (
                $type == 'expenditure' && (int)$amount < 0
            ) {
                return $this->forgeAmount($amount);
            }
            return '';
        };

        return [
            'operationDate' => $forgingDate($this->request['operationDate']),
            'valueDate' => $forgingDate($this->request['valueDate']),
            'description' => $this->request['description'],
            'reason' => $this->request['reason'],
            'revenue' => $forgingAmount($this->request['revenue'], 'revenue'),
            'expenditure' => $forgingAmount($this->request['expenditure'], 'expenditure'),
            'currency' => $this->request['currency'],
        ];
    }
}

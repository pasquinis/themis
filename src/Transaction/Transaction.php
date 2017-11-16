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
            //Intesa CSV has date in M/D/YYYY format
            $date = DateTime::createFromFormat('n/j/Y', $date);
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
            'operationDate' => $forgingDate($this->request['data']),
            'valueDate' => $forgingDate($this->request['data']),
            'description' => $this->request['category'],
            'reason' => $this->request['details'],
            'revenue' => $forgingAmount($this->request['amount'], 'revenue'),
            'expenditure' => $forgingAmount($this->request['amount'], 'expenditure'),
            'currency' => $this->request['currency'],
        ];
    }

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

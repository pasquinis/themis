<?php
namespace Themis\Transaction;

use Themis\Payload\Request;

class TransactionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->request = $this->createMock(Request::class);

    }

    public function testShouldCreateATransactionByBoxingARequest()
    {
        $this->request
            ->method('offsetExists')
            ->will($this->returnValue(true))
        ;
        $config = [
            'data' => '9/20/2017',
            'operation' => 'Bonifici in uscita',
            'details' => 'RISTRUTT./EFF. ENERG./MOB. ELETTR. ART. 16BIS TUIR Saldo Lavori',
            'bank_account' => 'Conto 1000/1234',
            'accounting' => 'CONTABILIZZATO',
            'category' => 'Bonifici in uscita',
            'currency' => 'EUR',
            'amount' => '-3,800.00',
        ];

        $this->request
            ->method('offsetGet')
            ->will($this->returnCallback(
                function ($key) use ($config) {
                    return $config[$key];
                }
            ))
        ;

        $transaction = Transaction::box($this->request);
        $this->assertEquals('2017-09-20', $transaction->operationDate());
        $this->assertEquals('2017-09-20', $transaction->valueDate());
        $this->assertEquals('Bonifici in uscita', $transaction->description());
        $this->assertEquals('RISTRUTT./EFF. ENERG./MOB. ELETTR. ART. 16BIS TUIR Saldo Lavori', $transaction->reason());
        $this->assertEquals('', $transaction->revenue());
        $this->assertEquals('-3,800.00', $transaction->expenditure());
        $this->assertEquals('EUR', $transaction->currency());
    }
}

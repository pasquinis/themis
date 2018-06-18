<?php
namespace Themis\Transaction;

use Themis\Payload\Request;

class TransactionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->request = $this->createMock(Request::class);
        $this->request
            ->method('offsetExists')
            ->will($this->returnValue(true))
        ;

        //TODO remove this duplication
        $config = [
            'operationDate' => '09-20-17',
            'valueDate' => '09-20-17',
            'description' => 'Bonifici in uscita',
            'description_extended' => 'RISTRUTT./EFF. ENERG./MOB. ELETTR. ART. 16BIS TUIR Saldo Lavori',
            'bank_account' => 'Conto 1000/1234',
            'revenue' => '',
            'expenditure' => '-3,800.00',
        ];
        //TODO this is when BancaIntesa use a different CSV output
        // $config = [
        //     'operationDate' => '20/09/17',
        //     'valueDate' => '20/09/17',
        //     'description' => 'Bonifici in uscita',
        //     'description_extended' => 'RISTRUTT./EFF. ENERG./MOB. ELETTR. ART. 16BIS TUIR Saldo Lavori',
        //     'bank_account' => 'Conto 1000/1234',
        //     'revenue' => '',
        //     'expenditure' => '-3,800.00',
        // ];
        $this->request
            ->method('offsetGet')
            ->will($this->returnCallback(
                function ($key) use ($config) {
                    return $config[$key];
                }
            ))
        ;

    }

    public function testShouldCreateATransactionByBoxingARequest()
    {
        $transaction = Transaction::byRequest($this->request);
        $this->assertEquals('2017-09-20', $transaction->operationDate());
        $this->assertEquals('2017-09-20', $transaction->valueDate());
        $this->assertEquals('Bonifici in uscita', $transaction->description());
        $this->assertEquals('RISTRUTT./EFF. ENERG./MOB. ELETTR. ART. 16BIS TUIR Saldo Lavori', $transaction->reason());
        $this->assertEquals('', $transaction->revenue());
        $this->assertEquals('-3,800.00', $transaction->expenditure());
        $this->assertEquals('EUR', $transaction->currency());
    }

    public function testShouldHaveAnArrayRepresentationOfTransaction()
    {
        $transaction = Transaction::byRequest($this->request);
        $this->assertEquals(
            [
                'operationDate' => '2017-09-20',
                'valueDate' => '2017-09-20',
                'description' => 'Bonifici in uscita',
                'reason' => 'RISTRUTT./EFF. ENERG./MOB. ELETTR. ART. 16BIS TUIR Saldo Lavori',
                'revenue' => '',
                'expenditure' => '-3,800.00',
                'currency' => 'EUR',
            ],
            $transaction->toArray())
        ;
    }
}

<?php
namespace Themis\Payload;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->httpRequest = new HttpRequest();
    }

    public function testShouldBoxAnHttpRequest()
    {
        $this->httpRequest->initialize([], ['data' => '02/01/18,29/12/17,Pagamento tramite pos,,-227.60,Supermercato Esselunga Via 29 121846  Carta N6762 Xxxx Xxxx Xx53abi  05584 Cod 008655,CARTA BANCOCARD                **** XX']);

        $boxed = Request::box($this->httpRequest);
        $this->assertEquals('02/01/18', $boxed['operationDate']);
        $this->assertEquals('29/12/17', $boxed['valueDate']);
        $this->assertEquals('Pagamento tramite pos', $boxed['description']);
        $this->assertEquals('', $boxed['revenue']);
        $this->assertEquals('-227.60', $boxed['expenditure']);
        $this->assertEquals('Supermercato Esselunga Via 29 121846  Carta N6762 Xxxx Xxxx Xx53abi  05584 Cod 008655', $boxed['description_extended']);
        $this->assertEquals('CARTA BANCOCARD                **** XX', $boxed['bank_account']);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testShouldVerifyTheSanityOfWrongPayload()
    {
        $this->httpRequest->initialize([], ['data' => ',Investimenti e previdenza:,-,,,,, ']);
        Request::box($this->httpRequest);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testShouldRejectTransactionWhenAccountingIsNotYetAccounted()
    {
        $this->httpRequest->initialize([], ['data' => '9/20/2017,Assegno N. 343,Assegno N. 831964Xxxx,Conto 1000/00014003,NON CONTABILIZZATO,Assegni pagati,EUR,-903.00']);
        Request::box($this->httpRequest);
    }
}

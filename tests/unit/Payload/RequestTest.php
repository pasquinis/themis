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
        $this->httpRequest->initialize([], ['data' => '9/20/2017,Assegno N. 343,Assegno N. 831964Xxxx,Conto 1000/00014003,CONTABILIZZATO,Assegni pagati,EUR,-903.00']);

        $boxed = Request::box($this->httpRequest);
        $this->assertEquals('9/20/2017', $boxed['data']);
        $this->assertEquals('Assegno N. 343', $boxed['operation']);
        $this->assertEquals('Assegno N. 831964Xxxx', $boxed['details']);
        $this->assertEquals('Conto 1000/00014003', $boxed['bank_account']);
        $this->assertEquals('CONTABILIZZATO', $boxed['accounting']);
        $this->assertEquals('Assegni pagati', $boxed['category']);
        $this->assertEquals('EUR', $boxed['currency']);
        $this->assertEquals('-903.00', $boxed['amount']);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testShouldVerifyTheSanityOfWrongPayload()
    {
        $this->httpRequest->initialize([], ['data' => ',Investimenti e previdenza:,-,,,,, ']);

        $boxed = Request::box($this->httpRequest);
    }
}

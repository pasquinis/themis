<?php
namespace Themis\Payload;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

class RequestCariparmaTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->httpRequest = new HttpRequest();
    }

    public function testShouldBoxAnHttpRequest()
    {
        $this->httpRequest->initialize([], ['data' => '28/02/2017,28/02/2017,PAGAMENTO UTENZE,"SDD A : ILLUMIA SPA FATTURA NUM. 48466/G DEL 08/02/2017, SCADENZA IL 28/02/201 7 ADDEBITO SDD NUMERO 1234567",,-123.80,EUR']);

        $boxed = RequestCariparma::box($this->httpRequest);
        $this->assertEquals('28/02/2017', $boxed['operationDate']);
        $this->assertEquals('28/02/2017', $boxed['valueDate']);
        $this->assertEquals('PAGAMENTO UTENZE', $boxed['description']);
        $this->assertEquals('SDD A : ILLUMIA SPA FATTURA NUM. 48466/G DEL 08/02/2017, SCADENZA IL 28/02/201 7 ADDEBITO SDD NUMERO 1234567', $boxed['reason']);
        $this->assertEquals('', $boxed['revenue']);
        $this->assertEquals('-123.8', $boxed['expenditure']);
        $this->assertEquals('EUR', $boxed['currency']);
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

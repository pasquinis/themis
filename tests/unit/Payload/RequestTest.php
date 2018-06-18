<?php
namespace Themis\Payload;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->httpRequest = new HttpRequest();
    }

    public function testShouldBoxAnHttpRequestWithRevenue()
    {
        //TODO sometimes the xlsx return this information.
        // I will put here as reminder
        // $this->httpRequest->initialize([], ['data' => '02/01/18,29/12/17,Pagamento tramite pos,,-227.60,Supermercato Esselunga Via 29 121846  Carta N6762 Xxxx Xxxx Xx53abi  05584 Cod 008655,CARTA BANCOCARD                **** XX']);
        $this->httpRequest->initialize([], ['data' => '05-04-18,Stipendio O Pensione,COD. DISP. 011805040R588P SALA STIPENDIO O PENSIONE 201804260658204683 Accredito Stipendio Bonifico A Vostro Favore Disposto Da MITT. TWOBOP S.R.L. BENEF. PASQUINI SIMONE BIC. ORD. BNLIITRRXXX,Conto 1000/0001,CONTABILIZZATO,Stipendi e pensioni,EUR,77.0']);

        $boxed = Request::box($this->httpRequest);
        $this->assertEquals('05-04-18', $boxed['operationDate']);
        $this->assertEquals('05-04-18', $boxed['valueDate']);
        $this->assertEquals('Stipendi e pensioni', $boxed['description']);
        $this->assertEquals('77.0', $boxed['revenue']);
        $this->assertEquals('', $boxed['expenditure']);
        $this->assertEquals('COD. DISP. 011805040R588P SALA STIPENDIO O PENSIONE 201804260658204683 Accredito Stipendio Bonifico A Vostro Favore Disposto Da MITT. TWOBOP S.R.L. BENEF. PASQUINI SIMONE BIC. ORD. BNLIITRRXXX', $boxed['description_extended']);
        $this->assertEquals('Conto 1000/0001', $boxed['bank_account']);
    }

    public function testShouldBoxAnHttpRequestWithExpenditure()
    {
        $this->httpRequest->initialize([], ['data' => "05-08-18,L'albero Della Frutta,Pagamento Su POS L'ALBERO DELLA FRUTTA 08/051618 Carta N.XXXX XXXX XX53 COD. 3532623/00003,Conto 1000/0001,CONTABILIZZATO,Generi alimentari e supermercato,EUR,-38.0"]);

        $boxed = Request::box($this->httpRequest);
        $this->assertEquals('05-08-18', $boxed['operationDate']);
        $this->assertEquals('05-08-18', $boxed['valueDate']);
        $this->assertEquals('Generi alimentari e supermercato', $boxed['description']);
        $this->assertEquals('', $boxed['revenue']);
        $this->assertEquals('-38.0', $boxed['expenditure']);
        $this->assertEquals("Pagamento Su POS L'ALBERO DELLA FRUTTA 08/051618 Carta N.XXXX XXXX XX53 COD. 3532623/00003", $boxed['description_extended']);
        $this->assertEquals('Conto 1000/0001', $boxed['bank_account']);
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

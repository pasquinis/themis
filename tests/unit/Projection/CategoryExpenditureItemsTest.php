<?php
namespace Themis\Projection;

class CategoryExpenditureItemsTest extends \PHPUnit_Framework_TestCase
{
    private $goodReason;
    private $badReason;
    private $posDescription;

    public function setUp()
    {
        $this->posDescription = 'PAGAMENTO TRAMITE POS';
        $this->goodReason = 'POS CARTA 01234567 DEL 27/03/17 ORE 13.19 C/O 123456700088 HOLDING DEI GIOCHI SPA - MILANO';
        $this->badReason = 'POS CARTA 01234567 DEL 27/03/17 ORE 13.19 C/O123456700088 HOLDING DEI GIOCHI SPA - MILANO';
        $this->unknownReason = 'POS CARTA 01234567 DEL 27/03/17 ORE 13.19 C/O 123456700086 HOLDING DEI GIOCHI SPA - MILANO';
    }

    public function testShouldKnowInWhichCategoryTheTransactionWillAppear()
    {
        $transaction = [
            'description' => $this->posDescription,
            'reason' => $this->goodReason
        ];
        $expected = CategoryExpenditureItems::VARIABLE_COST_SONS;

        $expenditureItems = new CategoryExpenditureItems();
        $this->assertEquals($expected, $expenditureItems->category($transaction));
    }

    public function testShouldReturnOtherCategoryWhenIsUnknow()
    {
        $transaction = [
            'description' => $this->posDescription,
            'reason' => $this->unknownReason
        ];
        $expected = CategoryExpenditureItems::VARIABLE_COST_OTHER;

        $expenditureItems = new CategoryExpenditureItems();
        $this->assertEquals($expected, $expenditureItems->category($transaction));
    }

    public function testShouldRecognizeTheSalary()
    {
        $transaction = [
            'description' => 'ACCREDITO EMOLUMENTI',
            'reason' => 'ORD:BIPONE SRL DT.ORD:000000 DESCR.OPERAZIONE SCT:ACCREDITOSTIPE'
        ];
        $expected = CategoryExpenditureItems::VARIABLE_REVENUE_HUSBAND;

        $expenditureItems = new CategoryExpenditureItems();
        $this->assertEquals($expected, $expenditureItems->category($transaction));
    }
}

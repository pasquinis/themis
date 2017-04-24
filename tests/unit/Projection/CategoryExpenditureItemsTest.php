<?php
namespace Themis\Projection;


class CategoryExpenditureItemsTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldExtractPOSIdentificationCodeFromDescriptionField()
    {
        $description = 'POS CARTA 01234567 DEL 27/03/17 ORE 13.19 C/O 123456700088 HOLDING DEI GIOCHI SPA - MILANO';
        $expected = '123456700088';

        $expenditureItems = new CategoryExpenditureItems();
        $this->assertEquals($expected, $expenditureItems->idPos($description));
    }

    /**
    * @expectedException Themis\Projection\CategoryExpenditureItemsException
    */
    public function testShouldThrowExceptionWhenTheDescriptionNotContaintPOSId()
    {
        $description = 'POS CARTA 01234567 DEL 27/03/17 ORE 13.19 C/O123456700088 HOLDING DEI GIOCHI SPA - MILANO';

        $expenditureItems = new CategoryExpenditureItems();
        $expenditureItems->idPos($description);
    }
}

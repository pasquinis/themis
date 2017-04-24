<?php
namespace Themis\Projection;

class CategoryExpenditureItems
{
    const VARIABLE_COST_OTHER = 'Altro';
    const VARIABLE_COST_SONS = 'Spese/figli';
    const VARIABLE_REVENUE_HUSBAND = 'HUSBAND';

    const CATEGORY_POS_PAYMENT = 'PAGAMENTO TRAMITE POS';
    const CATEGORY_ACCREDITATION_FEES = 'ACCREDITO EMOLUMENTI';

    private $categories;
    private $description;
    private $reason;

    public function __construct($description, $reason)
    {
        $this->categories = [
            self::VARIABLE_COST_SONS => ['123456700088'],
        ];
        $this->description = $description;
        $this->reason = $reason;
    }

    public function category()
    {
        if (self::CATEGORY_POS_PAYMENT == $this->description) {
            $idPos = $this->idPos($this->reason);
            foreach($this->categories as $category => $listOfId) {
                if (in_array($idPos, $listOfId)) {
                    return $category;
                }
            }
            return self::VARIABLE_COST_OTHER;
        }
        if (self::CATEGORY_ACCREDITATION_FEES == $this->description) {
            $idSalary = $this->idSalary();

            return ('ORG:TREBI' == $idSalary) ?
                self::VARIABLE_REVENUE_WIFE :
                self::VARIABLE_REVENUE_HUSBAND
            ;
        }
    }

    private function idSalary()
    {
        $matches = [];
        $pattern = '/ORD:([a-zA-Z0-9]*) /';
        preg_match($pattern, $this->reason, $matches);
        if (!isset($matches[1])) {
            throw new CategoryExpenditureItemsException(
                "ERROR: for {$this->reason} I can't identify SALARY Id"
            );
        }
        return $matches[1];
    }

    public function idPos()
    {
        $matches = [];
        $pattern = '/C\/O ([0-9]*) /';
        preg_match($pattern, $this->reason, $matches);
        if (!isset($matches[1])) {
            throw new CategoryExpenditureItemsException(
                "ERROR: for {$this->reason} I can't identify POS Id"
            );
        }
        return $matches[1];
    }
}

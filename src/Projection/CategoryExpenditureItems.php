<?php
namespace Themis\Projection;

class CategoryExpenditureItems implements ExpenditureItems
{
    const VARIABLE_COST_OTHER = 'Altro';
    const VARIABLE_COST_SONS = 'Spese/figli';
    const VARIABLE_REVENUE_HUSBAND = 'HUSBAND';

    const CATEGORY_POS_PAYMENT = 'PAGAMENTO TRAMITE POS';
    const CATEGORY_ACCREDITATION_FEES = 'ACCREDITO EMOLUMENTI';

    private $categories;

    public function __construct()
    {
        $this->categories = [
            self::VARIABLE_COST_SONS => ['123456700088'],
        ];
    }

    public function category(array $transaction)
    {
        $description = $transaction['description'];
        $reason = $transaction['reason'];
        if (self::CATEGORY_POS_PAYMENT == $description) {
            $idPos = $this->idPos($reason);
            foreach($this->categories as $category => $listOfId) {
                if (in_array($idPos, $listOfId)) {
                    return $category;
                }
            }
            return self::VARIABLE_COST_OTHER;
        }
        if (self::CATEGORY_ACCREDITATION_FEES == $description) {
            $idSalary = $this->idSalary($reason);

            return ('ORG:TREBI' == $idSalary) ?
                self::VARIABLE_REVENUE_WIFE :
                self::VARIABLE_REVENUE_HUSBAND
            ;
        }
    }

    private function idSalary($reason)
    {
        $matches = [];
        $pattern = '/ORD:([a-zA-Z0-9]*) /';
        preg_match($pattern, $reason, $matches);
        if (!isset($matches[1])) {
            throw new CategoryExpenditureItemsException(
                "ERROR: for {$reason} I can't identify SALARY Id"
            );
        }
        return $matches[1];
    }

    private function idPos($reason)
    {
        $matches = [];
        $pattern = '/C\/O ([0-9]*) /';
        preg_match($pattern, $reason, $matches);
        if (!isset($matches[1])) {
            throw new CategoryExpenditureItemsException(
                "ERROR: for {$reason} I can't identify POS Id"
            );
        }
        return $matches[1];
    }
}

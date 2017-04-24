<?php
namespace Themis\Projection;

class CategoryExpenditureItems
{
    const VARIABLE_COST_OTHER = 'Altro';
    const VARIABLE_COST_SONS = 'Spese/figli';

    private $categories;

    public function __construct()
    {
        $this->categories = [
            self::VARIABLE_COST_SONS => ['123456700088']
        ];
    }

    public function category($description)
    {
        $idPos = $this->idPos($description);

        foreach($this->categories as $category => $listOfId) {
            if (in_array($idPos, $listOfId)) {
                return $category;
            }
        }
        return self::VARIABLE_COST_OTHER;
    }

    public function idPos($description)
    {
        $matches = [];
        $pattern = '/C\/O ([0-9]*) /';
        preg_match($pattern, $description, $matches);
        if (!isset($matches[1])) {
            throw new CategoryExpenditureItemsException(
                "ERROR: for {$description} I can't identify POS Id"
            );
        }
        return $matches[1];
    }
}

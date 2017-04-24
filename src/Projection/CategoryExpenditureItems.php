<?php
namespace Themis\Projection;

class CategoryExpenditureItems
{
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

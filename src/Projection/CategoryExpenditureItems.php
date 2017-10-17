<?php
namespace Themis\Projection;

class CategoryExpenditureItems implements ExpenditureItems
{
    const VARIABLE_COST_OTHER = 'Altro';
    const VARIABLE_COST_SONS = 'Spese/Figli';
    const VARIABLE_COST_VIRIDEA = 'Spese/Viridea';
    const VARIABLE_COST_TRANSPORT = 'Spese/Trasporti';
    const VARIABLE_COST_PHARMACY = 'Spese/Farmacia';
    const VARIABLE_COST_FELTRINELLI = 'Spese/Feltrinelli';
    const VARIABLE_COST_TELEFONO = 'Spese/Telefono';
    const VARIABLE_COST_COZZI = 'Spese/Piscina';
    const VARIABLE_COST_MEDIAWORLD = 'Spese/MediaWorld';
    const VARIABLE_COST_DAMAFRUITS = 'Spese/Damafruits';
    const VARIABLE_COST_FOOD = 'Spese/Cibo';
    const VARIABLE_COST_DRESS = 'Spese/Vestiario';
    const VARIABLE_COST_HOME = 'Spese/Casa';
    const VARIABLE_COST_EXTRAORDINARY = 'Spese/Straordinarie';
    const VARIABLE_COST_WELFARE = 'Spese/Salute';
    const VARIABLE_COST_PARKING = 'Spese/Parcheggio';
    const VARIABLE_COST_COMMISSION = 'Banca/Commissioni';
    const VARIABLE_COST_WITHDRAW_MONEY = 'Banca/Prelievo';
    const VARIABLE_COST_CASH_PAYMENT_BANK = 'Banca/Versamento';
    const VARIABLE_COST_HOME_RENOVATION = 'Spese/Ristrutturazione';

    const VARIABLE_REVENUE_HUSBAND = 'Stipedio/Marito';
    const VARIABLE_REVENUE_WIFE = 'Stipedio/Moglie';
    const VARIABLE_MORTGAGE = 'Mutuo';
    const VARIABLE_CREDIT_CARD = 'CartaCredito';
    const VARIABLE_DOMICILIATIONS = [
        'GENERALI' => 'Utenze/Generali',
        'ILLUMIA' => 'Utenze/Illumia',
        'PROFAMILY' => 'Utenze/Condizionatore',
        '03069' => 'Utenze/Condominio',
        'MANTOVA' => 'Utenze/Spazzatura',
        'INFOSTRADA' => 'Utenze/Telefonia',
        'CREDIT' => 'Utenze/AssicurazioneCasa',
        'WIND-TRE' => 'Utenze/Telefonia',
    ];
    const VARIABLE_INTERESTS = [
        'CANONE' => 'Competenze/Canone',
        'COMPETENZE' => 'Competenze/Liquidazione'
    ];
    const VARIABLE_BANK_TRANSFER = [
        'PARROCCHIA SAN GIOVANNI BATTISTA' => 'Scuola/Lucio',
        'DEFAULT' => 'Spese/Straordinarie'
    ];

    const CATEGORY_POS_PAYMENT = 'PAGAMENTO TRAMITE POS';
    const CATEGORY_ACCREDITATION_FEES = 'ACCREDITO EMOLUMENTI';
    const CATEGORY_MORTGAGE = 'PAGAMENTO RATE FINANZIAMENTO';
    const CATEGORY_CREDIT_CARD = 'PAG. PER UTILIZZO CARTE CREDITO';
    const CATEGORY_DOMICILIATION = 'PAGAMENTO UTENZE';
    const CATEGORY_INTEREST = 'INTERESSI/COMPETENZE';
    const CATEGORY_BANK_TRANSFER = 'DISPOSIZIONE DI PAGAMENTO';
    const CATEGORY_COMMISSION = 'COMMISSIONI/SPESE';
    const CATEGORY_WITHDRAW_MONEY = 'PRELIEVO NOSTRO SPORTELLO AUTOM.';
    const CATEGORY_WITHDRAW_MONEY_FOREIGN_BANK = 'PRELIEVO SPORT. AUTOM. ALTRA BANCA';
    const CATEGORY_CASH_PAYMENT_BANK = 'VERSAMENTO CONTANTE/ASSEGNI';
    const CATEGORY_TRANSFER = 'GIROCONTO/BONIFICO';
    const CATEGORY_TRANSFER_DEBT = 'ADDEBITO ASSEGNO';
    const CATEGORY_FINANCIALS_TRANSFER = 'STORNO MOVIMENTI';
    const CATEGORY_DIFFERENT_PAYMENT = 'PAGAMENTI DIVERSI';

    private $categories;

    public function __construct()
    {
        $this->categories = [
            self::VARIABLE_COST_SONS => [
                '123456700088',
                '303274200088',
                '424802800002',
                '476914400001',
                '402142600004',
                '323904700025'
            ],
            self::VARIABLE_COST_VIRIDEA => ['380462500013'],
            self::VARIABLE_COST_TRANSPORT => [
                '396255400173',
                '396255400098',
                '396255400128',
                '380284200001',
                'NUOVA SIDAP',
                '336452500075',
                '484951700001'
            ],
            self::VARIABLE_COST_PHARMACY => [
                '413995900002',
                'FARMACIA'
            ],
            self::VARIABLE_COST_FELTRINELLI => ['389535500006'],
            self::VARIABLE_COST_TELEFONO => ['Ricarica telefonica'],
            self::VARIABLE_COST_COZZI => [
                '304964800017',
                'COZZI'
            ],
            self::VARIABLE_COST_MEDIAWORLD => ['303222300616'],
            self::VARIABLE_COST_DAMAFRUITS => ['387823400001'],
            self::VARIABLE_COST_FOOD => [
                'DONALD',
                '464796200001',
                '436815100002',
                '430208200004',
                '475205600001',
                '411242300002',
                '346748900002',
                '010763600001',
                '380005400002'
            ],
            self::VARIABLE_COST_PARKING => ['PARCHEGGIO'],
            self::VARIABLE_COST_DRESS => [
                '311279100007',
                '482007200036',
                '375226100003',
                '465385000133'
            ],
            self::VARIABLE_COST_HOME => [
                '398767900072',
                '475680800001',
                '321868700233',
                '301002400865',
                'ESSELUNGA',
                '342855100304',
                '373075601439',
                '301002401350',
                '300359400155',
                '328863000472',
                '444790700018'
            ],
            self::VARIABLE_COST_WELFARE => [
                '354225100005',
                '488214500014',
                '303662200005',
                '312135100015',
                '397489900003',
                '354225100004',
                '312161200006',
                '312144500001',
                '326152000003'
            ],
            self::VARIABLE_COST_EXTRAORDINARY => [
                '460324500001',
                '404314900001',
                '322865600004',
                '326700000002',
                '305054900002',
                '395775300001',
                '329560500005',
                '040315000001',
                '482007200252',
                '482007200159',
                '465385000501',
                '398767900046',
                '333989700801',
                '311279100026',
                '326904100002',
                '310859100003'
            ]
        ];
    }

    public function category(array $transaction)
    {
        $description = $transaction['description'];
        $reason = $transaction['reason'];
        if (self::CATEGORY_POS_PAYMENT == $description) {
            $idPos = $this->idPos($reason);
            foreach($this->categories as $category => $listOfId) {
                // var_dump("idPos: " .$idPos);
                // print_r($listOfId);
                if (in_array($idPos, $listOfId)) {
                    // var_dump("***" . $category);
                    return $category;
                }
            }
            // TODO sometimes remove the comment and fix the OTHER cost
            // var_dump("!!!!!!TO CHECK ". $idPos);
            return self::VARIABLE_COST_OTHER;
        }
        if (self::CATEGORY_ACCREDITATION_FEES == $description) {
            $idSalary = $this->idSalary($reason);

            return ('TREBI' == $idSalary) ?
                self::VARIABLE_REVENUE_WIFE :
                self::VARIABLE_REVENUE_HUSBAND
            ;
        }
        if (self::CATEGORY_MORTGAGE == $description) {
            return self::VARIABLE_MORTGAGE;
        }
        if (self::CATEGORY_CREDIT_CARD == $description) {
            return self::VARIABLE_CREDIT_CARD;
        }
        if (self::CATEGORY_DOMICILIATION == $description) {
            $domiciliation = $this->idDomiciliation($reason);
            // var_dump(__LINE__.$domiciliation);
            return self::VARIABLE_DOMICILIATIONS[$domiciliation];
        }
        if (self::CATEGORY_INTEREST == $description) {
            $interest = $this->idInterest($reason);
            return self::VARIABLE_INTERESTS[$interest];
        }
        if (self::CATEGORY_BANK_TRANSFER == $description) {
            $bankTransfer = $this->idBankTransfer($reason);
            return self::VARIABLE_BANK_TRANSFER[$bankTransfer];
        }
        if (self::CATEGORY_COMMISSION == $description) {
            return self::VARIABLE_COST_COMMISSION;
        }
        if (self::CATEGORY_WITHDRAW_MONEY == $description) {
            return self::VARIABLE_COST_WITHDRAW_MONEY;
        }
        if (self::CATEGORY_WITHDRAW_MONEY_FOREIGN_BANK == $description) {
            return self::VARIABLE_COST_WITHDRAW_MONEY;
        }
        if (self::CATEGORY_CASH_PAYMENT_BANK == $description) {
            return self::VARIABLE_COST_CASH_PAYMENT_BANK;
        }
        if (self::CATEGORY_TRANSFER == $description) {
            return self::VARIABLE_COST_CASH_PAYMENT_BANK;
        }
        if (self::CATEGORY_TRANSFER_DEBT == $description) {
            return self::VARIABLE_COST_WITHDRAW_MONEY;
        }
        if (self::CATEGORY_FINANCIALS_TRANSFER == $description) {
            return self::VARIABLE_COST_CASH_PAYMENT_BANK;
        }
        if (self::CATEGORY_DIFFERENT_PAYMENT == $description) {
            return self::VARIABLE_COST_HOME_RENOVATION;
        }
    }

    private function idBankTransfer($reason)
    {
        $matches = [];
        $pattern = '/(PARROCCHIA SAN GIOVANNI BATTISTA)/';
        preg_match($pattern, $reason, $matches);
        if (!isset($matches[1])) {
            //TODO because it is a generic expense
            return 'DEFAULT';
            //throw new CategoryExpenditureItemsException(
            //    "ERROR: for {$reason} I can't identify id Bank"
            //);
        }
        return $matches[1];
    }

    private function idInterest($reason)
    {
        $matches = [];
        $pattern = '/([a-zA-Z0-9]*) /';
        preg_match($pattern, $reason, $matches);
        if (!isset($matches[1])) {
            throw new CategoryExpenditureItemsException(
                "ERROR: for {$reason} I can't identify Interest"
            );
        }
        return $matches[1];
    }

    private function idDomiciliation($reason)
    {
        $matches = [];
        $pattern = '/SDD A : ([a-zA-Z0-9\-]*) |N. ([0-9]*)/';
        preg_match($pattern, $reason, $matches);
        $rightMatches = $this->returnTheRightPopulatedMatches($matches);
        // var_dump(__LINE__ . $rightMatches);
        if (!isset($rightMatches)) {
            throw new CategoryExpenditureItemsException(
                "ERROR: for {$reason} I can't identify SDD"
            );
        }
        return $rightMatches;
    }

    private function returnTheRightPopulatedMatches($matches)
    {
        unset($matches[0]);
        foreach($matches as $match) {
            if(!empty($match)) {
                return $match;
            }
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
        $pattern = '/C\/O ([0-9]*) |(NUOVA SIDAP)|(Ricarica telefonica)|(COZZI)|(DONALD)|(PARCHEGGIO)|(FARMACIA)|(ESSELUNGA)/';
        preg_match($pattern, $reason, $matches);
        $rightMatches = $this->returnTheRightPopulatedMatches($matches);
        // var_dump(__LINE__ . $rightMatches);
        if (!isset($rightMatches)) {
            throw new CategoryExpenditureItemsException(
                "ERROR: for {$reason} I can't identify POS Id"
            );
        }
        return $rightMatches;
    }
}

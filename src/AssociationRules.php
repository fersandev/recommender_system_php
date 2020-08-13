<?php

namespace feraiur\recommender_system_php;

/*
    Multitask class that encapsulates Apriori algorithm to find association rules from a dataset
*/

class AssociationRules {

    private $dataset = [];
    private $transactionTable = [];
    private $minSupportPercentage = 0;
    private $minConfidencePercentage = 0;

    function __construct($minSupportPercentage = 20, $minConfidencePercentage = 50) {
        $this->minSupportPercentage = $minSupportPercentage;
        $this->minConfidencePercentage = $minConfidencePercentage;
   }

    public function setDataset($dataset)
    {
        $isValid = false;
        if ($this->isValid($dataset)) {
            $this->dataset = $dataset;
            $this->createTransactionTable();
        } else {
            throw new Exception('Invalid dataset of transactions');
        }
    }

    public function isValid($dataset) {
        $isValid = true;

        if (count($dataset) == 0) {
            $isValid = false;
        }

        return $isValid;
    }

    public function getTransactionTable() {
        return $this->transactionTable;
    }

    public function createTransactionTable() {
        foreach ($this->dataset as $r => $rows) {
            $newRow = '';

            foreach ($rows as $c => $column) {
                $newRow .= '|'.$column.'|';
            }

            $this->transactionTable[] = $newRow;
        }
    }

    public function getUniqueItems() {
        $selectedElements = [];
        foreach ($this->transactionTable as $key => $value) {
            $elements = $this->everythingInTags($value, '\|');
            foreach ($elements as $e => $element) {
                if (!in_array($element, $selectedElements)) {
                    $selectedElements[] = strval($element);
                }
            }
        }
        return $selectedElements;
    }

    public function everythingInTags($string, $delimiter) {
        preg_match_all('/'.$delimiter.'(.*?)'.$delimiter.'/', $string, $match, PREG_PATTERN_ORDER);
        return $match[1];
    }

    public function getFrequencyOfElementsInArray($elementsInArray, $dataset) {
        $frequency = 0;

        foreach ($dataset as $d => $datarow) {
            $allFound = true;
            foreach ($elementsInArray as $e => $element) {
                $pos = strpos('$'.$datarow, '|'.$element.'|');

                if ($pos == 0) {
                    $allFound = false;
                }
            }

            if ($allFound) {
                $frequency++;
            }
        }

        return $frequency;
    }

    public function getSupports() {
        $totalTransactions = count($this->transactionTable);
        $supports = [];

        if ($totalTransactions > 0) {
            foreach ($this->getUniqueItems() as $key => $value) {
                $supports[$value] = $this->getFrequencyOfElementsInArray([$value], $this->transactionTable) / $totalTransactions;
            }
        }

        return $supports;
    }

    public function removeUnwantedSupports($supports) {
        $minSupportProportion = $this->minSupportPercentage / 100;

        foreach ($supports as $key => $value) {
            if ($value < $minSupportProportion) {
                unset($supports[$key]);
            }
        }

        return $supports;
    }

    public function getItemsetsWithK2($supports) {
        $supportOfCombinations = [];
        $supportsA = $supports;
        $supportsB = $supports;

        foreach ($supportsA as $key1 => $value1) {
            foreach ($supportsB as $key2 => $value2) {
                if ($key1 !== $key2) {
                    $pair = array($key1, $key2);
                    $supportOfCombinations[] = $pair;
                }
            }
            unset($supportsB[$key1]);
        }

        return $supportOfCombinations;
    }

    public function getK2Supports($itemsetsK2) {
        $totalTransactions = count($this->transactionTable);
        $supportsK2 = [];

        if ($totalTransactions > 0) {
            foreach ($itemsetsK2 as $key => $value) {
                $supportK2 = $this->getFrequencyOfElementsInArray($value, $this->transactionTable) / $totalTransactions;
                $supportsK2[$value[0].'|'.$value[1]] = $supportK2;
            }
        }

        return $supportsK2;
    }

    public function getFinalPairsItemsets($supportsK2) {
        return array_keys($supportsK2);
    }

    public function getConfidenceRules($finalPairsItemsets, $supportsK2, $supportsK1) {
        $minConfidenceAcepted = $this->minConfidencePercentage / 100;
        $confidences = [];

        foreach ($finalPairsItemsets as $p => $pairsItem) {
            $pairsItemArray = explode('|', $pairsItem);

            $ruleConfidenceA = $supportsK2[$pairsItem] / $supportsK1[$pairsItemArray[0]];
            if ($ruleConfidenceA >= $minConfidenceAcepted) {
                $confidences[$pairsItemArray[0].'->'.$pairsItemArray[1]] = $ruleConfidenceA;
            }

            $ruleConfidenceB = $supportsK2[$pairsItem] / $supportsK1[$pairsItemArray[1]];
            if ($ruleConfidenceB >= $minConfidenceAcepted) {
                $confidences[$pairsItemArray[1].'->'.$pairsItemArray[0]] = $ruleConfidenceB;
            }
        }
        return $confidences;
    }
}
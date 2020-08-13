<?php

namespace feraiur\recommender_system_php;

/**
 *  Class for similarity cosine matrix creation
 */

class SimilarityCosine {

    function createsimilarityCosineMatrix($ratingMatrixList) {
        list($usersIds, $itemsIds, $ratingMatrix) = $ratingMatrixList;

        foreach ($usersIds as $i => $userId) {
            foreach ($usersIds as $j => $userId2) {
                $cosineSimilarityMatrix[$userId][$userId2] = $this->similarity($ratingMatrix[$userId], $ratingMatrix[$userId2]);
            }
        }
       return $cosineSimilarityMatrix;
    }

    function similarity($A, $B) {
        $scalarProduct = $this->calculateHadamardProduct($A, $B) / ($this->calculateVectorialMagnitude($A) * $this->calculateVectorialMagnitude($B));
        return $scalarProduct;
    }

    function calculateHadamardProduct($A, $B) {
        $scalarProduct = 0;
        foreach ($B as $key => $value) {
            $scalarProduct += $A[$key] * $B[$key];
        }
        return $scalarProduct;
    }

    function calculateVectorialMagnitude($X) {
        $vectorialMagnitude = 0;
        foreach ($X as $key => $value) {
            $vectorialMagnitude += pow($X[$key], 2);
        }

        return sqrt($vectorialMagnitude);
    }

}
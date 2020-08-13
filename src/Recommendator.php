<?php

namespace feraiur\recommender_system_php;

/**
 *  Class for create recommendations based on similarity cosine matrix
 */

class Recommendator
{

    private $cosineSimilarityMatrix = [];
    const HOW_MANY_RECOMMENDATIONS = 5;
    const HOW_MUCH_SIMILAR = 0.2;

    function setCosineSimilarityMatrix($cosineSimilarityMatrix) {
        $this->cosineSimilarityMatrix = $cosineSimilarityMatrix;
    }

    function setRatingMatrixList($ratingMatrixList) {
        list($this->usersIds, $this->itemsIds, $this->ratingMatrix) = $ratingMatrixList;
    }

    function completeRatingMatrix() {
        foreach ($this->usersIds as $keyU => $userId) {
            foreach ($this->itemsIds as $keyI => $itemId) {
                if ($this->ratingMatrix[$userId][$itemId] == 0) {
                    $similarUsers = $this->getSimilarUsersList($userId);

                    /*
                    // Using average approach
                    $sumRu = 0;
                    $n = 0;
                    foreach ($similarUsers as $key => $similarUserId) {
                        $sumRu += $this->ratingMatrix[$similarUserId][$itemId];
                        $n++;
                    }
                    $Rui = $sumRu / $n;
                    */

                    // Using weighted average approach
                    $sumRu = 0;
                    $similarityFactorSum = 0;
                    foreach ($similarUsers as $key => $similarUserId) {
                        $sumRu += $this->ratingMatrix[$similarUserId][$itemId] * $this->cosineSimilarityMatrix[$userId][$similarUserId];
                        $similarityFactorSum += $this->cosineSimilarityMatrix[$userId][$similarUserId];
                    }

                    if ($similarityFactorSum > 0) {
                        $Rui = $sumRu / $similarityFactorSum;
                    } else {
                        $Rui = 0;
                    }

                    $ratingMatrixCompleted[$userId][$itemId] = round($Rui, 0, PHP_ROUND_HALF_EVEN);
                } else {
                    $ratingMatrixCompleted[$userId][$itemId] = $this->ratingMatrix[$userId][$itemId];
                }
            }
        }
        $this->ratingMatrixCompleted = $ratingMatrixCompleted;
        return $ratingMatrixCompleted;
    }

    function getSimilarUsersList($userId) {
        $similarUsers = array();

        foreach ($this->cosineSimilarityMatrix[$userId] as $userIdAsKey => $value) {
            if ($userId !== $userIdAsKey) {
                if ($value >= self::HOW_MUCH_SIMILAR) {
                    $similarUsers[$userIdAsKey] = $value;
                }
            }
        }

        arsort($similarUsers);

        return array_keys($similarUsers);
    }

    function getRecommendations() {
        $recommendations = [];
        foreach ($this->usersIds as $ui => $userId) {
            $recommendedItemsIds = [];

            $i = 0;
            foreach ($this->itemsIds as $ii => $itemId) {
                if (($this->ratingMatrix[$userId][$itemId] == 0) and ($this->ratingMatrixCompleted[$userId][$itemId] != 0 )) {
                    $i++;
                    $recommendedItemsIds[] = array('itemId' => $itemId, 'rating' => $this->ratingMatrixCompleted[$userId][$itemId]);
                }

                if ($i > self::HOW_MANY_RECOMMENDATIONS) {
                    break;
                }
            }

            $recommendations[] = array('userId' => $userId, 'recommendedItemsIds' => $recommendedItemsIds);
        }
        return $recommendations;
    }

}
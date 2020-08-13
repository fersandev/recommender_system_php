<?php

namespace feraiur\recommender_system_php;

/**
 *  Class for datasets process and manipulation
 */

class Dataset {
    /*
     * $datasetRating variable should be a multidimentional array with 3 columns ["user_id","item_id","rating"]
     */
    private $datasetRating = [];
    private $ratingMatrix = [];

    function createRatingMatrix($fillNaN = 0) {
        foreach ($this->datasetRating as $key => $value) {
            $usersIds[] = $value[0];
            $itemsIds[] = $value[1];
            $ratingMatrix[$value[0]][$value[1]] = (int)$value[2];
        }
        $usersIds = array_unique($usersIds);
        $itemsIds = array_unique($itemsIds);

        foreach ($usersIds as $keyU => $userId) {
            foreach ($itemsIds as $keyI => $itemId) {
                if (!isset($ratingMatrix[$userId][$itemId])) {
                    $ratingMatrix[$userId][$itemId] = $fillNaN;
                }
                //echo('<p>user: '.$userId.' - item: '.$itemId.' -> '.$ratingMatrix[$userId][$itemId].'</p>');
            }
        }

        return [$usersIds, $itemsIds, $ratingMatrix];
    }

    function getDatasetRating() {
        return $this->datasetRating;
    }

    function setDatasetRating($datasetRating) {
        if ($this->verifyDatasetRatingDimensions($datasetRating)) {
            $this->datasetRating = $datasetRating;
        } else {
            throw new Exception('Invalid dataset of ratings');
        }
    }

    function verifyDatasetRatingDimensions($datasetRating) {
        $isValid = true;
        if (count($datasetRating) < 2) {
            $isValid = false;
        } else {
            foreach ($datasetRating as $key => $value) {
                if (count($value) != 3) {
                    $isValid = false;
                    break;
                }

                if (count($value) == 3) {
                    if (!is_numeric($value[2])) {
                        $isValid = false;
                        break;
                    }
                }
            }
        }
        return $isValid;
    }
}
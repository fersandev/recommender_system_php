<?php

namespace feraiur\recommender_system_php;

require_once('Dataset.php');
require_once('SimilarityCosine.php');
require_once('Recommendator.php');

class Predictor {

    private $dataset = [];

    public function setDataset($dataset)
    {
        $this->dataset = $dataset;
    }

    public function predict() {
        $datasetObj = new Dataset();
        $datasetObj->setDatasetRating($this->dataset);
        list($usersIds, $itemsIds, $ratingMatrix) = $datasetObj->createRatingMatrix();

        $similarityCosine = new SimilarityCosine();
        $ratingMatrixCreated = $datasetObj->createRatingMatrix();
        $cosineSimilarityMatrix = $similarityCosine->createsimilarityCosineMatrix($ratingMatrixCreated);

        $recommendator = new Recommendator();
        $recommendator->setCosineSimilarityMatrix($similarityCosine->createsimilarityCosineMatrix($ratingMatrixCreated));
        $recommendator->setRatingMatrixList($ratingMatrixCreated);
        $ratingMatrixCompleted = $recommendator->completeRatingMatrix();

        $recommendations = $recommendator->getRecommendations();
        return $recommendations;
    }
}
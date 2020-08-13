# Recommender System Library with PHP
Recommender for PHP is a library that allows to create recommendations based on:
- Collarative Filtering.
- Apriori association rules.


## How To
Include the library using composer and then use the example below:
```
include __DIR__.'/vendor/autoload.php';

use feraiur\recommender_system_php\Predictor;
use feraiur\recommender_system_php\AssociationRules;


// Collaborative Filering

$predictor = new Predictor();
$dataseRatings = [
                [1, 1, 3],
                [2, 2, 5],
                [3, 3, 5],
                [4, 4, 5],
                [5, 5, 5],
                [2, 1, 2],
                [3, 1, 2],
                [4, 1, 2],
                [5, 1, 2],
              ];

try {
    $predictor->setDataset($dataseRatings);
    $recommendations = $predictor->predict();
} catch(Exception $e) {
    var_dump($e->getMessage());
    $recommendations = [];
}


echo("<h3>Recommendations:</h3>");
echo("<p>Some recommendations for users about items.</p>");
var_dump($recommendations);


// Association Rules

$associationRules = new AssociationRules(20, 50);
$datasetTransactions = [
                [1, 3],
                [3, 1],
                [2, 3],
                [3, 4],
                ["i1", 4],
                ["i2", 4],
                [1, 4],
                ["i2", 3],
                ["i2", 3, 4],
                ["i2", 3, "i1"],
              ];

try {
    $associationRules->setDataset($datasetTransactions);
    $supportsK1 = $associationRules->removeUnwantedSupports($associationRules->getSupports());
    $supportsK2 = $associationRules->removeUnwantedSupports($associationRules->getK2Supports($associationRules->getItemsetsWithK2($supportsK1)));
    $finalPairsItemsets = $associationRules->getFinalPairsItemsets($supportsK2);
    $confidenceRules = $associationRules->getConfidenceRules($finalPairsItemsets, $supportsK2, $supportsK1);

} catch(Exception $e) {
    var_dump($e->getMessage());
    $rules = [];
}

echo("<h3>Associations Rules:</h3>");
echo("<p>Ex: People who has item {A} has item {B} too.</p>");
var_dump($confidenceRules);
```


# Datasets format
- Dataset for rating predictions should be an array with 3 integer columns: user_id, item_id and rating value. (Items shuld not be duplicated for the same user).
- Dataset for apriori rules should be an array that contain another array of item ids. (Items should not be duplicated inside the array of each transaction)


## Author
Eng. Fernando Sánchez


## Unit Test Battery
The unit tests battery is inside the folder "/tests"
- Run `php phpunit.phar --verbose RecommendationTest.php`
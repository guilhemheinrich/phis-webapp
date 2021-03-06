<?php

//**********************************************************************************************
//                                       DocumentSearch.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: June 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June, 2017
// Subject: DocumentSearch represents the model behind the search form about app\models\Documents
//          Based on the Yii2 Search basic classes
//***********************************************************************************************

namespace app\models\yiiModels;

/**
 * implements the search action for the documents
 *
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DocumentSearch extends \app\models\yiiModels\YiiDocumentModel {
    //SILEX:refactor
    //create a trait (?) with methods search and jsonListOfArray and use it in 
    //each class ElementNameSearch
    //\SILEX:refactor
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['uri', 'documentType', 'creator', 'language', 'title', 'creationDate', 'extension', 'concernedItems'], 'safe']
        ];
    }
    
    /**
     * 
     * @param array $sessionToken used for the data access
     * @param string $params search params
     * @return mixed DataProvider of the result 
     *               or string \app\models\wsModels\WSConstants::TOKEN if the user needs to log in
     */
    public function search($sessionToken, $params) {
        //1. load the searched params 
        $this->load($params);
        if (isset($params[YiiModelsConstants::PAGE])) {
            $this->page = $params[YiiModelsConstants::PAGE];
        }
        
        //2. Check validity of search data
        if (!$this->validate()) {
            return new \yii\data\ArrayDataProvider();
        }
        
        //3. Request to the web service and return result
        $findResult = $this->find($sessionToken, $this->attributesToArray());
        
        if (is_string($findResult)) {
            return $findResult;
        } else if (isset($findResult[\app\models\wsModels\WSConstants::TOKEN])) {
            return $findResult;
        } else {
            $resultSet = $this->jsonListOfArraysToArray($findResult);
            return new \yii\data\ArrayDataProvider([
                'models' => $resultSet,
                'pagination' => [
                    'pageSize' => $this->pageSize,
                    'totalCount' => $this->totalCount
                ],
                //SILEX:info
                //totalCount must be there too to get the pagination in GridView
                'totalCount' => $this->totalCount
                //\SILEX:info
            ]);
        }
    }
    
    /**
     * transform the json into array
     * @param json jsonList
     * @return array
     */
    private function jsonListOfArraysToArray($jsonList) {
        $toReturn = [];
        if ($jsonList !== null) {
            foreach ($jsonList as $value) {
                $toReturn[] = $value;
            }
        }
        return $toReturn;
    }
}

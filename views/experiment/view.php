<?php
//**********************************************************************************************
//                                       view.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  February, 2017
// Subject: implements the view page for an Experiment
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiExperimentModel */

$this->title = $model->uri;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if (Yii::$app->session['isAdmin'] || $this->params['canUpdate']) {
            echo Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']);
        }
        ?>
        <?= Html::a(Yii::t('app', 'Add Document'), ['document/create', 'concernedItem' => $model->uri], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']) ?>
        <?= Html::a(Yii::t('app', 'Map Visualization'), 
                ['layer/view', 'objectURI' => $model->uri, 'objectType' => 'http://www.phenome-fppn.fr/vocabulary/2017#Experiment', 'depth' => 'true', 'generateFile' => 'false'], ['class' => 'btn btn-info']) ?>
        <?php if (Yii::$app->session['isAdmin']) {
            echo Html::a(Yii::t('app', 'Generate Map'), 
                ['layer/view', 'objectURI' => $model->uri, 'objectType' => 'http://www.phenome-fppn.fr/vocabulary/2017#Experiment', 'depth' => 'true', 'generateFile' => 'true'], ['class' => 'btn btn-success']);
            }
         ?>
    </p>

    <?php
    
    $attributes;
    
    if (Yii::$app->session['isAdmin']) {
        $attributes = [
            'uri',
            'alias',
            [
              'attribute' => 'projects',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->projects) > 0) {
                    foreach($model->projects as $project) {
                        $toReturn .= Html::a($project["acronyme"], ['project/view', 'id' => $project["uri"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
            [
              'attribute' => 'startDate',
              'format' => 'raw',
              'value' => function($model) {
                return date_format(date_create($model->startDate), 'jS F Y');
              }
            ],
            [
              'attribute' => 'endDate',
              'format' => 'raw',
              'value' => function($model) {
                return date_format(date_create($model->endDate), 'jS F Y');
              }
            ],
            'field',
            'campaign',
            'place',
            [
              'attribute' => 'scientificSupervisorContact',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->scientificSupervisorContacts) > 0) {
                    foreach($model->scientificSupervisorContacts as $scientificSupervisor) {
                        $toReturn .= Html::a($scientificSupervisor["firstName"] . " " . $scientificSupervisor["familyName"], ['user/view', 'id' => $scientificSupervisor["email"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
                    [
              'attribute' => 'technicalSupervisorContact',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->technicalSupervisorContacts) > 0) {
                    foreach($model->technicalSupervisorContacts as $technicalSupervisorContact) {
                        $toReturn .= Html::a($technicalSupervisorContact["firstName"] . " " . $technicalSupervisorContact["familyName"], ['user/view', 'id' => $technicalSupervisorContact["email"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
            'cropSpecies',
            'objective',
            //'groups',
            'keywords',
            'comment:ntext',
             [
              'attribute' => 'groups',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->groups) > 0) {
                    foreach($model->groups as $group) {
                        $toReturn .= Html::a($group["name"], ['group/view', 'id' => $group["uri"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
        ];
    } else {
        $attributes = [
            'uri',
            'alias',
            [
              'attribute' => 'projects',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->projects) > 0) {
                    foreach($model->projects as $project) {
                        $toReturn .= Html::a($project["acronyme"], ['project/view', 'id' => $project["uri"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
            'startDate',
            'endDate',
            'field',
            'campaign',
            'place',
            [
              'attribute' => 'scientificSupervisorContact',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->scientificSupervisorContacts) > 0) {
                    foreach($model->scientificSupervisorContacts as $scientificSupervisor) {
                        $toReturn .= Html::a($scientificSupervisor["firstName"] . " " . $scientificSupervisor["familyName"], ['user/view', 'id' => $scientificSupervisor["email"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
                    [
              'attribute' => 'technicalSupervisorContact',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->technicalSupervisorContacts) > 0) {
                    foreach($model->technicalSupervisorContacts as $technicalSupervisorContact) {
                        $toReturn .= Html::a($technicalSupervisorContact["firstName"] . " " . $technicalSupervisorContact["familyName"], ['user/view', 'id' => $technicalSupervisorContact["email"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
            'cropSpecies',
            'objective',
            //'groups',
            'keywords',
            'comment:ntext',
        ];
    }
     
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]); ?>
    
     <?php if ($dataDocumentsProvider->getCount() > 0) {
            echo "<h3>" . Yii::t('app', 'Linked Documents') . "</h3>";
            echo GridView::widget([
                'dataProvider' => $dataDocumentsProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'title',
                    'creator',
                    'creationDate',
                    'language',
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                                ['document/view', 'id' => $model->uri]); 
                            },
                        ]
                    ],
                ]
            ]);
          } 
    ?>
    
    <?php if ($dataAgronomicalObjectsProvider->getCount() > 0) {
        echo "<h3>" . Yii::t('app', 'Linked Agronomical Objects') . "</h3>";
            echo GridView::widget([
                'dataProvider' => $dataAgronomicalObjectsProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'uri',
                    'alias',
                    [
                        'attribute' => 'typeAgronomicalObject',
                        'format' => 'raw',
                        'value' => function($model, $key, $index) {
                            return explode("#", $model->typeAgronomicalObject)[1];
                        }
                    ], 
                ]
            ]);
    }    
    ?>
</div>

<?php

//**********************************************************************************************
//                                       WSDocumentModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: March 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June, 2017
// Subject: Corresponds to the documents service - extends WSModel
//***********************************************************************************************

namespace app\models\wsModels;

use GuzzleHttp\Client;

include_once '../config/web_services.php';

/**
 * encapsulate the documents service
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSDocumentModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * the exchange format to send documents to the web service. 
     * @var string
     */
    const OCTET_STREAM = "application/octet-stream";
    
    /**
     * initialize access to the agronomical objects service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "documents");
    }

    /**
     * Allows to handle differents errors. 
     * @see \openSILEX\guzzleClientPHP\WSModel::errorMessage()
     * @param int $errorCode error code
     * @param json $errorBody informations about the error 
     *                          (and the message error)
     * @return string the error message
     */
    protected function errorMessage($errorCode, $errorBody) {
        parent::errorMessage($errorCode, $errorBody);
    }
    
    /**
     * Post file to web service. 
     * Redefinition of the \openSILEX\guzzleClientPHP\WSModel::postFile(), adapted to files
     * upload.
     * @see \openSILEX\guzzleClientPHP\WSModel::postFile()
     * @param string $sessionToken
     * @param file $file
     * @param string $requestURL
     * @return mixed the result of the action,
     *               "token" if the token given isn't valid
     */
    public function postFile($sessionToken, $file, $requestURL) {
        $this->client = new Client(['base_uri' => $this->basePath,
                                    'headers' => ['Accept' => REQUEST_CONTENT_TYPE,
                                                  'Content-Type' => self::OCTET_STREAM,
                                                  'Authorization' => "Bearer " ]]);
        
         try {
            $requestRes = $this->client->request(
                    'POST', 
                    $requestURL,
                    ['headers' => ['Authorization' => "Bearer " . $sessionToken],
                     'body' => $file ]
            );

        } catch (\GuzzleHttp\Exception\ClientException $e) {    
            return $this->errorMessage($e->getResponse()->getStatusCode(), 
                                       json_decode($e->getResponse()->getBody()));
        } catch (\GuzzleHttp\Exception\ConnectException $e) { //Server connection error
            return WEB_SERVICE_CONNECTION_ERROR_MESSAGE;
        } catch (\GuzzleHttp\Exception $e) {
            return "Other exception : " . $e->getResponse()->getBody();
        }
        
        $res = json_decode($requestRes->getBody());
        
        if (isset($res->{WSConstants::TOKEN})) {
            return WSConstants::TOKEN;
        } else {        
            return $res; 
        }
    }
    
    /**
     * call to the web service to get the types of documents
     * @param string $sessionToken
     * @return mixed array with the types if no error,
     *               array with the error if error
     */
    public function getTypes($sessionToken) {
        $subService = "/types";
        $requestRes = $this->get($sessionToken, $subService);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * get a file by the document URI
     * @param string $sessionToken connection token
     * @param string $documentURI identifiant of the file wanted
     * @param string $format extension of the file
     * @return mixed url to the file,
     *               array with the error
     */
    public function getFileByURI($sessionToken, $documentURI, $format) {
        $this->client = new Client(['base_uri' => $this->basePath,
                                    'headers' => [
                                    'Accept' => self::OCTET_STREAM,
                                    'Content-Type' => REQUEST_CONTENT_TYPE,
                                    'Authorization' => "Bearer " ]]);
        
        try {
                $requestRes = $this->client->request(
                    'GET', 
                    $this->serviceName . "/" . urlencode($documentURI),
                    ['headers' => [
                     'Authorization' => "Bearer " . $sessionToken],
                      'sink' => \config::path()['documentsUrl'].'file.' . $format
                        ]);
            
            
        } catch (\GuzzleHttp\Exception\ClientException $e) { 
            return $this->errorMessage($e->getResponse()->getStatusCode(), 
                                       json_decode($e->getResponse()->getBody()));
            
        } catch (\GuzzleHttp\Exception\ConnectException $e) { //Server connection error
            return WEB_SERVICE_CONNECTION_ERROR_MESSAGE;
        }
        
        //SILEX:todo
        //manage errors returned by the service
        //\SILEX:todo
        if (is_array($requestRes)) {
            return $requestRes;
        } else {
            return \config::path()['documentsUrl'].'file.' . $format;
        }
    }
}

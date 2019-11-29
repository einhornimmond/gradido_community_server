<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

use Model\Transactions\Transaction;
/*!
 * @author: Dario Rekowski#
 * 
 * @date: 03.11.2019
 * 
 * @desc: Handle requests from other server put or request transaction
 */

class TransactionJsonRequestHandlerController extends AppController {
  
    public function initialize()
    {
        parent::initialize();
        //$this->Auth->allow(['add', 'edit']);
        $this->Auth->allow('index');
    }
    
  
    public function index()
    {
        if($this->request->is('post')) {
          $jsonData = $this->request->input('json_decode');
          //var_dump($jsonData);
          if($jsonData == NULL || !isset($jsonData->method) || !isset($jsonData->transaction)) {
            return $this->returnJson(['state' => 'error', 'msg' => 'parameter error']);
          }
          $method = $jsonData->method;
          switch($method) {
            case 'putTransaction': return $this->putTransaction($jsonData->transaction);
          }
          return $this->returnJson(['state' => 'error', 'msg' => 'unknown method', 'details' => $method]);
        }
        return $this->returnJson(['state' => 'error', 'msg' => 'no post']);
    }
  
    private function putTransaction($transactionBase64) {
      $transaction = new Transaction($transactionBase64);
      if($transaction->hasErrors()) {
        return $this->returnJson(['state' => 'error', 'msg' => 'error parsing transaction', 'details' => $transaction->getErrors()]);
      }
      if(!$transaction->validate()) {
        return $this->returnJsonSaveError($transaction, ['state' => 'error', 'msg' => 'error validate transaction', 'details' => $transaction->getErrors()]);
      }
      
      if ($transaction->save()) {
        // success
        return $this->returnJson(['state' => 'success']);
      } else {
        return $this->returnJsonSaveError($transaction, [
            'state' => 'error', 
            'msg' => 'error saving transaction in db', 
            'details' => json_encode($transaction->getErrors())
        ]);
      }
      
      return $this->returnJson(['state' => 'success']);
    }
    
    
    private function returnJsonSaveError($transaction, $errorArray) {
      $json = json_encode($errorArray);
      $stateUserTable = TableRegistry::getTableLocator()->get('StateUsers');
      $pub = $transaction->getFirstPublic();
      $stateUserQuery = $stateUserTable
                            ->find('all')
                            ->where(['public_key' => $pub])
                            ->contain(false);
      if($stateUserQuery->count() == 1) {
        $stateErrorsTable = TableRegistry::getTableLocator()->get('StateErrors');
        $stateErrorEntity = $stateErrorsTable->newEntity();
        $stateErrorEntity->state_user_id = $stateUserQuery->first()->id;
        $stateErrorEntity->transaction_type_id = $transaction->getTransactionBody()->getTransactionTypeId();
        $stateErrorEntity->message_json = $json;
        $stateErrorsTable->save($stateErrorEntity);
      }
      return $this->returnJsonEncoded($json);
    }
    
}
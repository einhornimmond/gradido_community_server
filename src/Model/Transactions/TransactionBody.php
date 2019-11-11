<?php

namespace Model\Transactions;

use Cake\ORM\TableRegistry;

class TransactionBody extends TransactionBase {
  private $mProtoTransactionBody = null;
  private $mSpecificTransaction = null;
  private $mTransactionID = 0;
  private $transactionTypeId = 0;
  
  public function __construct($bodyBytes) {
    $this->mProtoTransactionBody = new \Model\Messages\Gradido\TransactionBody();
    try {
      $this->mProtoTransactionBody->mergeFromString($bodyBytes);
      // cannot catch Exception with cakePHP, I don't know why
    } catch(\Google\Protobuf\Internal\GPBDecodeException $e) {
      //var_dump($e);
      $this->addError('TransactionBody', $e->getMessage());
      return;
    }
    
    switch($this->mProtoTransactionBody->getData()) {
      case 'creation' : $this->mSpecificTransaction = new TransactionCreation($this->mProtoTransactionBody->getCreation()); break;
      case 'transfer' : $this->mSpecificTransaction = new TransactionTransfer($this->mProtoTransactionBody->getTransfer()); break;
    }
  }
  
  public function validate($sigPairs) {
    
    // transaction type id
    $transactionTypesTable = TableRegistry::getTableLocator()->get('transaction_types');

    $typeName = $this->getTransactionTypeName();
    $transactionType = $transactionTypesTable->find('all')->where(['name' => $typeName])->select(['id'])->first();
    if($transactionType == NULL) {
      $this->addError('TransactionBody::validate', 'zero type id for type: ' . $typeName);
      return false;
    }
    $this->transactionTypeId = $transactionType->id;
      
    // check if creation time is in the past
    if($this->mProtoTransactionBody->getCreated()->getSeconds() > time()) {
      $this->addError('TransactionBody::validate', 'Transaction were created in the past!');
      return false;
    }
    if(!$this->mSpecificTransaction->validate($sigPairs)) {
      $this->addErrors($this->mSpecificTransaction->getErrors());
      return false;
    }
    
    
      
    return true;
  }
  
  public function getSpecificTransaction() {
    return $this->mSpecificTransaction;
  }
  
  public function getMemo() {
    return $this->mProtoTransactionBody->getMemo();
  }
  
  public function getTransactionTypeName()
  {
    return $this->mProtoTransactionBody->getData(); 
  }
  
  public function save($firstPublic) {
      $transactionsTable = TableRegistry::getTableLocator()->get('transactions');
      $transactionEntity = $transactionsTable->newEntity();
      
      
      $transactionEntity->transaction_type_id = $this->transactionTypeId;
      $transactionEntity->memo = $this->getMemo();
      
      if ($transactionsTable->save($transactionEntity)) {
        // success
        $this->mTransactionID = $transactionEntity->id;
        if(!$this->mSpecificTransaction->save($transactionEntity->id, $firstPublic)) {
          $this->addErrors($this->mSpecificTransaction->getErrors());
          return false;
        }  
      } else {
        $this->addError('TransactionBody::save', 'error saving transaction with: ' . json_encode($transactionEntity->getError()));
        return false;
      }
      return true;
  }
  
  public function getTransactionID() {
    return $this->mTransactionID;
  }
  
  public function getTransactionTypeId() {
    return $this->transactionTypeId;
  }
  
}

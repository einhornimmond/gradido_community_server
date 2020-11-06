<?php


namespace Model\Transactions;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

/*
 * @author: Dario Rekowski
 * 
 * @date: 2020-11-05
 * 
 * @brief: Class for reading records from gradido node (pauls json dump format) and translate into community server transaction db format
 * 
 * 
 */

class Signature
{
  private $signature;
  private $publicKey;
  
  public function __construct($signature, $pubkey)
  {
    $this->signature = $signature;
    $this->publicKey = $pubkey;
  }
  
  public function finalize($transactionId) 
  {
    $signaturesTable = TableRegistry::getTableLocator()->get('TransactionSignatures');
    $entity = $signaturesTable->newEntity();
    $entity->transaction_id = $transactionId;
    if(count($this->signature) != 128) {
      return ['state' => 'error', 'msg' => 'invalid signature size', 'details' => count($this->signature)];
    }
    if(count($this->publicKey) != 64) {
      return ['state' => 'error', 'msg' => 'invalid pubkey size', 'details' => count($this->publicKey)];
    }
    if(!preg_match('/^[0-9a-fA-F]*$/', $this->signature)) {
      return ['state' => 'error', 'msg' => 'signature isn\'t in hex format'];
    }
    if(!preg_match('/^[0-9a-fA-F]*$/', $this->publicKey)) {
      return ['state' => 'error', 'msg' => 'publicKey isn\'t in hex format'];
    }
    $entity->signature = hex2bin($this->signature);
    $entity->pubkey = hex2bin($this->publicKey);
    
    if(!$signaturesTable->save($entity)) {
      return ['state' => 'error', 'msg' => 'error saving signature', 'details' => $entity->getErrors()];
    }
    return true;
  }
}


class ManageNodeGroupAdd
{
  /*
    "add_user": {
        "user\": " << user << ",
     },
    OR
    
    "move_user_inbound|move_user_outbound": {
          "user": " << user << ",
          "other_group": " << other_group << ",
          "paired_transaction_id": {
            "seconds": << ts.seconds  <<,
            "nanos": << ts.nanos 
          }
    },
   
   */
  
  private $user_pubkey;
  private $other_group = '';
  private $remove_from_group = false;
  
  public function __construct($data)
  {
    $this->user_pubkey = $data['user'];
    if(isset($data['other_group'])) {
      $this->other_group = $data['other_group'];
    }
  }
  
  public function finalize($transactionId, $received) 
  {
    $transactionGroupAddadressTable = TableRegistry::getTableLocator()->get('TransactionGroupAddaddress'); 
    $stateGroupAddresses = TableRegistry::getTableLocator()->get('StateGroupAddresses');
    $transactionGroupEntity = $transactionGroupAddadressTable->newEntity();
    $transactionGroupEntity->transaction_id = $transactionId;
    if(count($this->user_pubkey) != 64) {
      return ['state' => 'error', 'msg' => 'invalid size user pubkey', 'details' => count($this->user_pubkey)];
    }
    if(!preg_match('/^[0-9a-fA-F]*$/', $this->user_pubkey)) {
      return ['state' => 'error', 'msg' => 'user_pubkey isn\'t in hex format'];
    }
    
    $transactionGroupEntity->public_key = hex2bin($this->user_pubkey);
    $transactionGroupEntity->remove_from_group = $this->remove_from_group;
    if(!$transactionGroupAddadressTable->save($transactionGroupEntity)) {
      return ['state' => 'error', 'msg' => 'error saving TransactionGroupAddaddress Entity', 'details' => $transactionGroupEntity->getErrors()];
    }
    $userPubkeyBin = hex2bin($this->user_pubkey);
    if($this->remove_from_group) {
      $stateGroup_query = $stateGroupAddresses->find('all')->where(['public_key' => hex2bin($this->user_pubkey)]);
      if(!$stateGroup_query->isEmpty()) {
        $stateGroupAddresses->delete($stateGroup_query->first());
      }
    } else {
      $stateGroupAddressesEntity = $stateGroupAddresses->newEntity();
      $stateGroupAddressesEntity->group_id = 1;
      $stateGroupAddressesEntity->public_key = $userPubkeyBin;
      $stateGroupAddressesEntity->address_type_id = 1;
      if(!$stateGroupAddresses->save($stateGroupAddressesEntity)) {
        return ['state' => 'error', 'msg' => 'error saving state group addresses entity', 'details' => $stateGroupAddressesEntity->getErrors()];
      }
    }
    
    return true;
  }
  
  public function setRemoveFromGroup($removeFromGroup) {
    $this->remove_from_group = $removeFromGroup;
  }
}

class GradidoModifieUserBalance
{
    
    public function getUserId($userPublicKey)
    {
        $stateUsersTable = TableRegistry::getTableLocator()->get('StateUsers');
        $stateUser = $stateUsersTable->find('all')->where(['public_key' => hex2bin($userPublicKey)]);
        if($stateUser->isEmpty()) {
          return ['state' => 'error', 'msg' => 'couldn\'t find user via public key'];
        }
        return $stateUser->first()->id;
    }
    
    public function updateBalance($newBalance, $recordDate, $userId)
    {
      $stateBalancesTable = TableRegistry::getTableLocator()->get('StateBalances');
      $stateBalanceQuery = $stateBalancesTable->find('all')->where(['state_user_id' => $userId]);
      $entity = null;
     
      if(!$stateBalanceQuery->isEmpty()) {
        $entity = $stateBalanceQuery->first();
        if($entity->record_date != NULL && $entity->record_date > $recordDate) {
          return false;
        }
      } else {
        $entity = $stateBalancesTable->newEntity();
        $entity->state_user_id = $userId;
      }
      $entity->record_date = $recordDate;
      $entity->amount = $newBalance;
      if(!$stateBalancesTable->save($entity)) {
        return ['state' => 'error', 'msg' => 'error saving state balance', 'details' => $entity->getErrors()];
      }
      return true;
    }
}

class GradidoCreation extends GradidoModifieUserBalance
{
  /*
   * "gradido_creation": {
           "user": " << user << ", 
           "new_balance": << v.new_balance << ,
           "prev_transfer_rec_num": << v.prev_transfer_rec_num <<,
           "amount": << v.amount <<
      }
   */
  private $userPubkey;
  private $amount;
  private $targetDate; // seems currently not in node server implementet, use hedera date until it is implemented
  private $new_balance;
  
  public function __construct($data) 
  {
    $this->userPubkey = $data['user'];
    $this->amount = $data['amount'];
    $this->new_balance = $data['new_balance'];
    //$this->targetDate = $received;
  }
  
  public function finalize($transactionId, $received) 
  {
    // TODO: don't use, after node server transmit correct date
    $this->targetDate = $received;
    
    $transactionCreationTable = TableRegistry::getTableLocator()->get('TransactionCreations'); 
    
    
    $state_user_id = $this->getUserId($this->userPubkey);
    if(!is_int($state_user_id)) {
      return $state_user_id;
    }
    
    $entity = $transactionCreationTable->newEntity();
    $entity->transaction_id = $transactionId;
    $entity->amount = $this->amount;
    $entity->target_date = $this->targetDate;
    $entity->state_user_id = $state_user_id;
    
    if(!$transactionCreationTable->save($entity)) {
      return ['state' => 'error', 'msg' => 'error saving create transaction', 'details' => $entity->getErrors()];
    }
    
    $balance_result = $this->updateBalance($this->new_balance, $received, $state_user_id);
    if(is_array($balance_result)) {
      return $balance_result;
    }
    
    return true;
  }
  
}

class GradidoTransfer extends GradidoModifieUserBalance
{
  /*
      "local_transfer|inbound_transfer|outbound_transfer": {
            "sender": {
              "user": " << sender << ",
              "new_balance":  << tt.sender.new_balance << ,
              "prev_transfer_rec_num": << tt.sender.prev_transfer_rec_num <<
            },
            "receiver": {
              "user": " << receiver << ",
              "new_balance": << tt.receiver.new_balance << ,
              "prev_transfer_rec_num": << tt.receiver.prev_transfer_rec_num <<
            },
            "amount":  << tt.amount << 
      },
   * */
  private $amount;
  private $sender_new_balance;
  private $sender_pubkey;
  
  private $receiver_pubkey;
  private $receiver_new_balance;
  
  public function __construct($data)
  {
    $this->amount = $data['amount'];
    
    $sender = $data['sender'];
    $this->sender_pubkey = $sender['user'];
    $this->sender_new_balance = $sender['new_balance'];
    
    $receiver = $data['receiver'];
    $this->receiver_pubkey = $receiver['user'];
    $this->receiver_new_balance = $receiver['new_balance'];
    
  }
  
  public function finalize($transactionId, $received) 
  {
    $transactionTransferTable = TableRegistry::getTableLocator()->get('TransactionSendCoins');
    if(count($this->sender_pubkey) != 64) {
      return ['state' => 'error', 'msg' => 'invalid size sender pubkey', 'details' => count($this->user_pubkey)];
    }
    if(!preg_match('/^[0-9a-fA-F]*$/', $this->sender_pubkey)) {
      return ['state' => 'error', 'msg' => 'sender_pubkey isn\'t in hex format'];
    }
    if(count($this->receiver_pubkey) != 64) {
      return ['state' => 'error', 'msg' => 'invalid size receiver pubkey', 'details' => count($this->user_pubkey)];
    }
    if(!preg_match('/^[0-9a-fA-F]*$/', $this->receiver_pubkey)) {
      return ['state' => 'error', 'msg' => 'receiver_pubkey isn\'t in hex format'];
    }
    
    $sender_id = $this->getUserId($this->sender_pubkey);
    $receiver_id = $this->getUserId($this->receiver_pubkey);
    if(is_array($sender_id) && is_array($receiver_id)) {
      return ['state' => 'error', 'msg' => 'neither sender or receiver known'];
    }
    $transferEntity = $transactionTransferTable->newEntity();
    $transferEntity->transaction_id = $transactionId;
    $transferEntity->sender_public_key = hex2bin($this->sender_pubkey);
    $transferEntity->receiver_public_key = hex2bin($this->receiver_pubkey);
    $transferEntity->amount = $this->amount;
    $transferEntity->sender_final_balance = $this->sender_new_balance;
    
    if(is_int($sender_id) && $sender_id > 0) {
      $transferEntity->state_user_id = $sender_id;
      $balance_result = $this->updateBalance($this->sender_new_balance, $received, $sender_id);
      if(is_array($balance_result)) {
        return $balance_result;
      }
    }
    if(is_int($receiver_id) && $receiver_id > 0) {
      $transferEntity->receiver_user_id = $receiver_id;
      $balance_result = $this->updateBalance($this->receiver_new_balance, $received, $receiver_id);
      if(is_array($balance_result)) {
        return $balance_result;
      }
    }
    
    if(!$transactionTransferTable->save($transferEntity)) {
      return ['state' => 'error', 'msg' => 'error saving transaction send coins entity', 'details' => $transferEntity->getErrors()];
    }
    
    return true;
  }
}




class Record
{
   private $sequenceNumber = 0;
   private $transactionType = '';
   private $memo = '';
   private $signatures = [];
   private $received;
   private $transactionObj = null;
   private $result;
   private $partCount = 0;
   
   public function __construct()
   {
     
   }
     
   
   public function parseRecord($json) {
     switch($json['record_type']) {
       case 'GRADIDO_TRANSACTION':
         return $this->parseTransaction($json['transaction']);
       case 'MEMO':
         $this->memo = $json['memo'];
         return true;
       case 'SIGNATURES':
         return $this->parseSignatures($json['signature']);
       case 'STRUCTURALLY_BAD_MESSAGE':
       case 'RAW_MESSAGE':
       case 'BLANK':
          return false;
     }
   }
   
   /*! 
    * \brief save data parts in db
    */
   public function finalize() 
   {
     $transactionTypesTable = TableRegistry::getTableLocator()->get('TransactionTypes');
     $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');
             
     $transactionTypeName = $this->nodeTransactionTypeToDBTransactionType($this->transactionType);
     $transactionTypeResults = $transactionTypesTable->find('all')->where(['name' => $transactionTypeName]);
     if($transactionTypeResults->isEmpty()) {
       return [
           'state' => 'error', 'msg' => 'transaction type not found', 
           'details' => ['nodeType' => $this->transactionType, 'dbType' => $transactionTypeName]
       ];
     }
     if(!$this->transactionObj) {
       return ['state' => 'error', 'msg' => 'transaction obj is null'];
     }
     if($this->sequenceNumber <= 0) {
       return ['state' => 'error', 'msg' => 'sequence number invalid', 'details' => $this->sequenceNumber];
     }
     $transactionExistResult = $transactionsTable->find('all')->where(['id' => $this->sequenceNumber]);
     if(!$transactionExistResult->isEmpty()) {
       return ['state' => 'warning', 'msg' => 'transaction already exist in db', 'details' => $this->sequenceNumber];
     }
     $newTransaction = $transactionsTable->newEntity();
     $newTransaction->id = $this->sequenceNumber;
     $newTransaction->transaction_type_id = $transactionTypeResults->first()->id;
     $newTransaction->memo = $this->memo;
     $newTransaction->received = $this->received;
     
     if(!$transactionsTable->save($newTransaction)) {
       return ['state' => 'error', 'msg' => 'error saving transaction', 'details' => $newTransaction->getErrors()];
     }
     foreach($this->signatures as $sign) {
       $sign_result = $sign->finalize($this->sequenceNumber);
       iF($sign_result != true) {
         return ['state' => 'error', 'msg', 'error finalizing signature', 'details' => $sign_result];
       }
     }
     $transaction_obj_result = $this->transactionObj->finalize($newTransaction->id, $this->received);
     if($transaction_obj_result != true) {
       return ['state' => 'error', 'msg' => 'error finalizing transaction object', 'details' => $transaction_obj_result];
     }
     return true;
     
   }
   
   private function nodeTransactionTypeToDBTransactionType($nodeTransactionType)
   {
     switch($nodeTransactionType) {
       case 'GRADIDO_CREATION': 
         return 'creation';
         
       case 'MOVE_USER_INBOUND':
       case 'ADD_USER': 
         return 'group add member';
         
       case 'MOVE_USER_OUTBOUND': 
         return 'group remove member';
         
       case 'LOCAL_TRANSFER':
       case 'INBOUND_TRANSFER':
       case 'OUTBOUND_TRANSFER':
         return 'transfer';
     }
     return 'unknown';
   }
   
   private function parseSignatures($signaturesArray) 
   {
      foreach($signaturesArray as $sign) {
        $this->signatures[] = new Signature($sign['signature'], $sign['pubkey']);
      }
      return true;
   }
   
   private function parseTransaction($data) 
   {
      $this->transactionType = $data['transaction_type'];
      $sign = $data['signature'];
      $this->signatures[] = new Signature($sign['signature'], $sign['pubkey']);
      
      $hedera = $data['hedera_transaction'];
      $this->sequenceNumber = $hedera['sequenceNumber'];
      $this->received = Time::createFromTimestamp($hedera['consensusTimestamp']['seconds']);
      
      $field_index = '';
      $class_name = '';
      
      $removeFromGroup = false;
      switch($this->transactionType)
      {
        case 'GRADIDO_CREATION': $field_index = 'gradido_creation'; $class_name = 'GradidoCreation'; break;
        case 'ADD_USER': $field_index = 'add_user'; $class_name = 'ManageNodeGroupAdd'; break;
        case 'MOVE_USER_INBOUND': $field_index = 'move_user_inbound'; $class_name = 'ManageNodeGroupAdd'; break;
        case 'MOVE_USER_OUTBOUND':  $field_index = 'move_user_outbound'; $class_name = 'ManageNodeGroupAdd'; $removeFromGroup = true; break;
        case 'LOCAL_TRANSFER': $field_index = 'local_transfer'; $class_name = 'GradidoTransfer'; break;
        case 'INBOUND_TRANSFER': $field_index = 'inbound_transfer'; $class_name = 'GradidoTransfer'; break;
        case 'OUTBOUND_TRANSFER': $field_index = 'outbound_transfer'; $class_name = 'GradidoTransfer'; break;
      }
      if($class_name == '' || $field_index == '') {
        return ['state' => 'error', 'msg' => 'node transaction type unknown', 'details' => $this->transactionType];
      }
      $this->transactionObj = new $class_name($data[$field_index]);
      if($class_name == 'ManageNodeGroupAdd') {
        $this->transactionObj->setRemoveFromGroup($removeFromGroup);
      }
      
      $this->result = $data['result'];
      $this->partCount = intval($data['parts']);
      $this->memo = $data['memo'];
      return true;
   }
   
   public function getSequenceNumber() {
     return $this->sequenceNumber;
   }
   public function getPartCount() {
     return $this->partCount;
   }
           
}
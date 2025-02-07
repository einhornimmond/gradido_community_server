<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TransactionStates Model
 *
 * @property \App\Model\Table\TransactionsTable&\Cake\ORM\Association\HasMany $Transactions
 *
 * @method \App\Model\Entity\TransactionState get($primaryKey, $options = [])
 * @method \App\Model\Entity\TransactionState newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TransactionState[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TransactionState|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TransactionState saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TransactionState patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TransactionState[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TransactionState findOrCreate($search, callable $callback = null, $options = [])
 */
class TransactionStatesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('transaction_states');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Transactions', [
            'foreignKey' => 'transaction_state_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('text')
            ->maxLength('text', 255)
            ->requirePresence('text', 'create')
            ->notEmptyString('text');

        return $validator;
    }
}

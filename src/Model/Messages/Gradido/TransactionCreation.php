<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: TransactionCreation.proto

namespace Model\Messages\Gradido;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * need signature from group admin or 
 * percent of group users another than the receiver
 *
 * Generated from protobuf message <code>model.messages.gradido.TransactionCreation</code>
 */
class TransactionCreation extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.model.messages.gradido.ReceiverAmount receiverAmount = 1;</code>
     */
    private $receiverAmount = null;
    /**
     * Generated from protobuf field <code>sint32 ident_hash = 2;</code>
     */
    private $ident_hash = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Model\Messages\Gradido\ReceiverAmount $receiverAmount
     *     @type int $ident_hash
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\TransactionCreation::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.model.messages.gradido.ReceiverAmount receiverAmount = 1;</code>
     * @return \Model\Messages\Gradido\ReceiverAmount
     */
    public function getReceiverAmount()
    {
        return $this->receiverAmount;
    }

    /**
     * Generated from protobuf field <code>.model.messages.gradido.ReceiverAmount receiverAmount = 1;</code>
     * @param \Model\Messages\Gradido\ReceiverAmount $var
     * @return $this
     */
    public function setReceiverAmount($var)
    {
        GPBUtil::checkMessage($var, \Model\Messages\Gradido\ReceiverAmount::class);
        $this->receiverAmount = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>sint32 ident_hash = 2;</code>
     * @return int
     */
    public function getIdentHash()
    {
        return $this->ident_hash;
    }

    /**
     * Generated from protobuf field <code>sint32 ident_hash = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setIdentHash($var)
    {
        GPBUtil::checkInt32($var);
        $this->ident_hash = $var;

        return $this;
    }

}


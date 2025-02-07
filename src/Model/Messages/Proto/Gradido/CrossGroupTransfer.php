<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: gradido/GradidoTransfer.proto

namespace Proto\Gradido;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.gradido.CrossGroupTransfer</code>
 */
class CrossGroupTransfer extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.proto.gradido.LocalTransfer transfer = 1;</code>
     */
    protected $transfer = null;
    /**
     * Generated from protobuf field <code>string other_group = 3;</code>
     */
    protected $other_group = '';
    /**
     * this matches related OutboundTransfer.paired_transaction_id
     *
     * Generated from protobuf field <code>.proto.gradido.Timestamp paired_transaction_id = 4;</code>
     */
    protected $paired_transaction_id = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Proto\Gradido\LocalTransfer $transfer
     *     @type string $other_group
     *     @type \Proto\Gradido\Timestamp $paired_transaction_id
     *           this matches related OutboundTransfer.paired_transaction_id
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Gradido\GradidoTransfer::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.proto.gradido.LocalTransfer transfer = 1;</code>
     * @return \Proto\Gradido\LocalTransfer|null
     */
    public function getTransfer()
    {
        return isset($this->transfer) ? $this->transfer : null;
    }

    public function hasTransfer()
    {
        return isset($this->transfer);
    }

    public function clearTransfer()
    {
        unset($this->transfer);
    }

    /**
     * Generated from protobuf field <code>.proto.gradido.LocalTransfer transfer = 1;</code>
     * @param \Proto\Gradido\LocalTransfer $var
     * @return $this
     */
    public function setTransfer($var)
    {
        GPBUtil::checkMessage($var, \Proto\Gradido\LocalTransfer::class);
        $this->transfer = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string other_group = 3;</code>
     * @return string
     */
    public function getOtherGroup()
    {
        return $this->other_group;
    }

    /**
     * Generated from protobuf field <code>string other_group = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setOtherGroup($var)
    {
        GPBUtil::checkString($var, True);
        $this->other_group = $var;

        return $this;
    }

    /**
     * this matches related OutboundTransfer.paired_transaction_id
     *
     * Generated from protobuf field <code>.proto.gradido.Timestamp paired_transaction_id = 4;</code>
     * @return \Proto\Gradido\Timestamp|null
     */
    public function getPairedTransactionId()
    {
        return isset($this->paired_transaction_id) ? $this->paired_transaction_id : null;
    }

    public function hasPairedTransactionId()
    {
        return isset($this->paired_transaction_id);
    }

    public function clearPairedTransactionId()
    {
        unset($this->paired_transaction_id);
    }

    /**
     * this matches related OutboundTransfer.paired_transaction_id
     *
     * Generated from protobuf field <code>.proto.gradido.Timestamp paired_transaction_id = 4;</code>
     * @param \Proto\Gradido\Timestamp $var
     * @return $this
     */
    public function setPairedTransactionId($var)
    {
        GPBUtil::checkMessage($var, \Proto\Gradido\Timestamp::class);
        $this->paired_transaction_id = $var;

        return $this;
    }

}


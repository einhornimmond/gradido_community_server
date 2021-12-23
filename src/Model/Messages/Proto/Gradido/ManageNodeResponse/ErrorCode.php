<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: gradido/ManageNodeRequest.proto

namespace Proto\Gradido\ManageNodeResponse;

use UnexpectedValueException;

/**
 * Protobuf type <code>proto.gradido.ManageNodeResponse.ErrorCode</code>
 */
class ErrorCode
{
    /**
     * Generated from protobuf enum <code>INVALID_BODY = 0;</code>
     */
    const INVALID_BODY = 0;
    /**
     * Generated from protobuf enum <code>INVALID_SIGNATURE = 1;</code>
     */
    const INVALID_SIGNATURE = 1;
    /**
     * Generated from protobuf enum <code>SIGNER_NOT_KNOWN = 2;</code>
     */
    const SIGNER_NOT_KNOWN = 2;
    /**
     * Generated from protobuf enum <code>GROUP_ALIAS_ALREADY_EXIST = 3;</code>
     */
    const GROUP_ALIAS_ALREADY_EXIST = 3;

    private static $valueToName = [
        self::INVALID_BODY => 'INVALID_BODY',
        self::INVALID_SIGNATURE => 'INVALID_SIGNATURE',
        self::SIGNER_NOT_KNOWN => 'SIGNER_NOT_KNOWN',
        self::GROUP_ALIAS_ALREADY_EXIST => 'GROUP_ALIAS_ALREADY_EXIST',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorCode::class, \Proto\Gradido\ManageNodeResponse_ErrorCode::class);

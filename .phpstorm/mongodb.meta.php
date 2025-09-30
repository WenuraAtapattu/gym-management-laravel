<?php

namespace PHPSTORM_META {

    // MongoDB Driver
    override(\MongoDB\Driver\Manager::__construct(), type(0));
    override(\MongoDB\Driver\Command::__construct(), type(0));
    
    // MongoDB Exceptions
    override(\MongoDB\Driver\Exception\RuntimeException::class, type(0));
    override(\MongoDB\Driver\Exception\ConnectionTimeoutException::class, type(0));
    override(\MongoDB\Driver\Exception\AuthenticationException::class, type(0));
    override(\MongoDB\Driver\Exception\SSLConnectionException::class, type(0));
    
    // MongoDB Types
    registerArgumentsSet('mongodb_types',
        'MongoDB\Driver\Manager',
        'MongoDB\Driver\Command',
        'MongoDB\Driver\Cursor',
        'MongoDB\Driver\CursorId',
        'MongoDB\Driver\Server',
        'MongoDB\Driver\WriteResult',
        'MongoDB\Driver\BulkWrite',
        'MongoDB\Driver\Query',
        'MongoDB\Driver\ReadPreference',
        'MongoDB\Driver\WriteConcern',
        'MongoDB\Driver\Session',
        'MongoDB\Driver\Exception\RuntimeException',
        'MongoDB\Driver\Exception\ConnectionTimeoutException',
        'MongoDB\Driver\Exception\AuthenticationException',
        'MongoDB\Driver\Exception\SSLConnectionException',
        'MongoDB\Driver\Exception\InvalidArgumentException',
        'MongoDB\Driver\Exception\BulkWriteException',
        'MongoDB\Driver\Exception\ConnectionException',
        'MongoDB\Driver\Exception\DuplicateKeyException',
        'MongoDB\Driver\Exception\ExecutionTimeoutException',
        'MongoDB\Driver\Exception\LogicException',
        'MongoDB\Driver\Exception\ServerException',
        'MongoDB\Driver\Exception\UnexpectedValueException',
        'MongoDB\Driver\Exception\WriteException'
    );
    
    // Register MongoDB types for type hinting
    expectedArguments(
        \MongoDB\Driver\Manager::__construct(),
        0, // DSN
        1, // Options
        2  // Driver options
    );
    
    expectedArguments(
        \MongoDB\Driver\Command::__construct(),
        0 // Command document
    );
    
    // MongoDB Manager methods
    expectedReturnValues(
        \MongoDB\Driver\Manager::__construct(),
        'MongoDB\Driver\Manager'
    );
    
    expectedReturnValues(
        \MongoDB\Driver\Command::__construct(),
        'MongoDB\Driver\Command'
    );
    
    // MongoDB Exception types
    expectedReturnValues(
        \MongoDB\Driver\Exception\RuntimeException::class,
        'MongoDB\Driver\Exception\RuntimeException'
    );
    
    expectedReturnValues(
        \MongoDB\Driver\Exception\ConnectionTimeoutException::class,
        'MongoDB\Driver\Exception\ConnectionTimeoutException'
    );
    
    expectedReturnValues(
        \MongoDB\Driver\Exception\AuthenticationException::class,
        'MongoDB\Driver\Exception\AuthenticationException'
    );
    
    expectedReturnValues(
        \MongoDB\Driver\Exception\SSLConnectionException::class,
        'MongoDB\Driver\Exception\SSLConnectionException'
    );
}

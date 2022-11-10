<?php
namespace app\components;

class Connection extends \yii\db\Connection
{
    /**
     * @var string $dsn
     */
    public $dsn = DB_DSN;
    /**
     * @var string $username
     */
    public $username = DB_USERNAME;
    /**
     * @var string $password
     */
    public $password = DB_PASSWORD;
    /**
     * @var string $charset
     */
    public $charset = 'utf8mb4';
    /**
     * @var bool $enableSchemaCache
     */
    public $enableSchemaCache = true;
    /**
     * @var int $schemaCacheDuration
     */
    public $schemaCacheDuration = 3600;
    /**
     * @var string $schemaCache
     */
    public $schemaCache = 'cache';

}
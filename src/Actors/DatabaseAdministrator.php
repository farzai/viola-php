<?php

namespace Farzai\Viola\Actors;

use Farzai\Viola\Contracts\Actor as ActorContract;
use Farzai\Viola\Contracts\Database\Connection;

class DatabaseAdministrator implements ActorContract
{
    public static $prompt = '
        I want you to act as a Database Administrator.
        The database contains tables named [tables].
        I will type questions and you will reply with SQL queries.
        I want you to reply with a SQL Query in a single code block, and nothing else. Do not write explanations. 
        Do not type commands unless I instruct you to do so. 
        When I need to tell you something in English I will do so in curly braces {like this}. 
        My first command is ‘Show me all products’
    ';

    /**
     * @var \Farzai\Viola\Contracts\Database\Connection
     */
    private $connection;

    /**
     * DatabaseAdministrator constructor.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the prompt for the actor.
     */
    public function getPrompt(): string
    {
        return static::$prompt;
    }

    /**
     * Handle the command.
     */
    public function handle(string $command): string
    {
        return $this->connection->performQuery($command);
    }

    /**
     * Get the identifier for the actor.
     */
    public function getIdentifier(): string
    {
        return 'database-administrator';
    }
}

<?php declare(strict_types=1);

namespace App\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class RandFunction extends FunctionNode
{
    use PlatformAware;

    /**
     * Eine der beiden Funktionen ohne Entsprechung im SQL-Standard: MySQL
     * schreibt RAND(), PostgreSQL RANDOM().
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return $this->isPostgreSql($sqlWalker) ? 'RANDOM()' : 'RAND()';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}

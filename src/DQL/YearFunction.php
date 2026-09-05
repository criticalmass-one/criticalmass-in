<?php declare(strict_types=1);

namespace App\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class YearFunction extends FunctionNode
{
    private Node|string|null $arg = null;

    /**
     * EXTRACT gehoert zum SQL-Standard und wird von MySQL wie von
     * PostgreSQL verstanden; YEAR() kennt nur MySQL.
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('EXTRACT(YEAR FROM %s)', $this->arg->dispatch($sqlWalker));
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->arg = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}

<?php declare(strict_types=1);

namespace App\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class DateFunction extends FunctionNode
{
    private Node|string|null $arg = null;

    /**
     * CAST gehoert zum SQL-Standard; die Funktion DATE() gibt es in
     * PostgreSQL nicht.
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('CAST(%s AS DATE)', $this->arg->dispatch($sqlWalker));
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->arg = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}

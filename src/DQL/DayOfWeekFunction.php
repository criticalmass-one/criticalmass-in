<?php declare(strict_types=1);

namespace App\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class DayOfWeekFunction extends FunctionNode
{
    use PlatformAware;

    private Node|string|null $arg = null;

    /**
     * Die zweite Funktion ohne Entsprechung im SQL-Standard — und die einzige,
     * bei der nicht nur der Name abweicht, sondern die Zaehlung: MySQLs
     * DAYOFWEEK() liefert 1 bis 7 mit Sonntag als 1, PostgreSQLs
     * EXTRACT(DOW ...) liefert 0 bis 6 mit Sonntag als 0. Ohne das +1 waere
     * jeder Wochentag um eins verschoben, ohne dass irgendetwas danach
     * auffiele.
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        $arg = $this->arg->dispatch($sqlWalker);

        if ($this->isPostgreSql($sqlWalker)) {
            return sprintf('(EXTRACT(DOW FROM %s) + 1)', $arg);
        }

        return sprintf('DAYOFWEEK(%s)', $arg);
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->arg = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}

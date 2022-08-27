<?php

declare(strict_types=1);

namespace Doctrine\ORM\Query\AST;

use Doctrine\ORM\Query\SqlWalker;

/**
 * SubselectFromClause ::= "FROM" SubselectIdentificationVariableDeclaration {"," SubselectIdentificationVariableDeclaration}*
 *
 * @link    www.doctrine-project.org
 */
class SubselectFromClause extends Node
{
    /** @var mixed[] */
    public $identificationVariableDeclarations = [];

    /** @param mixed[] $identificationVariableDeclarations */
    public function __construct(array $identificationVariableDeclarations)
    {
        $this->identificationVariableDeclarations = $identificationVariableDeclarations;
    }

    public function dispatch(SqlWalker $walker): string
    {
        return $walker->walkSubselectFromClause($this);
    }
}

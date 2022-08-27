<?php

declare(strict_types=1);

namespace Doctrine\ORM\Mapping;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"PROPERTY","ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class JoinColumn implements Annotation
{
    /** @var string|null */
    public $name;

    /** @var string */
    public $referencedColumnName = 'id';

    /** @var bool */
    public $unique = false;

    /** @var bool */
    public $nullable = true;

    /** @var string|null */
    public $columnDefinition;

    /**
     * Field name used in non-object hydration (array/scalar).
     *
     * @var string|null
     */
    public $fieldName;

    /** @var array<string, mixed> */
    public $options = [];

    /** @param array<string, mixed> $options */
    public function __construct(
        string|null $name = null,
        string $referencedColumnName = 'id',
        bool $unique = false,
        bool $nullable = true,
        public $onDelete = null,
        string|null $columnDefinition = null,
        string|null $fieldName = null,
        array $options = [],
    ) {
        $this->name                 = $name;
        $this->referencedColumnName = $referencedColumnName;
        $this->unique               = $unique;
        $this->nullable             = $nullable;
        $this->columnDefinition     = $columnDefinition;
        $this->fieldName            = $fieldName;
        $this->options              = $options;
    }
}

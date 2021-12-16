<?php

declare(strict_types=1);

namespace loophp\phptree\Modifier;

use loophp\phptree\Node\NodeInterface;
use loophp\phptree\Traverser\PostOrder;
use loophp\phptree\Traverser\PreOrder;
use loophp\phptree\Traverser\TraverserInterface;

/**
 * Class Filter.
 */
class Filter implements ModifierInterface
{
    /**
     * @var callable
     */
    private $filter;

    /**
     * @var PreOrder|TraverserInterface
     */
    private $traverser;

    /**
     * Filter constructor.
     */
    public function __construct(callable $filter, ?TraverserInterface $traverser = null)
    {
        $this->filter = $filter;
        $this->traverser = $traverser ?? new PostOrder();
    }

    /**
     * {@inheritdoc}
     */
    public function modify(NodeInterface $tree): NodeInterface
    {
        foreach ($this->traverser->traverse($tree) as $item) {
            if (null === $parent = $item->getParent()) {
                continue;
            }

            if (!(bool) ($this->filter)($item)) {
                continue;
            }

            $parent->remove($item);
        }

        return $tree;
    }
}

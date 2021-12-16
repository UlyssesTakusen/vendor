<?php

declare(strict_types=1);

namespace loophp\phptree\Node;

/**
 * Interface MerkleNodeInterface.
 */
interface MerkleNodeInterface extends ValueNodeInterface
{
    public function hash(): string;

    /**
     * @return \loophp\phptree\Node\MerkleNodeInterface
     */
    public function normalize(): MerkleNodeInterface;
}

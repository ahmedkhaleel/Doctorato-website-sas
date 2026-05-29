<?php

namespace App\Observers;

use App\Services\PublicContentCache;

/**
 * One observer registered against every model whose data feeds
 * PublicContentCache. Any save / delete flushes the entire public
 * cache so an admin edit shows up on the next page render — no
 * stale prices, no stale FAQs after a content update.
 *
 * Flushing all keys (vs. only the affected ones) is the right move
 * here because: the key space is ~30 entries, and 'flush' on the
 * file driver is itself a series of cheap unlink() calls. The
 * granular alternative would mean each model tracking which keys
 * it influences — overkill for the volume of admin writes we get.
 */
class PublicContentObserver
{
    public function __construct(protected PublicContentCache $cache)
    {
    }

    public function saved($model): void
    {
        $this->cache->flush();
    }

    public function deleted($model): void
    {
        $this->cache->flush();
    }
}

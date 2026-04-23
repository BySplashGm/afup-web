<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TalkBlueskyPosts extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("ALTER TABLE `afup_sessions` ADD `bluesky_posts` text DEFAULT NULL AFTER `tweets`");
    }
}

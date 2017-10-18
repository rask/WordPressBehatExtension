<?php

namespace rask\WordPressBehatExtension\Context;

/**
 * Interfaces for contexts which want to receive an inbox factory
 * This allows the context to ask for inbox associated with an e-mail address to make assertions about
 * its contents.
 */
interface WordPressInboxFactoryAwareContext
{
    public function setInboxFactory(\rask\WordPressBehatExtension\WordPress\InboxFactory $factory);
}

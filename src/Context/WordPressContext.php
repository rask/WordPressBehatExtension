<?php
namespace rask\WordPressBehatExtension\Context;

use Behat\Gherkin\Node\TableNode;
use rask\WordPressBehatExtension\WordPress\InboxFactory;
use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert as PHPUnit;

class WordPressContext implements Context, WordPressInboxFactoryAwareContext
{
    /**
     * @var InboxFactory
     */
    protected $inboxFactory;

    public function setInboxFactory(InboxFactory $factory)
    {
        $this->inboxFactory = $factory;
    }

    /**
     * Create a new WordPress website from scratch
     *
     * @Given /^\w+ have a vanilla WordPress installation$/
     */
    public function installWordPress(TableNode $table = null)
    {
        /** @var \WP_Rewrite $wp_rewrite */
        global $wp_rewrite;

        PHPUnit::assertTrue(function_exists('get_option'), 'WordPress core not loaded while installing!');

        $name = "admin";
        $email = "an@example.com";
        $password = "test";
        $username = "admin";

        if ($table) {
            $hash = $table->getHash();
            $row = $hash[0];
            $name = $row["name"];
            $username = $row["username"];
            $email = $row["email"];
            $password = $row["password"];
        }

        $mysqli = new \Mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $value = $mysqli->multi_query(implode("\n", array(
            "DROP DATABASE IF EXISTS " . DB_NAME . ";",
            "CREATE DATABASE " . DB_NAME . ";",
        )));

        PHPUnit::assertTrue($value);

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        wp_install($name, $username, $email, true, '', $password);

        //This is a bit of a hack, we don't care about the notification e-mails here so clear the inbox
        //we run the risk of deleting stuff we want!
        $this->inboxFactory->getInbox($email)->clearInbox();

        $wp_rewrite->init();
        $wp_rewrite->set_permalink_structure('/%year%/%monthnum%/%day%/%postname%/');
    }
}

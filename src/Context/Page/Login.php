<?php

namespace rask\WordPressBehatExtension\Context\Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Login extends Page
{
    use \rask\WordPressBehatExtension\Context\Util\Spin;
    use \rask\WordPressBehatExtension\Context\Users\WordPressUserTrait;

    /**
     * @var string $path
     */
    protected $path = '/wp-login.php';


    public function loginAs($username, $password)
    {

        $user = $this->getUserByLogin($username);
        $this->validatePassword($user, $password);

        $currentPage = $this;

        // Wait 1/4 second -- seems to fix https://github.com/stephenharris/WordPressBehatExtension/issues/10
        usleep(250000);

        $this->spin(function ($context) use ($currentPage, $username, $password) {
            $currentPage->fillField('user_login', $username);
            $currentPage->fillField('user_pass', $password);
            $context->checkField('rememberme');
            $currentPage->clickLogIn();
            return true;
        });

        return $this->getPage('Dashboard');
    }

    public function clickLogIn()
    {
        $this->findButton('wp-submit')->click();
    }


    /**
     * Modified isOpen function which throws exceptions
     * @param array $urlParameters
     * @see https://github.com/sensiolabs/BehatPageObjectExtension/issues/57
     * @return boolean
     */
    public function isOpen(array $urlParameters = array())
    {
        $this->verify($urlParameters);
        return true;
    }

    /**
     * Overloaded to remove any query variables, i.e ?redirect_to=...
     *
     * @param array $urlParameters
     */
    protected function verifyUrl(array $urlParameters = array())
    {
        $cleanedUrl = strtok($this->getDriver()->getCurrentUrl(), '?');
        if ($cleanedUrl !== $this->getUrl($urlParameters)) {
            throw new UnexpectedPageException(
                sprintf(
                    'Expected to be on "%s" but found "%s" instead',
                    $this->getUrl($urlParameters),
                    $this->getDriver()->getCurrentUrl()
                )
            );
        }
    }
}

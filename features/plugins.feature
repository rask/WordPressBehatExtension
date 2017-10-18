Feature: Manage plugins
    In order to manage plugins
    As an admin
    I need to enable and disable plugins

    Background:
        Given I have a vanilla WordPress installation
            | name          | email             | username | password |
            | BDD WordPress | admin@example.com | admin    | password |
        And I am logged in as "admin" with password "password"

    Scenario: Enable the dolly plugin
        Given there are plugins
            | plugin    | status  |
            | hello.php | enabled |
        When I go to "/wp-admin/"
        Then I should see a "#dolly" element

    Scenario: Disable the dolly plugin
        Given there are plugins
            | plugin    | status   |
            | hello.php | disabled |
        When I go to "/wp-admin/"
        Then I should not see a "#dolly" element

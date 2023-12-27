<?php

namespace Tahseen9\LaravelSupervisorManager\Services;

class ControlPanel
{
    /*
     * background processes should be managed within this control panel
     *
     * Security: This control panel will be opened only to the relevant members, so
     * we need a command to allow ip addresses to view this panel
     * we will limit request from same ip to a specific number and will revoke it if it is
     * accessed more than the number provided in same day. We will make a log of each activity done
     * using control panel.
     * It will only run on HTTPS.
     * Use Internal HMAC
     * In a scenario where the client is a browser, HMAC could be used to ensure the integrity and authenticity of data exchanged between the server and the client.
        Here's a simplified idea on how it could be implemented:

        1-Upon an individual's sign in, the server generates a unique secret key for the session, which is then stored on the server side and never revealed to the client.

        2-The server uses this secret key along with the user's ID or another unique identifier to generate an HMAC.

        3-The server then sends both the user's ID and the HMAC to the client, typically as cookies.

        4-The next time the client sends a request to the server, it includes both the user's ID and the HMAC.

        5-The server regenerates the HMAC using the user's ID and the secret key it stored earlier.

        6-The server compares the HMAC it generated with the HMAC provided by the client. If they match, the server accepts the request. If they don't match, the server rejects the request.

        This process ensures that even if an attacker steals both the user's ID and the HMAC, they won't have the ability to modify the user's ID without access to the unique secret key that was used to generate the HMAC.
     *
     *
     * Control panel user story
     *
     * regular user mode
     * options
     *      - run only php / artisan commands as background process - functional flow (wizard based), activity status etc
     *      - run custom commands as background process - Wizard based, Allow only allowed(php, java etc which ever is allowed) commands
     * settings via install command when package is added - it must run on each environment at least once
     *      - it will include its own .env like configuration file which will store the paths of allowed commands for example
     *          -php
     *          -java
     *          -*any other language command if it requires path
     *
     * */

}

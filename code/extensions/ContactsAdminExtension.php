<?php

namespace ilateral\SilverStripe\Contacts\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class ContactsAdminExtension extends Extension
{
    public function init()
    {
        Requirements::css('i-lateral/silverstripe-contacts: client/dist/css/admin.css');
    }
}
<?php
/**
 * Wrapper around Ids object that correctly handles POP3 UID strings.
 *
 * Copyright 2011 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Imap_Client
 */
class Horde_Imap_Client_Ids_Pop3 extends Horde_Imap_Client_Ids
{
    /**
     */
    protected $_utilsClass = 'Horde_Imap_Client_Utils_Pop3';

}

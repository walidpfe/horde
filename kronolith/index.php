<?php
/**
 * Copyright 1999-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * not receive such a file, see also http://www.fsf.org/copyleft/gpl.html.
 */

require_once dirname(__FILE__) . '/lib/Application.php';
Horde_Registry::appInit('kronolith');

/* Load traditional interface? */
if (!$prefs->getValue('dynamic_view') || !$browser->hasFeature('xmlhttpreq') ||
    ($browser->isBrowser('msie') && $browser->getMajor() <= 6)) {
    include KRONOLITH_BASE . '/' . $prefs->getValue('defaultview') . '.php';
    exit;
}

/* Load Ajax interface. */
$menu = new Horde_Menu();
$help_link = Horde::getServiceLink('help', 'kronolith');
if ($help_link) {
    $help_link = Horde::widget($help_link, _("Help"), 'helplink', 'help', Horde::popupJs($help_link, array('urlencode' => true)) . 'return false;');
}
$today = new Horde_Date($_SERVER['REQUEST_TIME']);
$_SESSION['horde_prefs']['nomenu'] = true;

$alarm_methods = $alarm_params = '';
foreach (Horde_Alarm::notificationMethods() as $method => $params) {
    $alarm_methods .= ' <input type="checkbox" name="event_alarms[]" id="kronolithEventAlarm' . $method . '" value="' . $method . '" /> <label for="kronolithEventAlarm' . $method . '">' . $params['__desc'] . '</label>';
    if (count($params) < 2) {
        continue;
    }
    $alarm_params .= ' <div id="kronolithEventAlarm' . $method . 'Params" style="display:none">';
    foreach ($params as $name => $param) {
        if (substr($name, 0, 2) == '__') {
            continue;
        }
        $alarm_params .= ' <label for="kronolithEventAlarmParam' . $name
            . '">' . $param['desc'] . '</label> ';
        $name_att = 'name="event_alarms_' . $name . '"';
        $att = 'id="kronolithEventAlarmParam' . $name . '" ' . $name_att;
        switch ($param['type']) {
        case 'text':
            $alarm_params .= '<input type="text" ' . $att . ' />';
            break;
        case 'boolean':
            $alarm_params .= '<input type="checkbox" ' . $att . ' />';
            break;
        case 'sound':
            $alarm_params .= '<ul class="sound-list"><li><input type="radio" ' . $att
                . ' value="" checked="checked" /> ' . _("No Sound") . '</li>';
            foreach (glob($registry->get('themesfs', 'horde') . '/sounds/*.wav') as $sound) {
                $sound = htmlspecialchars(basename($sound));
                $alarm_params .= '<li><input type="radio" id="kronolithEventAlarmParam'
                    . $name . str_replace('.wav', '', $sound) . '" ' . $name_att
                    . ' value="' .  $sound
                    . '" /> <embed autostart="false" src="'
                    . $registry->get('themesuri', 'horde') . '/sounds/'
                    . $sound . '" /> ' . $sound . '</li>';
            }
            $alarm_params .= '</ul>';
            break;
        }
        $alarm_params .= '<br />';
    }
    $alarm_params = substr($alarm_params, 0, - 6);
    $alarm_params .= '</div>';
}

Kronolith::header();
echo "<body class=\"kronolithAjax\">\n";
require KRONOLITH_TEMPLATES . '/index/index.inc';
Horde::includeScriptFiles();
Horde::outputInlineScript();
if ($conf['maps']['driver']) {
    Kronolith::initEventMap($conf['maps']);
}
$tac = Horde_Ajax_Imple::factory(array('kronolith', 'TagAutoCompleter'), array('triggerId' => 'kronolithEventTags', 'box' => 'kronolithEventACBox', 'pretty' => true));
$tac->attach();
echo "</body>\n</html>";

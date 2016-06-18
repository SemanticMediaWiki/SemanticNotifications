<?php

namespace SMW\Notifications\ValueChange;

use EchoEditFormatter;
use EchoEvent;
use EchoNotificationController;
use Message;
use User;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ChangeNotificationFormatter extends EchoEditFormatter {

	/**
	 * @param $event EchoEvent
	 * @param $param
	 * @param $message Message
	 * @param $user User
	 */
	protected function processParam( $event, $param, $message, $user ) {
		parent::processParam( $event, $param, $message, $user );
	}

}

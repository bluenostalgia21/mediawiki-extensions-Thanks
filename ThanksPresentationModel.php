<?php
class EchoThanksPresentationModel extends EchoEventPresentationModel {
	public function canRender() {
		return (bool)$this->event->getTitle();
	}

	public function getIconType() {
		return 'thanks';
	}

	public function getHeaderMessage() {
		$msg = parent::getHeaderMessage();
		$msg->params( $this->event->getTitle()->getPrefixedText() );

		$msg->params( $this->getViewingUserForGender() );
		return $msg;
	}

	public function getPrimaryLink() {
		return array(
			$this->event->getTitle()->getLocalURL( array(
				'oldid' => 'prev',
				'diff' => $this->event->getExtraParam( 'revid' )
			) ),
			$this->msg( 'notification-link-text-view-edit' )->text()
		);
	}

	public function getSecondaryLinks() {
		return array( $this->getAgentLink() );
	}
}
<?php
class EchoCoreThanksPresentationModel extends EchoEventPresentationModel {
	public function canRender() {
		return (bool)$this->event->getTitle();
	}

	public function getIconType() {
		return 'thanks';
	}

	public function getHeaderMessage() {
		$type = $this->event->getExtraParam( 'logid' ) ? 'log' : 'rev';
		if ( $this->isBundled() ) {
			$msg = $this->msg( "notification-bundle-header-$type-thank" );
			$msg->params( $this->getBundleCount() );
			$msg->params( $this->getTruncatedTitleText( $this->event->getTitle(), true ) );
			$msg->params( $this->getViewingUserForGender() );
			return $msg;
		} else {
			$msg = $this->getMessageWithAgent( "notification-header-$type-thank" );
			$msg->params( $this->getTruncatedTitleText( $this->event->getTitle(), true ) );
			$msg->params( $this->getViewingUserForGender() );
			return $msg;
		}
	}

	public function getCompactHeaderMessage() {
		$msg = parent::getCompactHeaderMessage();
		$msg->params( $this->getViewingUserForGender() );
		return $msg;
	}

	public function getBodyMessage() {
		$comment = $this->getRevOrLogComment();
		if ( $comment ) {
			$msg = new RawMessage( '$1' );
			$msg->plaintextParams( $comment );
			return $msg;
		}
	}

	private function getRevisionEditSummary() {
		if ( !$this->userCan( Revision::DELETED_COMMENT ) ) {
			return false;
		}

		$revId = $this->event->getExtraParam( 'revid', false );
		if ( !$revId ) {
			return false;
		}

		$revision = Revision::newFromId( $revId );
		if ( !$revision ) {
			return false;
		}

		$summary = $revision->getComment( Revision::RAW );
		return $summary ?: false;
	}

	/**
	 * Get the comment/summary/excerpt of the log entry or revision,
	 * for use in the notification body.
	 * @return string|bool The comment or false if it could not be retrieved.
	 */
	protected function getRevOrLogComment() {
		if ( $this->event->getExtraParam( 'logid' ) ) {
			// Use the saved log entry excerpt.
			$excerpt = $this->event->getExtraParam( 'excerpt', false );
			// Turn wikitext into plaintext
			$excerpt = Linker::formatComment( $excerpt );
			$excerpt = Sanitizer::stripAllTags( $excerpt );
			return $excerpt;
		} else {
			// Try to get edit summary.
			$summary = $this->getRevisionEditSummary();
			if ( $summary ) {
				return $summary;
			}
			// Fallback on edit excerpt.
			if ( $this->userCan( Revision::DELETED_TEXT ) ) {
				return $this->event->getExtraParam( 'excerpt', false );
			}
		}
	}

	public function getPrimaryLink() {
		if ( $this->event->getExtraParam( 'logid' ) ) {
			$logId = $this->event->getExtraParam( 'logid' );
			$url = Title::newFromText( "Special:Redirect/logid/$logId" )->getCanonicalURL();
			$label = 'notification-link-text-view-logentry';
		} else {
			$url = $this->event->getTitle()->getLocalURL( [
				'oldid' => 'prev',
				'diff' => $this->event->getExtraParam( 'revid' )
			] );
			$label = 'notification-link-text-view-edit';
		}
		return [
			'url' => $url,
			// Label is only used for non-JS clients.
			'label' => $this->msg( $label )->text(),
		];
	}

	public function getSecondaryLinks() {
		$pageLink = $this->getPageLink( $this->event->getTitle(), null, true );
		if ( $this->isBundled() ) {
			return [ $pageLink ];
		} else {
			return [ $this->getAgentLink(), $pageLink ];
		}
	}
}

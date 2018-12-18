<?php

namespace SUPT;

use Engagement_Funnel\Engagement_Funnel;

class EFPConfiguration extends \TimberPost {
	public function initial_action() {
		$engagement_funnel = new Engagement_Funnel();
		$action_id = $engagement_funnel->get_initial_action_id($this->id);
		
		return new \TimberPost($action_id);
	}
}

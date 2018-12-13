<?php
namespace Vanderbilt\RestrictSurveyReferrers;

class RestrictSurveyReferrers extends \ExternalModules\AbstractExternalModule
{
	function redcap_survey_page(){
		$urls = $this->getProjectSetting('urls');
		if($urls === null){
			// The settings dialog has never been saved for this project.
			return;
		}

		$atLeastOneUrlSet = false;
		foreach($urls as $url){
			$url = trim($url);
			if(!empty($url)){
				$atLeastOneUrlSet = true;

				if(strpos(@$_SERVER['HTTP_REFERER'], $url) === 0){
					// The referrer matched!  No need to check any further
					return;
				}
			}
		}
		// No referrers matched

		if($atLeastOneUrlSet){
			$message = $this->getProjectSetting('message');
			if(empty($message)){
				$message = "This survey is restricted.  To access it you must click the link to it from it's original context (copy/pasting the link will not work).";
			}
			?>
			<script>
				$('#pagecontent').html(<?=json_encode($message)?>)
				$('#pagecontent').css('padding', '50px')
			</script>
			<?php
		}
	}
}
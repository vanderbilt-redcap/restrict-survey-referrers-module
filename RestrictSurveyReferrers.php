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

		$referrer = $this->normalizeUrl(@$_SERVER['HTTP_REFERER']);

		$atLeastOneUrlSet = false;
		foreach($urls as $url){
			$url = $this->normalizeUrl($url);
			if(!empty($url)){
				$atLeastOneUrlSet = true;

				if(strpos($referrer, $url) === 0){
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

	private function normalizeUrl($url){
		$url = trim($url);

		// Support the scenario where one survey is the referrer for another, and the URL prior to index.php redirection is used.
		$url = str_replace('/surveys/index.php?s=', '/surveys/?s=', $url);

		return $url;
	}
}
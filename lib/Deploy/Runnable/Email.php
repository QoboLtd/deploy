<?php
namespace Deploy\Runnable;

use Qobo\Pattern\Pattern;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class Email {

	const CONFIG_KEY_EMAIL_TOGGLE  = 'email.send';
	const CONFIG_KEY_EMAIL_TO      = 'email.to';
	const CONFIG_KEY_EMAIL_FROM    = 'email.from';
	const CONFIG_KEY_EMAIL_SUBJECT = 'email.subject';
	const CONFIG_KEY_EMAIL_BODY    = 'email.body';
	
	const TEMPLATE_VAR_OUTPUT = 'output';
	
	protected $config;

	/**
	 * Constructor
	 * 
	 * @todo Figure out a better way to deal with params
	 * @param array $config Configuration parameters
	 * @return object
	 */
	public function __construct($config) {
		$this->config = $config['params'];
		$this->config['target.project'] = $config['_target']['project'][0];
		$this->config['target.environment'] = $config['_target']['environment'][0];
		$this->config['target.command'] = $config['_target']['command'][0];
	}
	
	protected function needSending() {
		$result = false;

		if (!empty($this->config[ self::CONFIG_KEY_EMAIL_TOGGLE ]) && $this->config[ self::CONFIG_KEY_EMAIL_TOGGLE ]) {
			$result = true;
		}

		return $result;
	}
	
	/**
	 * Send 
	 * 
	 * @param string $content Email body
	 * @return boolean True on success, false otherwise
	 */
	public function send($content) {
		$result = false;

		if (!$this->needSending()) {
			return $result;
		}
		
		$transport = \Swift_SmtpTransport::newInstance('localhost', 25);
		$mailer = \Swift_Mailer::newInstance($transport);
		$message = \Swift_Message::newInstance();
		
		$message->setFrom($this->getFrom());
		$message->setTo($this->getTo());
		$message->setSubject($this->getSubject());
		$message->setBody($this->getBody($content));
		$message->addPart($this->getBody($content, true), 'text/html');
		
		if ($mailer->send($message)) {
			$result = true;
		}

		return $result;
	}

	protected function getDefaultBody($asHtml = false) {
		$result = '';

		if ($asHtml) {
			$result = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title></title>
        <style></style>
    </head>
    <body>
        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
            <tr>
                <td align="left" valign="top">
                    <table style="color:white;background-color:black;font-family:monospace;" border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer">
						<tr>
							<td style="background-color:#baecb7;color:#32a32d;font-weight:bold;font-family:Helvetica,sans-serif;" align="left" valign="top">
								<h3>Deployment notification</h3>
								Project: %%target.project%%<br />
								Environment: %%target.environment%%<br />
								Command: %%target.command%%<br />
							</td>
						</tr>
                        <tr>
                            <td align="left" valign="top">
								<h4>Full log</h4>
								<hr />
								<pre>%%output%%</pre>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
EOT;
		}
		else {
			$result = '%%output%%';
		}

		return $result;
	}
	
	protected function getBody($content, $asHtml = false) {
		$result = '';
		
		if (empty($this->config[self::CONFIG_KEY_EMAIL_BODY])) {
			$template = $this->getDefaultBody($asHtml);
		}
		else {
			$template = $this->config[self::CONFIG_KEY_EMAIL_BODY];
		}
	
		if ($asHtml) {
			$converter = new AnsiToHtmlConverter();
			$content = $converter->convert($content);
		}
		else {
			// As per: http://www.webdeveloper.com/forum/showthread.php?186004-RESOLVED-Remove-Ansi-Escape-Sequences
			$content = preg_replace('/\x1b(\[|\(|\))[;?0-9]*[0-9A-Za-z]/', "",$content); 
			$content = preg_replace('/\x1b(\[|\(|\))[;?0-9]*[0-9A-Za-z]/', "",$content); 
			$content = preg_replace('/[\x03|\x1a]/', "", $content); 
		}
		$params = $this->config;
		$params[ self::TEMPLATE_VAR_OUTPUT ] = $content;
	
		$result = (string) new Pattern($template, $params);
	
		return $result;
	}
	
	protected function getSubject() {
		$result = '';
		
		$subject = $this->config[self::CONFIG_KEY_EMAIL_SUBJECT];
		$params = $this->config;
		$result = (string) new Pattern($subject, $params);
		
		return $result;
	}


	protected function getTo() {
		$result = $this->config[self::CONFIG_KEY_EMAIL_TO];
		return $result;
	}

	protected function getFrom() {
		$result = '';
		
		if (!empty($this->config[self::CONFIG_KEY_EMAIL_FROM])) {
			$result = $this->config[self::CONFIG_KEY_EMAIL_FROM];
			return $result;
		}
		
		$result = $this->generateFrom();

		return $result;
	}
	
	/**
	 * Generate From: address
	 * 
	 * Use the current user's username at hostname
	 * 
	 * @return string
	 */
	protected function generateFrom() {
		$result = '';
		
		$processUser = posix_getpwuid(posix_geteuid());
		$result = $processUser['name'] . '@' . gethostname();

		return $result;
	}
}
?>

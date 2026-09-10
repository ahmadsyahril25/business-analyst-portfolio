<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated', 'phpids' ) );
dvwaDatabaseConnect();

$page = dvwaPageNewGrab();
$page[ 'title' ] = 'Vulnerability: Insecure Direct Object Reference (IDOR)' . $page[ 'title_separator' ] . $page[ 'title' ];
$page[ 'page_id' ] = 'idor';
$page[ 'help_button' ] = 'idor';
$page[ 'source_button' ] = 'idor';

$vulnerabilityFile = '';
switch( dvwaSecurityLevelGet() ) {
	case 'low':
		$vulnerabilityFile = 'low.php';
		break;
	case 'medium':
		$vulnerabilityFile = 'medium.php';
		break;
	case 'high':
		$vulnerabilityFile = 'high.php';
		break;
	default:
		$vulnerabilityFile = 'impossible.php';
		break;
}

require_once DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/idor/source/{$vulnerabilityFile}";

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Insecure Direct Object Reference (IDOR)</h1>

	<p>This vulnerability demonstrates horizontal access control issues where a user can access other users' data by manipulating parameters.</p>

	<div class=\"vulnerable_code_area\">
		<h2>User Document Access</h2>
		<p>View your documents by entering your document ID below:</p>
		<form action=\"#\" method=\"GET\">
			<p>
				Document ID: 
				<input type=\"text\" size=\"5\" name=\"doc_id\" value=\"1\">
				<input type=\"submit\" value=\"View Document\" name=\"action\">
			</p>
		</form>
		{$html}
	</div>

	<br />

	<h2>More Information</h2>
	<ul>
		<li>" . dvwaExternalLinkUrlGet('https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References') . "</li>
		<li>" . dvwaExternalLinkUrlGet('https://portswigger.net/web-security/access-control/idor') . "</li>
		<li>" . dvwaExternalLinkUrlGet('https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html') . "</li>
	</ul>
</div>";

dvwaHtmlEcho( $page );

?>

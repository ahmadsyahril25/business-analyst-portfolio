<div class="body_padded">
	<h1>Help - Insecure Direct Object Reference (IDOR)</h1>

	<div id="code">
		<table width="100%" bgcolor="white" style="border:2px #C0C0C0 solid">
			<tr>
				<td>
					<div id="code">
						<h3>About</h3>
						<p>Insecure Direct Object Reference (IDOR) occurs when an application provides direct access to objects based on user-supplied input without proper authorization checks. This allows attackers to bypass authorization and access resources they shouldn't have access to.</p>
						
						<p><strong>Horizontal Access Control:</strong> Users at the same privilege level accessing each other's data (e.g., User A viewing User B's documents).</p>
						
						<p><strong>Vertical Access Control:</strong> Users accessing functionality or data of users with higher privileges (e.g., regular user accessing admin functions).</p>

						<br />
						<hr /><br />

						<h3>Objective</h3>
						<p>Your goal is to view documents belonging to other users by manipulating the document ID parameter. Each security level implements different (or no) access controls.</p>
						
						<p>Try logging in as different users and see if you can access documents that don't belong to you!</p>

						<br />
						<hr />

						<h3>Low Level</h3>
						<p><strong>Vulnerability:</strong> No access control at all. The application directly uses the user-supplied document ID without checking ownership.</p>
						
						<p><strong>Hint:</strong> Simply change the doc_id parameter in the URL to view other users' documents.</p>

						<div class="vulnerable_code_area">
							<h3>Solution</h3>
							<button class="popup_button" onclick="toggle_visibility('low_solution')">Show Solution</button>
							<br>
							<div id="low_solution" style="display: none;">
								<ol>
									<li>View your own documents first to see what document IDs you have access to</li>
									<li>Try accessing document IDs sequentially: 1, 2, 3, 4, 5...</li>
									<li>You'll be able to view documents belonging to other users</li>
									<li>Notice there's no check whether you own the document or not</li>
								</ol>
								<p><strong>Example:</strong> If your documents are IDs 1 and 2, try accessing doc_id=3 or doc_id=4 to see other users' documents.</p>
							</div>
						</div>

						<br />

						<h3>Medium Level</h3>
						<p><strong>Vulnerability:</strong> Uses a token-based check, but the token is predictable (MD5 of doc_id + known salt).</p>
						
						<p><strong>Hint:</strong> The token is calculated as md5(doc_id + 'secret_key'). Can you calculate it yourself?</p>

						<div class="vulnerable_code_area">
							<h3>Solution</h3>
							<button class="popup_button" onclick="toggle_visibility('medium_solution')">Show Solution</button>
							<br>
							<div id="medium_solution" style="display: none;">
								<ol>
									<li>Notice the token format revealed in the debug message</li>
									<li>Calculate the token for any document ID: <code>md5(doc_id + 'secret_key')</code></li>
									<li>Use online MD5 calculators or command line:
										<ul>
											<li>Linux/Mac: <code>echo -n "3secret_key" | md5sum</code></li>
											<li>Or use PHP: <code>echo md5("3secret_key");</code></li>
										</ul>
									</li>
									<li>Add the calculated token to your URL</li>
								</ol>
								<p><strong>Why this fails:</strong> The token uses a predictable algorithm with a known salt.</p>
							</div>
						</div>

						<br />

						<h3>High Level</h3>
						<p><strong>Vulnerability:</strong> Uses referer header check which can be easily spoofed.</p>
						
						<p><strong>Hint:</strong> The application checks if the request comes from the same page, but HTTP headers can be manipulated.</p>

						<div class="vulnerable_code_area">
							<h3>Solution</h3>
							<button class="popup_button" onclick="toggle_visibility('high_solution')">Show Solution</button>
							<br>
							<div id="high_solution" style="display: none;">
								<ol>
									<li>Use a proxy tool like Burp Suite or browser developer tools</li>
									<li>Intercept the request when viewing a document</li>
									<li>The referer check defaults to true if no referer is set</li>
									<li>Access the URL directly without clicking a link</li>
								</ol>
								<p><strong>Why this fails:</strong> HTTP headers are client-controlled and should never be trusted for security decisions.</p>
							</div>
						</div>

						<br />

						<h3>Impossible Level</h3>
						<p>This level implements proper IDOR prevention:</p>
						<ul>
							<li><strong>Authorization Check:</strong> Verifies the document belongs to the current user</li>
							<li><strong>Prepared Statements:</strong> Prevents SQL injection</li>
							<li><strong>Input Validation:</strong> Ensures document ID is numeric</li>
							<li><strong>Logging:</strong> All access attempts are logged for audit</li>
						</ul>

						<br />
						<hr /><br />

						<h3>More Information</h3>
						<ul>
							<li><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" target="_blank">OWASP Testing Guide - IDOR</a></li>
							<li><a href="https://portswigger.net/web-security/access-control/idor" target="_blank">PortSwigger - IDOR</a></li>
							<li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html" target="_blank">OWASP Cheat Sheet - IDOR Prevention</a></li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>

<script>
function toggle_visibility(id) {
	var e = document.getElementById(id);
	if (e.style.display == "block") {
		e.style.display = "none";
	} else {
		e.style.display = "block";
	}
}
</script>
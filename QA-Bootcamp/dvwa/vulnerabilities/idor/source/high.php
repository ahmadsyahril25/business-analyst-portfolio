<?php

// HIGH Level - Access control with session check (still vulnerable to IDOR)
// Vulnerability: Checks ownership through session but has logic flaw

$html = "";

// Get current user ID with prepared statement
$current_user = dvwaCurrentUser();
$query = "SELECT user_id FROM users WHERE user = ? LIMIT 1";
$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $query);
mysqli_stmt_bind_param($stmt, "s", $current_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$current_user_id = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['user_id'] : 0;
mysqli_stmt_close($stmt);

if( isset( $_GET[ 'action' ] ) && isset( $_GET[ 'doc_id' ] ) ) {
	$doc_id = intval( $_GET[ 'doc_id' ] );
	
	// Get document with prepared statement
	$query = "SELECT doc_id, user_id, title, content, created_at FROM documents WHERE doc_id = ? LIMIT 1";
	$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $query);
	mysqli_stmt_bind_param($stmt, "i", $doc_id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if( $result && mysqli_num_rows( $result ) > 0 ) {
		$row = mysqli_fetch_assoc( $result );
		
		// Check if document belongs to current user - BUT with logic flaw
		// Vulnerability: Uses referer header as "additional verification" which can be spoofed
		$referer_ok = true; // Defaults to true!
		
		if( isset( $_SERVER['HTTP_REFERER'] ) ) {
			// Only checks referer if it's set, but defaults to true if not set
			if( strpos( $_SERVER['HTTP_REFERER'], 'vulnerabilities/idor/' ) !== false ) {
				$referer_ok = true;
			}
		}
		
		// Access granted if referer check passes (which it always does by default)
		if( $referer_ok ) {
			$html .= "
			<div class=\"document-view\">
				<h3>Document Details</h3>
				<table>
					<tr><td><strong>Document ID:</strong></td><td>" . htmlspecialchars($row['doc_id']) . "</td></tr>
					<tr><td><strong>Owner ID:</strong></td><td>" . htmlspecialchars($row['user_id']) . "</td></tr>
					<tr><td><strong>Title:</strong></td><td>" . htmlspecialchars($row['title']) . "</td></tr>
					<tr><td><strong>Content:</strong></td><td>" . htmlspecialchars($row['content']) . "</td></tr>
					<tr><td><strong>Created:</strong></td><td>" . htmlspecialchars($row['created_at']) . "</td></tr>
				</table>
			</div>";
			$html .= "<p><em>Note: Access was granted based on referer check. But did you verify ownership?</em></p>";
		} else {
			$html .= "<p>Access denied. Invalid request source.</p>";
		}
	} else {
		$html .= "<p>No document found with ID: " . htmlspecialchars( $doc_id ) . "</p>";
	}
	mysqli_stmt_close($stmt);
}

// Show list of "my documents"
$html .= "
<div class=\"my-documents\">
	<br /><h3>My Documents (User ID: {$current_user_id})</h3>
	<p>Below are your documents. You should only be able to view documents you own.</p>
	<ul>";

$doc_query = "SELECT doc_id, title FROM documents WHERE user_id = ?";
$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $doc_query);
mysqli_stmt_bind_param($stmt, "i", $current_user_id);
mysqli_stmt_execute($stmt);
$doc_result = mysqli_stmt_get_result($stmt);

if( $doc_result && mysqli_num_rows( $doc_result ) > 0 ) {
	while( $doc = mysqli_fetch_assoc( $doc_result ) ) {
		$html .= "<li>Document ID {$doc['doc_id']}: " . htmlspecialchars($doc['title']) . "</li>";
	}
} else {
	$html .= "<li>No documents found for your user.</li>";
}
mysqli_stmt_close($stmt);

$html .= "
	</ul>
	<p><em>Hint: The referer check can be bypassed easily. What happens if you access a document that isn't yours?</em></p>
</div>";

?>

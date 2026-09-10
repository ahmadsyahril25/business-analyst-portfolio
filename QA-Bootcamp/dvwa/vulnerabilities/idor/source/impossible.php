<?php

// IMPOSSIBLE Level - Proper IDOR prevention
// Uses proper authorization checks - user can only access their own resources

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
	// Validate input
	if( !preg_match( '/^\d+$/', $_GET[ 'doc_id' ] ) ) {
		$html .= "<p>Invalid document ID format.</p>";
	} else {
		$doc_id = intval( $_GET[ 'doc_id' ] );
		
		// Get document with prepared statement
		$query = "SELECT doc_id, user_id, title, content, created_at FROM documents WHERE doc_id = ? LIMIT 1";
		$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $query);
		mysqli_stmt_bind_param($stmt, "i", $doc_id);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		if( $result && mysqli_num_rows( $result ) > 0 ) {
			$row = mysqli_fetch_assoc( $result );
			mysqli_stmt_close($stmt);
			
			// CRITICAL: Proper authorization check
			// User can only access their own documents
			if( $row['user_id'] == $current_user_id ) {
				// Authorization passed - show document
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
				
				// Log successful access
				logIDORAccess($current_user_id, $doc_id, 'access_granted');
			} else {
				// Authorization failed - deny access
				$html .= "<p>Access denied. You can only view your own documents.</p>";
				$html .= "<p><em>Security: This document belongs to User ID {$row['user_id']}.</em></p>";
				
				// Log unauthorized access attempt
				logIDORAccess($current_user_id, $doc_id, 'access_denied');
			}
		} else {
			$html .= "<p>No document found with ID: " . htmlspecialchars( $doc_id ) . "</p>";
			mysqli_stmt_close($stmt);
		}
	}
}

// Show list of "my documents"
$html .= "
<div class=\"my-documents\">
	<br /><h3>My Documents (User ID: {$current_user_id})</h3>
	<p>Below are your documents. You can only access documents you own.</p>
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
</div>";

// Helper function for logging
function logIDORAccess($user_id, $doc_id, $action) {
	$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	
	$log_query = "INSERT INTO idor_log (user_id, doc_id, action, ip_address, timestamp) VALUES (?, ?, ?, ?, NOW())";
	$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $log_query);
	mysqli_stmt_bind_param($stmt, "iiss", $user_id, $doc_id, $action, $ip);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

?>

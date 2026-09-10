<?php

// MEDIUM Level - Weak access control (easily bypassed)
// Vulnerability: Access control based on predictable tokens

$html = "";

// Get current user ID
$current_user = dvwaCurrentUser();
$query = "SELECT user_id FROM users WHERE user = '$current_user'";
$result = mysqli_query( $GLOBALS[ "___mysqli_ston" ], $query );
$current_user_id = ( $result && mysqli_num_rows( $result ) > 0 ) ? mysqli_fetch_assoc( $result )['user_id'] : 0;

if( isset( $_GET[ 'action' ] ) && isset( $_GET[ 'doc_id' ] ) ) {
	$doc_id = intval( $_GET[ 'doc_id' ] );
	
	// Check if document exists
	$query = "SELECT doc_id, user_id, title, content, created_at FROM documents WHERE doc_id = $doc_id";
	$result = mysqli_query( $GLOBALS[ "___mysqli_ston" ], $query );
	
	if( $result && mysqli_num_rows( $result ) > 0 ) {
		$row = mysqli_fetch_assoc( $result );
		
		// Weak access control - checks for a "token" parameter that can be guessed/calculated
		$expected_token = md5( $doc_id . "secret_key" ); // Predictable token!
		
		if( isset( $_GET[ 'token' ] ) && $_GET[ 'token' ] === $expected_token ) {
			$html .= "
			<div class=\"document-view\">
				<h3>Document Details</h3>
				<table>
					<tr><td><strong>Document ID:</strong></td><td>{$row['doc_id']}</td></tr>
					<tr><td><strong>Owner ID:</strong></td><td>{$row['user_id']}</td></tr>
					<tr><td><strong>Title:</strong></td><td>{$row['title']}</td></tr>
					<tr><td><strong>Content:</strong></td><td>{$row['content']}</td></tr>
					<tr><td><strong>Created:</strong></td><td>{$row['created_at']}</td></tr>
				</table>
			</div>";
		} else {
			// Still leaks information about document existence
			$html .= "<p>Access denied. Invalid or missing token.</p>";
			$html .= "<p><em>Debug: Token format is md5(doc_id + 'secret_key') - Can you calculate it?</em></p>";
		}
	} else {
		$html .= "<p>No document found with ID: " . htmlspecialchars( $doc_id ) . "</p>";
	}
}

// Show list of "my documents" with tokens
$html .= "
<div class=\"my-documents\">
	<h3>My Documents (User ID: {$current_user_id})</h3>
	<p>Below are your documents with access tokens:</p>
	<ul>";

$doc_query = "SELECT doc_id, title FROM documents WHERE user_id = $current_user_id";
$doc_result = mysqli_query( $GLOBALS[ "___mysqli_ston" ], $doc_query );

if( $doc_result && mysqli_num_rows( $doc_result ) > 0 ) {
	while( $doc = mysqli_fetch_assoc( $doc_result ) ) {
		$token = md5( $doc['doc_id'] . "secret_key" );
		$html .= "<li>Document ID {$doc['doc_id']}: {$doc['title']} (Token: {$token})</li>";
	}
} else {
	$html .= "<li>No documents found for your user.</li>";
}

$html .= "
	</ul>
	<p><em>Hint: The token is predictable. Try calculating tokens for other document IDs...</em></p>
</div>";

?>

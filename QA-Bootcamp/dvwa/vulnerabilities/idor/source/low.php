<?php

// LOW Level - No access control at all
// Vulnerability: Direct object reference without any authorization check

$html = "";

if( isset( $_GET[ 'action' ] ) && isset( $_GET[ 'doc_id' ] ) ) {
	$doc_id = $_GET[ 'doc_id' ];
	
	// No validation - accepts any input
	$query = "SELECT doc_id, user_id, title, content, created_at FROM documents WHERE doc_id = '$doc_id'";
	$result = mysqli_query( $GLOBALS[ "___mysqli_ston" ], $query );
	
	if( $result && mysqli_num_rows( $result ) > 0 ) {
		$row = mysqli_fetch_assoc( $result );
		
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
		$html .= "<p>No document found with ID: " . htmlspecialchars( $doc_id ) . "</p>";
	}
}

// Show list of "my documents" for context
$current_user = dvwaCurrentUser();
$query = "SELECT user_id FROM users WHERE user = '$current_user'";
$result = mysqli_query( $GLOBALS[ "___mysqli_ston" ], $query );
$current_user_id = ( $result && mysqli_num_rows( $result ) > 0 ) ? mysqli_fetch_assoc( $result )['user_id'] : 0;

$html .= "
<div class=\"my-documents\">
	<br /><h3>My Documents (User ID: {$current_user_id})</h3>
	<p>Below are your document IDs that you can access:</p>
	<ul>";

$doc_query = "SELECT doc_id, title FROM documents WHERE user_id = $current_user_id";
$doc_result = mysqli_query( $GLOBALS[ "___mysqli_ston" ], $doc_query );

if( $doc_result && mysqli_num_rows( $doc_result ) > 0 ) {
	while( $doc = mysqli_fetch_assoc( $doc_result ) ) {
		$html .= "<li>Document ID {$doc['doc_id']}: {$doc['title']}</li>";
	}
} else {
	$html .= "<li>No documents found for your user.</li>";
}

$html .= "
	</ul>
	<p><em>Hint: Try accessing document IDs that don't belong to you...</em></p>
</div>";

?>

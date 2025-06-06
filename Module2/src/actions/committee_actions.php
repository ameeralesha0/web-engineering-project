<?php
require_once '../config/db_config.php';

header('Content-Type: application/json');

// Get action type
$action = $_GET['action'] ?? $_POST['action'];

try {
    switch($action) {
        case 'add':
            $event_id = $_POST['event_id'];
            $user_id = $_POST['user_id'];
            $position = $conn->real_escape_string($_POST['position']);

            // Check existing assignment
            $check = $conn->query("SELECT * FROM committees 
                                 WHERE event_id = $event_id AND user_id = $user_id");
            if($check->num_rows > 0) {
                throw new Exception('This student is already assigned to this event');
            }

            $conn->query("INSERT INTO committees (event_id, user_id, position)
                        VALUES ($event_id, $user_id, '$position')");
            
            // Update user permissions
            $conn->query("UPDATE users SET committee_view = 1 WHERE id = $user_id");
            
            echo json_encode(['success' => true]);
            break;

        case 'get':
            $event_id = $_GET['event_id'];
            $result = $conn->query("SELECT c.*, u.username 
                                  FROM committees c
                                  JOIN users u ON c.user_id = u.id
                                  WHERE c.event_id = $event_id");
            
            $html = '<table class="table table-hover">
                     <thead>
                         <tr>
                             <th>Student</th>
                             <th>Position</th>
                             <th>Assigned Date</th>
                             <th>Actions</th>
                         </tr>
                     </thead>
                     <tbody>';
            
            while($row = $result->fetch_assoc()) {
                $html .= "<tr>
                            <td>{$row['username']}</td>
                            <td>{$row['position']}</td>
                            <td>".date('d M Y', strtotime($row['assigned_at']))."</td>
                            <td>
                                <button class='btn btn-sm btn-danger delete-btn' 
                                        data-id='{$row['id']}'>Delete</button>
                            </td>
                          </tr>";
            }
            
            $html .= '</tbody></table>';
            echo $html;
            break;

        case 'delete':
            $committee_id = $_POST['id'];
            $conn->query("DELETE FROM committees WHERE id = $committee_id");
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
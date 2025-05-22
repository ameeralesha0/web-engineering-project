<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Merit</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/mypetakom_logo.png" alt="Mypetakom Logo">
        </div>
        <h1>Claim Merit</h1>
        <button class="logout-button">Logout</button>
    </header>

    <main>
        <div class="form-container">
            <div class="form-group">
                <label for="claimID">Claim ID:</label>
                <input type="text" id="claimID" value="[AUTO_GENERATED]" readonly>
            </div>
            <div class="form-group">
                <label for="studentID">Student ID:</label>
                <input type="text" id="studentID" value="[USER_ID]" readonly>
            </div>
            <div class="form-group">
                <label for="eventID">Event ID:</label>
                <input type="text" id="eventID">
            </div>
            <div class="form-group">
                <label for="eventName">Event Name:</label>
                <input type="text" id="eventName">
            </div>
            <div class="form-group">
                <label for="eventLevel">Event Level:</label>
                <select id="eventLevel">
                    <option value="">Select Level</option>
                    <option value="school">School</option>
                    <option value="district">District</option>
                    <option value="state">State</option>
                    <option value="national">National</option>
                    <option value="international">International</option>
                </select>
            </div>
            <div class="form-group">
                <label for="position">Position:</label>
                <select id="position">
                    <option value="">Select Position</option>
                    <option value="champion">Champion</option>
                    <option value="first_runner_up">First Runner-Up</option>
                    <option value="participant">Participant</option>
                    </select>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date">
            </div>
            <div class="form-group">
                <label for="upload">Upload Document:</label>
                <input type="file" id="upload">
                <button type="button" class="upload-button">Upload</button>
                <div id="file-name"></div>
            </div>

            <div class="button-group">
                <button type="button" class="save-draft-button">Save Draft</button>
                <button type="button" class="submit-claim-button">Submit Claim</button>
                <button type="button" class="delete-button">Delete</button>
                <button type="button" class="back-button">Back</button>
            </div>

            <div class="status-area">
                <p>Status: <span id="claim-status"></span></p>
            </div>

            <div class="history-link">
                <a href="claim_history.html">Claim History Table</a>
            </div>
        </div>
    </main>

    <script src="js/script.js"></script>
</body>
</html>
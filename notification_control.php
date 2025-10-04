<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in AND is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

// Fetch all contact messages that are not soft-deleted
$messages = [];
$sql_messages = "SELECT * FROM contact_messages WHERE deleted_at IS NULL ORDER BY created_at DESC";
if ($result = mysqli_query($link, $sql_messages)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
}

// Fetch all testimonials that are not soft-deleted
$testimonials = [];
$sql_testimonials = "SELECT t.*, u.username FROM testimonials t JOIN users u ON t.user_id = u.user_id WHERE t.deleted_at IS NULL ORDER BY t.created_at DESC";
if ($result = mysqli_query($link, $sql_testimonials)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $testimonials[] = $row;
    }
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tavern Publico - Notification Control</title>
    <link rel="stylesheet" href="CSS/admin.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* --- STYLES FOR TABS --- */
        .tabs { overflow: hidden; border-bottom: 1px solid #dee2e6; margin-bottom: 25px; }
        .tab-link { background-color: #f8f9fa; border: 1px solid transparent; border-bottom: none; border-radius: 8px 8px 0 0; cursor: pointer; float: left; font-size: 16px; font-weight: 600; outline: none; padding: 12px 20px; margin-right: 5px; transition: background-color 0.3s ease, color 0.3s ease; color: #495057; }
        .tab-link:hover { background-color: #e9ecef; color: #007bff; }
        .tab-link.active { background-color: #ffffff; color: #007bff; border-color: #dee2e6 #dee2e6 #fff; position: relative; top: 1px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .truncate-text { display: block; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        #readMoreBody { white-space: pre-wrap; text-align: left; background-color: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 50vh; overflow-y: auto; line-height: 1.6; }
        
        .btn.view-full-text-btn { background-color: #007bff; color: white; }
        .btn.view-full-text-btn:hover { background-color: #0069d9; }
        .btn.reply-message-btn { background-color: #28a745; color: white; }
        .btn.reply-message-btn:hover { background-color: #218838; }
        .btn.delete-message-btn, .btn.delete-testimonial-btn { background-color: #dc3545; color: white; }
        .btn.delete-message-btn:hover, .btn.delete-testimonial-btn:hover { background-color: #c82333; }

        .btn.feature-btn[data-featured="1"] { background-color: #17a2b8; color: white; }
        .btn.feature-btn[data-featured="0"] { background-color: #6c757d; color: white; }

        /* Styles for the new alert/confirm modal */
        #alertModal .modal-content { max-width: 450px; text-align: center; }
        #alertModal h2 { margin-top: 0; }
        #alertModalActions { justify-content: center; }
    </style>
</head>
<body>

    <div class="page-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header"> <img src="Tavern.png" alt="Home Icon" class="home-icon"> </div>
            <nav>
                 <ul class="sidebar-menu">
                    <li class="menu-item"><a href="admin.php"><i class="material-icons">dashboard</i> Dashboard</a></li>
                     <li class="menu-item"><a href="update.php"><i class="material-icons">file_upload</i> Upload Management</a></li>
                    <li class="menu-item"><a href="reservation.php"><i class="material-icons">event_note</i> Reservation</a></li>
                </ul>
                <div class="user-management-title">User Management</div>
                <ul class="sidebar-menu user-management-menu">
                    <li class="menu-item active"><a href="notification_control.php"><i class="material-icons">notifications</i> Notification Control</a></li>
                    <li class="menu-item"><a href="table_management.php"><i class="material-icons">table_chart</i>Calendar Management</a></li>
                    <li class="menu-item"><a href="customer_database.php"><i class="material-icons">people</i> Customer Database</a></li>
                    <li class="menu-item"><a href="reports.php"><i class="material-icons">analytics</i>Reservation Reports</a></li>
                    <li class="menu-item"><a href="deletion_history.php"><i class="material-icons">history</i>Archive</a></li>
                    <li class="menu-item"><a href="logout.php"><i class="material-icons">logout</i> Log out</a></li>
                </ul>
            </nav>
        </aside>

        <div class="admin-content-area">
            <header class="main-header">
                <div class="header-content">
                    <div class="admin-header-right">
                         <div class="admin-user-info">
                            <span class="admin-username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <span class="admin-role">Admin</span>
                        </div>
                        <?php
                        $admin_avatar_path = isset($_SESSION['avatar']) && file_exists($_SESSION['avatar']) 
                                            ? htmlspecialchars($_SESSION['avatar']) 
                                            : 'images/default_avatar.png';
                        ?>
                        <img src="<?php echo $admin_avatar_path; ?>" alt="Admin Avatar" class="admin-avatar">
                    </div>
                </div>
            </header>

            <main class="dashboard-main-content">
                <div class="tabs">
                    <button class="tab-link active" onclick="openTab(event, 'messages')">Contact Messages</button>
                    <button class="tab-link" onclick="openTab(event, 'testimonials')">Guest Testimonials</button>
                </div>

                <div id="messages" class="tab-content">
                    <div class="reservation-page-header">
                        <h1>Contact Form Messages</h1>
                        <input type="text" id="messageSearch" class="search-input" placeholder="Search messages...">
                    </div>
                    <section class="all-reservations-section">
                        <div class="table-responsive">
                            <table id="messagesTable">
                                <thead>
                                    <tr>
                                        <th>CUSTOMER</th> <th>SUBJECT</th> <th>MESSAGE</th> <th>RECEIVED</th> <th>STATUS</th> <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($messages)): ?>
                                        <tr><td colspan="6" style="text-align: center;">No messages found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($messages as $message): ?>
                                            <tr data-id="<?php echo $message['id']; ?>" 
                                                data-email="<?php echo htmlspecialchars($message['email']); ?>"
                                                data-subject="<?php echo htmlspecialchars($message['subject']); ?>"
                                                data-messagebody="<?php echo htmlspecialchars($message['message']); ?>">
                                                <td>
                                                    <strong><?php echo htmlspecialchars($message['name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($message['email']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                                <td><span class="truncate-text" title="<?php echo htmlspecialchars($message['message']); ?>"><?php echo htmlspecialchars($message['message']); ?></span></td>
                                                <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                                                <td><span class="status-badge <?php echo !empty($message['replied_at']) ? 'confirmed' : 'pending'; ?>"><?php echo !empty($message['replied_at']) ? 'Replied' : 'New'; ?></span></td>
                                                <td class="actions">
                                                    <button class="btn btn-small view-full-text-btn">View</button>
                                                    <button class="btn btn-small reply-message-btn">Reply</button>
                                                    <button class="btn btn-small delete-message-btn">Delete</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <div id="testimonials" class="tab-content">
                    <div class="reservation-page-header">
                        <h1>Guest Testimonials</h1>
                        <input type="text" id="testimonialSearch" class="search-input" placeholder="Search testimonials...">
                    </div>
                    <section class="all-reservations-section">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>USERNAME</th> <th>RATING</th> <th>COMMENT</th> <th>FEATURED</th> <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody id="testimonialsTableBody">
                                    <?php if (empty($testimonials)): ?>
                                        <tr><td colspan="5" style="text-align: center;">No testimonials found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($testimonials as $testimonial): ?>
                                            <tr data-id="<?php echo $testimonial['id']; ?>" 
                                                data-comment="<?php echo htmlspecialchars($testimonial['comment'], ENT_QUOTES); ?>" 
                                                data-username="<?php echo htmlspecialchars($testimonial['username'], ENT_QUOTES); ?>">
                                                <td><?php echo htmlspecialchars($testimonial['username']); ?></td>
                                                <td><?php echo str_repeat('★', $testimonial['rating']) . str_repeat('☆', 5 - $testimonial['rating']); ?></td>
                                                <td><span class="truncate-text" title="<?php echo htmlspecialchars($testimonial['comment']); ?>"><?php echo htmlspecialchars($testimonial['comment']); ?></span></td>
                                                <td>
                                                    <button class="btn btn-small feature-btn" data-featured="<?php echo $testimonial['is_featured']; ?>">
                                                        <?php echo $testimonial['is_featured'] ? 'Yes' : 'No'; ?>
                                                    </button>
                                                </td>
                                                <td class="actions">
                                                    <button class="btn btn-small view-full-text-btn">View</button>
                                                    <button class="btn btn-small delete-testimonial-btn">Delete</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <div id="replyModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Reply to Message</h2>
            <form id="replyMessageForm">
                <input type="hidden" id="replyMessageId" name="message_id">
                <input type="hidden" id="replyCustomerEmail" name="customer_email">
                <div class="form-group"><label>Original Message:</label><div id="originalMessage" style="background-color: #f0f0f0; padding: 10px; border-radius: 5px; min-height: 80px;"></div></div>
                <div class="form-group"><label for="replyText">Your Reply:</label><textarea id="replyText" name="reply_text" rows="6" required></textarea></div>
                <div class="modal-actions"><button type="submit" class="btn modal-save-btn">Send Reply</button></div>
            </form>
        </div>
    </div>
    
    <div id="readMoreModal" class="modal">
        <div class="modal-content"><span class="close-button">&times;</span><h2 id="readMoreTitle">Full Text</h2><p id="readMoreBody"></p></div>
    </div>

    <div id="alertModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2 id="alertModalTitle" style="margin-top: 0;"></h2>
            <p id="alertModalMessage"></p>
            <div id="alertModalActions" class="modal-actions"></div>
        </div>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("tab-link");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.className += " active";
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('.tab-link.active').click();

        // --- NEW MODAL FUNCTIONS ---
        const alertModal = $('#alertModal');
        const alertModalTitle = $('#alertModalTitle');
        const alertModalMessage = $('#alertModalMessage');
        const alertModalActions = $('#alertModalActions');

        function showAlert(title, message, callback) {
            alertModalTitle.text(title);
            alertModalMessage.text(message);
            alertModalActions.html('<button class="btn" id="alertOkBtn">OK</button>');
            alertModal.css('display', 'flex');
            $('#alertOkBtn').on('click', function() {
                alertModal.css('display', 'none');
                if (callback) callback();
            });
        }

        function showConfirm(title, message, callback) {
            alertModalTitle.text(title);
            alertModalMessage.text(message);
            alertModalActions.html(
                '<button class="btn" id="confirmCancelBtn" style="background-color: #6c757d; color: white;">Cancel</button>' +
                '<button class="btn" id="confirmOkBtn" style="background-color: #dc3545; color: white;">Yes, Proceed</button>'
            );
            alertModal.css('display', 'flex');

            $('#confirmOkBtn').on('click', function() {
                alertModal.css('display', 'none');
                callback(true);
            });
            $('#confirmCancelBtn').on('click', function() {
                alertModal.css('display', 'none');
                callback(false);
            });
        }

        alertModal.on('click', '.close-button', function() {
            alertModal.css('display', 'none');
        });
        
        $(window).on('click', function(event) {
            if ($(event.target).is(alertModal)) {
                alertModal.css('display', 'none');
            }
        });

        $('#messageSearch').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#messagesTable tbody tr").filter(function() { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) });
        });

        $('#testimonialSearch').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#testimonialsTableBody tr").filter(function() { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) });
        });

        $('#testimonialsTableBody').on('click', '.feature-btn', function() {
            var btn = $(this);
            var testimonialId = btn.closest('tr').data('id');
            $.ajax({
                url: 'manage_testimonial.php',
                type: 'POST',
                data: { action: 'feature', testimonial_id: testimonialId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var isFeatured = btn.data('featured') == 1;
                        btn.data('featured', isFeatured ? 0 : 1);
                        btn.attr('data-featured', isFeatured ? 0 : 1);
                        btn.text(isFeatured ? 'No' : 'Yes');
                    } else {
                        showAlert('Error', response.message);
                    }
                }
            });
        });

        $('#testimonialsTableBody').on('click', '.delete-testimonial-btn', function() {
            var row = $(this).closest('tr');
            var testimonialId = row.data('id');
            showConfirm('Confirm Deletion', 'Are you sure you want to move this testimonial to the deletion history?', function(confirmed) {
                if (confirmed) {
                    $.ajax({
                        url: 'manage_testimonial.php',
                        type: 'POST',
                        data: { action: 'delete', testimonial_id: testimonialId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                row.fadeOut(400, function() { $(this).remove(); });
                            } else {
                                showAlert('Error', response.message);
                            }
                        }
                    });
                }
            });
        });
        
        const replyModal = document.getElementById('replyModal');
        const replyCloseBtn = replyModal.querySelector('.close-button');

        $('#messagesTable').on('click', '.reply-message-btn', function() {
            var row = $(this).closest('tr');
            $('#replyMessageId').val(row.data('id'));
            $('#replyCustomerEmail').val(row.data('email'));
            $('#originalMessage').text(row.data('messagebody'));
            replyModal.style.display = 'flex';
        });

        $('#messagesTable').on('click', '.delete-message-btn', function() {
            var row = $(this).closest('tr');
            var messageId = row.data('id');
            showConfirm('Confirm Deletion', 'Are you sure you want to move this message to the deletion history?', function(confirmed) {
                if (confirmed) {
                    $.ajax({
                        url: 'manage_message.php',
                        type: 'POST',
                        data: { action: 'delete', message_id: messageId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                               row.fadeOut(400, function() { $(this).remove(); });
                            } else {
                               showAlert('Error', response.message);
                            }
                        }
                    });
                }
            });
        });

        replyCloseBtn.onclick = function() { replyModal.style.display = 'none'; }

        $('#replyMessageForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize() + '&action=reply';
            $.ajax({
                url: 'manage_message.php', type: 'POST', data: formData, dataType: 'json',
                success: function(response) {
                    showAlert(response.success ? 'Success' : 'Error', response.message, function() {
                        if (response.success) {
                            replyModal.style.display = 'none';
                            location.reload();
                        }
                    });
                }
            });
        });

        const readMoreModal = document.getElementById('readMoreModal');
        const readMoreTitle = document.getElementById('readMoreTitle');
        const readMoreBody = document.getElementById('readMoreBody');
        const readMoreCloseBtn = readMoreModal.querySelector('.close-button');

        function openReadMoreModal(title, content) {
            readMoreTitle.textContent = title;
            readMoreBody.textContent = content;
            readMoreModal.style.display = 'flex';
        }

        readMoreCloseBtn.addEventListener('click', () => { readMoreModal.style.display = 'none'; });
        
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('view-full-text-btn')) {
                const row = e.target.closest('tr');
                if (row) {
                    let title = 'Full Message';
                    let content = '';
                    if (row.closest('#messagesTable')) {
                        const customerName = row.querySelector('strong').textContent;
                        title = `Message from ${customerName}`;
                        content = row.dataset.messagebody;
                    } else if (row.closest('#testimonialsTableBody')) {
                        title = `Comment from ${row.dataset.username}`;
                        content = row.dataset.comment;
                    }
                    openReadMoreModal(title, content);
                }
            }
        });

        window.onclick = function(event) {
            if (event.target == replyModal) { replyModal.style.display = 'none'; }
            if (event.target == readMoreModal) { readMoreModal.style.display = 'none'; }
        }
    });
</script>
</body>
</html>
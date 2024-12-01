<?php
use SimpleWire\Database;

$success = $wire->getState('form_success') ?? false;
$error = $wire->getState('form_error') ?? '';

try {
    $db = new Database();
    $messages = $db->getMessages();
} catch (RuntimeException $e) {
    $error = $e->getMessage();
    $messages = [];
}
?>

<div class="contact-form">
    <?php if ($success): ?>
        <div class="alert alert-success">
            Your message has been sent successfully!
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form wire:submit="submit-contact">
        <div class="form-group">
            <label for="name">Name:</label>
            <input
                type="text"
                id="name"
                name="name"
                wire:model="name"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input
                type="email"
                id="email"
                name="email"
                wire:model="email"
                required
            >
        </div>

        <div class="form-group">
            <label for="message">Message:</label>
            <textarea
                id="message"
                name="message"
                wire:model="message"
                required
            ></textarea>
        </div>

        <button type="submit">Send Message</button>
    </form>

    <?php if (!empty($messages)): ?>
    <div class="messages">
        <h3>Recent Messages</h3>
        <?php foreach ($messages as $msg): ?>
            <div class="message">
                <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                <em><?php echo htmlspecialchars($msg['email']); ?></em>
                <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                <small><?php echo $msg['created_at']; ?></small>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.contact-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 8px;
}

.alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
}

.messages {
    margin-top: 30px;
}

.message {
    border-bottom: 1px solid #eee;
    padding: 10px 0;
}

.message em {
    color: #666;
    margin-left: 10px;
}

.message small {
    display: block;
    color: #999;
}
</style>
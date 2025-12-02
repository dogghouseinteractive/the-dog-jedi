<?php
include 'main.php';
// Default event values
$event = [
    'title' => '',
    'description' => '',
    'color' => '#5373ae',
    'datestart' => date('Y-m-d\TH:i:s'),
    'dateend' => date('Y-m-d\TH:i:s'),
    'uid' => 1,
    'submit_date' => date('Y-m-d\TH:i:s')
];
if (isset($_GET['id'])) {
    // Retrieve the event from the database
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing event
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the event
        $stmt = $pdo->prepare('UPDATE events SET title = ?, description = ?, color = ?, datestart = ?, dateend = ?, uid = ?, submit_date = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['color'], date('Y-m-d H:i:s', strtotime($_POST['datestart'])), date('Y-m-d H:i:s', strtotime($_POST['dateend'])), $_POST['uid'], date('Y-m-d H:i:s', strtotime($_POST['submit_date'])), $_GET['id'] ]);
        header('Location: events.php');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the event
        $stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: events.php');
        exit;
    }
} else {
    // Create a new event
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO events (title,description,color,datestart,dateend,uid,submit_date) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['color'], date('Y-m-d H:i:s', strtotime($_POST['datestart'])), date('Y-m-d H:i:s', strtotime($_POST['dateend'])), $_POST['uid'], date('Y-m-d H:i:s', strtotime($_POST['submit_date'])) ]);
        header('Location: events.php');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Event', 'events')?>

<h2><?=$page?> Event</h2>

<div class="content-block">

    <form action="" method="post" class="form responsive-width-100">

        <label for="uid">Unique ID</label>
        <input id="uid" type="text" name="uid" placeholder="Unique Identifier" value="<?=$event['uid']?>" required>

        <label for="title">Title</label>
        <input id="title" type="text" name="title" placeholder="Title" value="<?=htmlspecialchars($event['title'], ENT_QUOTES)?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" placeholder="Description..."><?=htmlspecialchars($event['description'], ENT_QUOTES)?></textarea>

        <label for="color">Color</label>
        <input id="color" name="color" type="color" placeholder="Color" value="<?=$event['color']?>" list="presetColors" style="height:30px;width:60px;padding:5px;margin-top:10px">
        <datalist id="presetColors">
            <option>#5373ae</option>
            <option>#ae5353</option>
            <option>#9153ae</option>
            <option>#53ae6d</option>
            <option>#ae8653</option>
        </datalist>

        <label for="datestart">Date Start</label>
        <input id="datestart" type="datetime-local" name="datestart" placeholder="Date" value="<?=date('Y-m-d\TH:i:s', strtotime($event['datestart']))?>" required>

        <label for="dateend">Date End</label>
        <input id="dateend" type="datetime-local" name="dateend" placeholder="Date" value="<?=date('Y-m-d\TH:i:s', strtotime($event['dateend']))?>" required>

        <label for="submit_date">Date Submitted</label>
        <input id="submit_date" type="datetime-local" name="submit_date" placeholder="Date" value="<?=date('Y-m-d\TH:i:s', strtotime($event['submit_date']))?>" required>

        <div class="submit-btns">
            <input type="submit" name="submit" value="Submit">
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Delete" class="delete">
            <?php endif; ?>
        </div>

    </form>

</div>

<?=template_admin_footer()?>

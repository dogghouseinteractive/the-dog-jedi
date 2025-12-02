<?php
include 'main.php';
// Delete event
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: events.php');
    exit;
}
// SQL query that will retrieve all the events from the database ordered by the submit_date column
$stmt = $pdo->prepare('SELECT * FROM events ORDER BY submit_date DESC');
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?=template_admin_header('Events', 'events')?>

<h2>Events</h2>

<div class="links">
    <a href="event.php">Create Event</a>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>Title</td>
                    <td class="responsive-hidden">Description</td>
                    <td>Start Date</td>
                    <td>End Date</td>
                    <td>Unique ID</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no recent events</td>
                </tr>
                <?php else: ?>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><span style="color:<?=$event['color']?>" title="<?=$event['color']?>">&#9724;</span> <?=htmlspecialchars($event['title'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden"><?=nl2br(htmlspecialchars($event['description'], ENT_QUOTES))?></td>
                    <td><?=date('F j, Y H:ia', strtotime($event['datestart']))?></td>
                    <td><?=date('F j, Y H:ia', strtotime($event['dateend']))?></td>
                    <td><?=$event['uid']?></td>
                    <td>
                        <a href="event.php?id=<?=$event['id']?>">Edit</a>
                        <a href="events.php?delete=<?=$event['id']?>">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>

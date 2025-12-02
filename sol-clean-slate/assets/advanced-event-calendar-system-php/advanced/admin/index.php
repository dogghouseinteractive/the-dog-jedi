<?php
include 'main.php';
// Get new events
$stmt = $pdo->prepare('SELECT * FROM events ORDER BY submit_date DESC LIMIT 5');
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the current day events
$stmt = $pdo->prepare('SELECT * FROM events WHERE cast(datestart as DATE) <= cast(now() as DATE) AND cast(dateend as DATE) >= cast(now() as DATE)');
$stmt->execute();
$current_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the total number of upcoming events
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM events WHERE cast(dateend as DATE) > cast(now() as DATE)');
$stmt->execute();
$events_upcoming_total = $stmt->fetchColumn();
// Get the total number of events
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM events');
$stmt->execute();
$events_total = $stmt->fetchColumn();
// Get the total number of unique pages
$stmt = $pdo->prepare('SELECT COUNT(uid) AS total FROM events GROUP BY uid');
$stmt->execute();
$events_page_total = $stmt->fetchAll(PDO::FETCH_ASSOC);
$events_page_total = count($events_page_total);
?>
<?=template_admin_header('Dashboard', 'dashboard')?>

<h2>Dashboard</h2>

<div class="dashboard">
    <div class="content-block stat">
        <div>
            <h3>Current Events</h3>
            <p><?=number_format(count($current_events))?></p>
        </div>
        <i class="far fa-calendar-alt"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Upcoming Events</h3>
            <p><?=number_format($events_upcoming_total)?></p>
        </div>
        <i class="fas fa-clock"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Total Events</h3>
            <p><?=number_format($events_total)?></p>
        </div>
        <i class="far fa-calendar-alt"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Total Pages</h3>
            <p><?=number_format($events_page_total)?></p>
        </div>
        <i class="fas fa-file-alt"></i>
    </div>
</div>

<h2>Newly Listed Events</h2>

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

<h2 style="padding-top:35px">Current Events</h2>

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
                <?php if (empty($current_events)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no recent events</td>
                </tr>
                <?php else: ?>
                <?php foreach ($current_events as $event): ?>
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
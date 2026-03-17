<?php
session_start();
require_once "db_connect.php";

$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['login_time'])) {
    $elapsed_time = time() - $_SESSION['login_time'];
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.html?timeout=1");
        exit();
    }
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Update session time
$_SESSION['login_time'] = time();

// Fetch user info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $role);
$stmt->fetch();
$stmt->close();

$events_stmt = $conn->prepare("SELECT id, event_name, event_date, event_description, event_status FROM events WHERE event_status = 'open' ORDER BY event_date ASC LIMIT 5");
$events_stmt->execute();
$events_result = $events_stmt->get_result();
$events = [];
while ($row = $events_result->fetch_assoc()) {
    $events[] = $row;
}
$events_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="dashstyle.css" />
    <link rel="icon" href="icon2.png" />
    <title>Dashboard | BrainBin</title>
  </head>
  <body>
    <div class="container">
      <div id="navdiv">
        <header>
          <nav>
            <a href="index.html" class="acard">Go to Home</a>
            <a href="portfolio/admin.html" target="_blank" class="acard">Portfolio</a>
            <a href="contact.html" class="acard">Contact Us</a>
            <!-- Added logout button -->
            <a href="logout.php" class="acard" style="background-color: #d32f2f;">Logout</a>
            <?php if ($role === 'admin'): ?>
              <!-- Show admin panel link only for admins -->
              <a href="admin_dashboard.php" class="acard" style="background-color: #ff9800;">Admin Panel</a>
            <?php endif; ?>
          </nav>
        </header>
      </div>

      <main>
        <h1>
          Welcome, <?php echo htmlspecialchars($username); ?>! 
          <img src="icon1.png" />
        </h1>
        <p style="text-align:center; margin-bottom:20px;">
          Logged in with email: <strong><?php echo htmlspecialchars($email); ?></strong>
          <?php if ($role === 'admin'): ?>
            <span style="color: #ff9800; font-weight: bold;"> (Admin)</span>
          <?php endif; ?>
        </p>

        <!-- Added upcoming events section -->
        <?php if (count($events) > 0): ?>
        <section style="max-width: 900px; margin: 0 auto 40px; padding: 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          <h2 style="text-align: center; color: #0b3d91; margin-bottom: 20px;">Upcoming Events</h2>
          <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($events as $event): ?>
            <div style="padding: 15px; background: #f8faff; border-left: 4px solid #0b3d91; border-radius: 8px;">
              <h3 style="color: #0b3d91; margin-bottom: 5px;"><?php echo htmlspecialchars($event['event_name']); ?></h3>
              <p style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">
                <strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
              </p>
              <?php if ($event['event_description']): ?>
              <p style="color: #333; font-size: 0.9rem;"><?php echo htmlspecialchars($event['event_description']); ?></p>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php if ($role === 'admin'): ?>
          <div style="text-align: center; margin-top: 20px;">
            <a href="manage_events.php" style="display: inline-block; padding: 10px 20px; background: #0b3d91; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">Manage Events</a>
          </div>
          <?php endif; ?>
        </section>
        <?php endif; ?>

        <div class="grid-container">
          <!-- Added new card for collective materials page -->
          <a href="materials.php">
            <div class="card">
              <img
                src="https://cdn-icons-png.flaticon.com/128/3771/3771284.png"
                alt="All Materials"
              />
              <h3>All Study Materials</h3>
              <p>
                Access all semester materials in one place with organized folders
              </p>
            </div>
          </a>

          <a href="first.html">
            <div class="card">
              <img
                src="https://cdn-icons-png.flaticon.com/128/4161/4161801.png"
                alt="first"
              />
              <h3>1<sup>st</sup> Semester</h3>
              <p>
                Click the icon to view materials of the 1<sup>st</sup>
                semester.
              </p>
            </div>
          </a>

          <a href="second.html">
            <div class="card">
              <img
                src="https://cdn-icons-png.flaticon.com/128/4161/4161804.png"
                alt="Login"
              />
              <h3>2<sup>nd</sup> Semester</h3>
              <p>
                Click the icon to view materials of the 2<sup>nd</sup>
                semester.
              </p>
            </div>
          </a>

          <a href="third.html">
            <div class="card">
              <img
                src="https://cdn-icons-png.flaticon.com/128/4161/4161807.png"
                alt="About"
              />
              <h3>3<sup>rd</sup> Semester</h3>
              <p>
                Click the icon to view materials of the 3<sup>rd</sup>
                semester.
              </p>
            </div>
          </a>

          <a href="fourth.html">
            <div class="card">
              <img
                src="https://cdn-icons-png.flaticon.com/128/4161/4161809.png"
                alt="Contact"
              />
              <h3>4<sup>th</sup> Semester</h3>
              <p>
                Click the icon to view materials of the 4<sup>th</sup>
                semester.
              </p>
            </div>
          </a>

          <a href="fifth.html">
            <div class="card">
              <img
                src="https://cdn-icons-png.flaticon.com/128/2121/2121184.png"
                alt="Contact"
              />
              <h3>5<sup>th</sup> Semester</h3>
              <p>
                Click the icon to view materials of the 5<sup>th</sup>
                semester.
              </p>
            </div>
          </a>

          <a href="sixth.html">
            <div class="card">
              <img
                src="https://cdn-icons-png.flaticon.com/128/3285/3285596.png"
                alt="Portfolio"
              />
              <h3>6<sup>th</sup> Semester</h3>
              <p>
                Click the icon to view materials of the 6<sup>th</sup>
                semester.
              </p>
            </div>
          </a>
        </div>

        <!-- Add this inside <main>, after the semester cards -->
        <section class="faq-section">
          <h2>Frequently Asked Questions</h2>

          <div class="faq-item">
            <button class="faq-question">What is BrainBin?</button>
            <div class="faq-answer">
              <p>
                BrainBin is a centralized portal to access semester-wise
                learning resources and tools.
              </p>
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-question">
              How do I access different semester materials?
            </button>
            <div class="faq-answer">
              <p>
                Just click on the semester card in the dashboard to access
                relevant materials and notes.
              </p>
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-question">Who manages this portal?</button>
            <div class="faq-answer">
              <p>
                The BrainBin portal is maintained by the admin and
                contributors from the development team.
              </p>
            </div>
          </div>
        </section>

        <script>
          document.querySelectorAll(".faq-question").forEach((button) => {
            button.addEventListener("click", () => {
              button.classList.toggle("active");
              const answer = button.nextElementSibling;
              answer.style.maxHeight = answer.style.maxHeight
                ? null
                : answer.scrollHeight + "px";
            });
          });
        </script>
      </main>
    </div>
  </body>
</html>

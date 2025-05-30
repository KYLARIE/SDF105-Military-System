<?php
require_once '../config/db.php';
require_once '../config/auth.php';
include_once '../includes/header.php';
?>

<!-- Import Google Fonts -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Libre+Franklin:ital,wght@0,100..900;1,100..900&display=swap');
</style>

<!-- Slideshow Section -->
<section class="slideshow-container">
    <div class="slideshow">
        <div class="slide fade">
            <img src="../images/affairs.jpg" alt="Military Personnel" style="width:100%">
            <div class="slide-caption">
                <h2 class="jost-heading">Philippine Army, Veterans Federation of the Phil sign usufruct deal </h2>
                <p class="libre-franklin-text">Veterans' Federation of the Philippines (VFP) President retired Maj. Gen. Romeo D. Alamillo renders courtesy call on Army Chief Lt. Gen. Roy M. Galido prior to the signing of the usufruct deal at the Headquarters Philippine Army, Fort</p>
            </div>
        </div>

        <div class="slide fade">
            <img src="../images/soldiers.png" alt="Military Units" style="width:100%">
            <div class="slide-caption">
                <h2 class="jost-heading">Philippine Army, US troops simulate Air Assault in Northern Philippines </h2>
                <p class="libre-franklin-text">Philippine Army troops and their U.S. counterparts prepare for an airfield security on May 24, 2025 at Calayan Air strip as part of Exercise SALAKNIB Phase</p>
            </div>
        </div>

        <div class="slide fade">
            <img src="../images/vals.jpg" alt="Military Ranks" style="width:100%">
            <div class="slide-caption">
                <h2 class="jost-heading">Values</h2>
                <p class="libre-franklin-text">Philippine Army, Serving the People Securing the Land</p>
            </div>
        </div>

        <!-- Navigation dots -->
        <div class="slideshow-dots">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </div>
</section>

<!-- Quick Stats -->
<section class="quick-stats">
    <div class="stats-grid">
        <?php
        try {
            $people_count = $pdo->query("SELECT COUNT(*) FROM people")->fetchColumn();
        } catch (PDOException $e) {
            $people_count = 0;
        }
        
        try {
            $ranks_count = $pdo->query("SELECT COUNT(*) FROM ranks")->fetchColumn();
        } catch (PDOException $e) {
            $ranks_count = 0;
        }
        
        try {
            $units_count = $pdo->query("SELECT COUNT(*) FROM units")->fetchColumn();
        } catch (PDOException $e) {
            $units_count = 0;
        }
        ?>
        <div class="stat-card">
            <h3 class="jost-heading">Personnel</h3>
            <p class="stat-number"><?php echo $people_count; ?></p>
            <a href="../people/index.php" class="libre-franklin-link">View All <i class="fas fa-arrow-right"></i></a>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card">
            <h3 class="jost-heading">Ranks</h3>
            <p class="stat-number"><?php echo $ranks_count; ?></p>
            <a href="../ranks/index.php" class="libre-franklin-link">View All <i class="fas fa-arrow-right"></i></a>
            <div class="stat-icon">
                <i class="fas fa-medal"></i>
            </div>
        </div>
        <div class="stat-card">
            <h3 class="jost-heading">Units</h3>
            <p class="stat-number"><?php echo $units_count; ?></p>
            <a href="../units/index.php" class="libre-franklin-link">View All <i class="fas fa-arrow-right"></i></a>
            <div class="stat-icon">
                <i class="fas fa-sitemap"></i>
            </div>
        </div>
    </div>
</section>

<style>
/* Font Definitions */
.jost-heading {
  font-family: "Jost", sans-serif;
  font-optical-sizing: auto;
  font-weight: 600;
  font-style: normal;
}

.libre-franklin-text {
  font-family: "Libre Franklin", sans-serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
}

.libre-franklin-link {
  font-family: "Libre Franklin", sans-serif;
  font-optical-sizing: auto;
  font-weight: 500;
  font-style: normal;
}

.stat-number {
  font-family: "Jost", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
  font-size: 2.5rem;
  margin: 10px 0;
}

/* Slideshow styles */
.slideshow-container {
    width: 100%;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.slideshow {
    width: 100%;
    position: relative;
}

.slide {
    display: none;
    width: 100%;
    position: relative;
}

.slide.active {
    display: block;
}

.slide img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    display: block;
}

.slide-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 20px;
}

.slide-caption h2 {
    margin: 0 0 10px 0;
    font-size: 24px;
}

.slide-caption p {
    margin: 0;
    font-size: 16px;
}

/* Slideshow navigation */
.slideshow-dots {
    text-align: center;
    position: absolute;
    bottom: 20px;
    width: 100%;
}

.dot {
    height: 12px;
    width: 12px;
    margin: 0 5px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
    transition: background-color 0.6s ease;
    cursor: pointer;
}

.dot.active, .dot:hover {
    background-color: #fff;
}

/* Animation */
.fade {
    animation-name: fade;
    animation-duration: 1.5s;
}

@keyframes fade {
    from {opacity: .4} 
    to {opacity: 1}
}

/* Stat card styles */
.stat-card {
    position: relative;
    overflow: hidden;
    padding: 20px;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 2.5rem;
    opacity: 0.8;
    color: #1e4620; /* Match accent color */
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card h3 {
    margin-top: 0;
    color: #333;
}

.stat-card a {
    display: inline-block;
    margin-top: 10px;
    color: #1e4620; /* Match accent color */
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.stat-card a:hover {
    color: #2a582c; /* Slightly darker accent color on hover */
}
</style>

<script>
let slideIndex = 1;

// Initialize slideshow when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    showSlides(slideIndex);
    // Auto advance slides every 5 seconds
    setInterval(function() {
        plusSlides(1);
    }, 5000);
});

// Next/previous controls
function plusSlides(n) {
    showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
    showSlides(slideIndex = n);
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("slide");
    let dots = document.getElementsByClassName("dot");
    
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    
    // Hide all slides
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    
    // Remove active class from all dots
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    
    // Show current slide and activate corresponding dot
    slides[slideIndex-1].style.display = "block";
    dots[slideIndex-1].className += " active";
}
</script>

<?php include_once '../includes/footer.php'; ?>
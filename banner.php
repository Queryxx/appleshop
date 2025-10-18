<?php
include 'conn.php';
// Fetch banner content
$banner = $conn->query("SELECT * FROM banner LIMIT 1")->fetch_assoc();
?>

<!-- Hero Section (iPhone-style) -->
<header class="hero-iphone bg-cover bg-center h-screen fade-in" 
        style="background-image: url('./image/<?php echo htmlspecialchars($banner['background_image'] ?? 'logo.png'); ?>');">
    <div class="hero-overlay h-full flex items-center justify-center">
        <div class="hero-card text-center p-8 max-w-3xl mx-4">
            <h1 class="hero-title mb-4">
                <?php echo htmlspecialchars($banner['title'] ?? 'iPhone Shop'); ?>
            </h1>
            <p class="hero-subtitle mt-2 mb-6">
                <?php echo htmlspecialchars($banner['subtitle'] ?? 'Discover the latest iPhones and accessories'); ?>
            </p>
            <a href="<?php echo htmlspecialchars($banner['button_link'] ?? 'choose.php'); ?>" 
               class="hero-cta mt-6 inline-flex items-center px-6 py-3 rounded-full shadow-sm">
                 <i class="fas fa-shopping-cart mr-3">  </i>
                 <?php echo htmlspecialchars($banner['button_text'] ?? 'Shop Now'); ?>
            </a>
        </div>
    </div>
</header>

<style>
    /* System font stack, closer to iOS look */
    .hero-iphone, .hero-iphone * { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }

    .hero-overlay{ background: rgba(255,255,255,0.12); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }

    .hero-card{ background: rgba(255,255,255,0.9); border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); color: #111; }

    .hero-title{ font-size: 3rem; font-weight: 700; color: #111; }

    .hero-subtitle{ font-size: 1.125rem; color: #444; }

    .hero-cta{ background: #000; color: #fff; text-decoration: none; }
    .hero-cta:hover{ background: #111; }

    @media (max-width: 640px){
        .hero-title{ font-size: 2rem; }
        .hero-card{ padding: 1.25rem; }
    }
</style>